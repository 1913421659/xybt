<?php

namespace app\topic_h5\controller;
use think\Controller;
use app\common\VORsAjax;
use app\common\user\MUser;
use anywhere\FW;
use phpqrcode\QRcode;

class Lottery extends Controller{
	private $user = null;
	private $lottery_id = 1;//当前抽奖活动ID
	protected $times = 2;//总可抽次数
	
	public function __construct(){
		if(isset($_GET['parent_id'])){
			session('parent_id', intval($_GET['parent_id']));
			$_SESSION['parent_id'] = intval($_GET['parent_id']);
		}
		$this->user = MUser::getInstance()->getLogined();
		parent::__construct();
	}
	public function index(){
		//return $this->fetch();
	}
	
	
	/**
	 * 检查用户是否已登录
	 *
	 * @author darkcloud
	 */
	public function check(){
		$rs = new VORsAjax();
		try{
			if(!$this->user){
				throw new \Exception('您还没登录');
			}
			list($times,$total,$used) = $this->getUserDrawTimes();
			list($top,$total) = $this->getMyTop();
			$rs->data = [
				'user_id' => $this->user->user_id,
				'user_name' => $this->user->mobile_phone ? $this->user->mobile_phone : $this->user->user_name,
				'times' => $times,
				'used' => $used,
				'total' => $total,
				'my_top'=>$top,
			];
			$rs->status = 1;
		}catch(\Exception $e){
			$rs->errByException($e);
		}
		$rs->outputJSON();
		exit;
	}
	
	private function getMyTop(){
		$me = db('lottery_1_top')->where('user_id', $this->user->user_id)->find();
		$top = '-';
		$total = 0;
		if($me){
			$total = $me['total'];
			$top = db('lottery_1_top')->where('total','>',$total)
			->value('count(*)')+ db('lottery_1_top')->where('total','=',$total)
			->where('last_time','<',$me['last_time'])->value('count(*)');
			if($top !== false){
				$top += 1;
			}
		}
		return [$top,$total];
	}
	
	private function getTotal(){
		$total = db('lottery_1_top')->where('user_id', $this->user->user_id)
		->value('total');
		if(!$total){
			$total = 0;
		}
		return $total;
	}
	
	public function top(){
		$rs = new VORsAjax();
		try{
			$page_size= FW::_GET('page_size', 20);
			$page = FW::_GET('page', 1);
			$begin = $page_size * ($page - 1);
			$list = db('lottery_1_top')->order('total desc, last_time')
			->limit($begin, $page_size)->select();
			$user_ids = array_column($list,'user_id');
			$user_list = db('users')->whereIn('user_id', $user_ids)
			->column('user_name,mobile_phone','user_id');
			foreach($list as $k => $v){
				$name = '';
				if(isset($user_list[$v['user_id']])){
					$tmp = $user_list[$v['user_id']];
					$name = $tmp['user_name'];
					if($tmp['mobile_phone']){
						$name = $tmp['mobile_phone'];
					}
				}
				$list[$k]['name'] = $name == '' ? '匿名' : substr($name,0,3) . '*****' . substr($name,-3);
				unset($list[$k]['user_id']);
			}
			$rs->data = $list;
			$rs->status = 1;
		}catch(\Exception $e){
			$rs->errByException($e);
		}
		$rs->outputJSON();
	}
	
	/**
	 * ajax抽奖请求。
	 *
	 * @author darkcloud
	 */
	public function luck(){
		$rs = new VORsAjax();
		try{
			throw new \Exception('活动已结束，感谢关注。');
			if(!$this->user){
				throw new \Exception('您还没登录');
			}
			db()->startTrans();
			list($times,$total,$used) = $this->getUserDrawTimes();
			if($times <= 0){
				throw new \Exception('您的抽奖次数用完了。',1001);
			}
			$id = $this->run();
			$insert_rs = db('lottery_user_awards')->insert([
				'add_time'=>time(),
				'lottery_id'=>$this->lottery_id,
				'user_id'=>$this->user->user_id,
				'awards_id'=>$id
			]);
			if(!$insert_rs){
				throw new \Exception('抽奖失败，服务器繁忙（3）');
			}
			$user_awards_id = db()->getLastInsID();
			if(!db('lottery_awards')->where('id', $id)->setDec('inventory')){
				throw new \Exception('抽奖失败，服务器繁忙（4）');
			}
			$awards_name = db('lottery_awards')->where('id',$id)->value('name');
			if(!$awards_name){
				throw new \Exception('抽奖失败，服务器繁忙（3-1）');
			}
			//添加到用户奖励
			db('users')->where('user_id', $this->user->user_id)
			->setInc('reward_total', intval(floatval($awards_name) * 100))
			;//奖励
			db('users')->where('user_id', $this->user->user_id)
			->setInc('reward_total_all', intval(floatval($awards_name) * 100))
			;//累计奖励
			db('users')->where('user_id', $this->user->user_id)
			->setInc('yi_currency_total', intval(floatval($awards_name) * 100))
			;//用户蚁币总额
			//添加到“足迹”xybt_user_task_record
			db('user_task_record')->insert([
				'user_id'=>$this->user->user_id,
				'content'=>'红包抽奖活动获得'.$awards_name . '元',
				'add_time'=>time()
			]);
			//发放分红
			
			$rs->data = [
				'awards_id' => $id,
				'awards_name' => $awards_name,
// 				'prize_list' => $prize_list,
			];
			db()->commit();
			$rs->status = 1;
		}catch (\Exception $e){
			db()->rollback();
			$rs->errByException($e);
		}
		$rs->outputJSON();exit;
	}
	
	public function myPrize(){
		$rs = new VORsAjax();
		try{
			$rs->data = $this->getMyprize();
			$rs->status = 1;
		}catch (\Exception $e){
			db()->rollback();
			$rs->errByException($e);
		}
		$rs->outputJSON();
		exit;
	}
	
	private function getMyprize(){
		$list = db('lottery_user_awards')->alias('ua')
		->join('lottery_awards a','a.id = ua.awards_id','left')
		->where("ua.user_id = " . $this->user->user_id)
		->order('ua.id', 'desc')
		->select();
		;
		return $list;
	}
	
	/**
	 * 获奖名单
	 * @author darkcloud
	 */
	public function logs(){
		$rs = new VORsAjax();
		try{
			$limit = FW::_GET('limit', 10);
			$list = db('lottery_user_awards')->alias('t1')
			->join('lottery_awards t2','t2.id = t1.awards_id','left')
			->join('users t4','t4.user_id = t1.user_id','left')
			->field('t1.add_time,t2.name as awards_name, t4.nick_name,t4.mobile_phone')
			->order('t1.id','desc')->limit($limit)
			->select();
			$rs->data= [];
			if(!$rs){
				throw new \Exception('');
			}
			foreach($list as $v){
				$rs->data[] = [
					'time' => date('m-d AH:i', $v['add_time']),
					'user' => trim($v['nick_name']) == ''
					? substr($v['mobile_phone'],0,3) . '*****' . substr($v['mobile_phone'],-3)
					: trim($v['nick_name']),
					'prize'=>trim($v['awards_name']),
				];
			}
			$rs->status = 1;
		}catch (\Exception $e){
			$rs->errByException($e);
		}
		$rs->outputJSON();exit;
	}
	/**
	 * 获取当前用户可抽奖次数
	 * @return number
	 * @author darkcloud
	 */
	private function getUserDrawTimes(){
		$lottery = db('lottery')->where('id',$this->lottery_id)->find();
		$times = 0;
		//获取活动时间开始后注册的当前用户的下线数量。
		$times = db('users')->where('parent_id', $this->user->user_id)
		->where('reg_time','>=', strtotime($lottery['begin']))
		->value('count(*)');
		;
// 		$times = $this->db->getOne("
// 	select count(*)
// from " . $this->db->tbName('users') . "
// where 1
// and parent_id=" . $this->user_id . "
// and reg_time>=" . strtotime($this::$begin_time) . "
// ");
		//获取当前用户已抽奖次数
		$times2 = db('lottery_user_awards')
		->where('lottery_id', $this->lottery_id)
		->where('user_id', $this->user->user_id)
		->value('count(*)');
// 		$times2 = $this->db->getOne("
// select count(*)
// from " . $this->db->tbName('lottery_user_awards') . "
// where 1
// and user_id=" . $this->user_id . "
// ");
		//相减后+1得出可抽奖次数。
		return [$times - $times2 + $this->times,$times+1, $times2];
	}
	
	
	/**
	 * 计算抽奖（返回中奖奖项ID）
	 * @author darkcloud
	 */
	private function run(){
		//获取所有可以抽出的奖项
		$rs = db('lottery_awards')->field('id,inventory')
		->where('inventory','>','0')
		->where('lottery_id',$this->lottery_id)
		->select();
		if(!$rs){
			throw new \Exception('服务器繁忙（103）');
		}
		$total = array_sum(array_column($rs,'inventory')); //将所有奖项库存数相加，记为$total
		$rand = mt_rand(1,$total);//随机出1~$total之间的整数，记为$rand。
		foreach($rs as $k => $v){//遍历所有奖项
			if($rand <= $v['inventory']){ //$rand小于奖项库存，返回奖项ID。
				return $v['id'];
			}else{ //否则，$rand -= 奖项库存。
				$rand -= $v['inventory'];
			}
		}
		throw new \Exception('服务器繁忙（104）');
	}

	/**
	 * 获取海报
	 * 
	 * @author darkcloud
	 */
	public function poster(){
// 		var_dump($_SERVER);exit;
		if($this->user){
			$id = $this->user->user_id;
		}else{
			$id = FW::_GET('id');
		}
			$file_name = ROOT_PATH.'/public/topic_h5/lottery/images/haibao_bg.jpg';
			$p = imagecreatefromjpeg($file_name);
			$qr = new QRcode();
			$qr_file = tempnam("", "tmp");
// 			var_dump($qr_file);exit;
			$qr->png('http://'.$_SERVER['HTTP_HOST'].'/topic_h5/lottery/index.html#'.$id, $qr_file,1);
			$p2 = imagecreatefrompng($qr_file);
			$qr_size = getimagesize($qr_file);
			$x = 372;
			$y = 1353;
			$w = 337;
			$h = 337;
			imagecopyresized($p, $p2, $x, $y, 0, 0, $w, $h, $qr_size[0], $qr_size[1]);
			unlink($qr_file);
			ob_end_clean();
			header("Content-type: image/jpeg");
			imagejpeg($p);
			//
	}

	/**
	 * 下载页
	 * 
	 * @author darkcloud
	 */
	public function download(){
		if(strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone')||strpos($_SERVER['HTTP_USER_AGENT'], 'iPad')){
			return $this->fetch('description_ios');
		}else{
			return $this->fetch('description_android');
		}
	}
	public function debug(){
		var_dump(session('parent_id'));
		var_dump($_SESSION);
	}
}



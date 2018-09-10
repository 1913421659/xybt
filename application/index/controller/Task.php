<?php

namespace app\index\controller;


use app\common\model\MTaskCategory;
use app\common\VORsAjax;
use app\common\model\MTask;
use think\exception\DbException;
use app\common\model\MRegion;

class Task extends Common {
	public function create() {
		$user = $this->user;
		if(!$user){
			$this->redirect('login/index', ['from' => urlencode(request()->path())]);
		}
		$post = request()->post();
		if($post){
			$data = [
				'name'              => ker('name', $post),
//				'task_category_id' => ker('task_category_id', $post),
				'demand'            => ker('demand', $post),
				'demand_path'      => '',//文件上传
				'budget'            => floatval(ker('budget', $post)),
				'contact_phone'    => ker('contact_phone', $post),
				'contact_name'     => ker('contact_name', $post),
				'user_id'          => $user->user_id,
				'exam_type'        => ker('exam_type', $post),
				'add_time'         => time()
			];
			$id = db('demand')->insert($data, false, true);
			if($id){
				$area_list = ker('area_list', $post);
				$area_list = explode('-', $area_list);
				foreach ($area_list as $v){
					db('demand_area_about')->insert([
						'demand_id' => $id,
						'region_id' => $v
					]);
				}
				foreach($_POST['promotion_about'] as $v){
					db('demand_promotion_about')->insert([
						'demand_id' => $id,
						'promotion_id' => $v
					]);
				}
				$this->success('需求保存成功',url('user/index'));
			}else{
				$this->error('需求提交失败！');
			}
		}else{
			//获取地区
			$mod_region = new MRegion();
			$region_list = $mod_region->getTree ();
			$this->que_menu ();
			// 获取营销方式
			$promotion_list = db ( 'task_promotion' )->order("id")->select ();
			array2tree($promotion_list);
			//分类
			$category_list = db('task_category')->select();
			$data = [ 
				'region_list' => $region_list,
				'promotion_list' => $promotion_list,
				'category_list' => $category_list
			];
			$this->assign ( $data );
			return $this->fetch ('requirements');
		}
	}
	
	/**
	 * 以JS格式获取地区数据
	 * 
	 * @author 谭武云
	 *         @date 2017年9月20日
	 */
	public function getCityDataJs() {
		$list = db ( 'region' )->where ( 'region_type', '>', 0 )->where ( 'region_type', '<=', 2 )->order ( 'region_type' )->select ();
		$js = '
var __LocalDataCities = {
	list: {';
		foreach ( $list as $v ) {
			$js .= '"' . $v ['region_id'] . '":["' . $v ['region_name'] . '", ""],' . "\r\n";
		}
		// "010": ["北京", "BEIJING"],
		array2tree ( $list, 'region_id' );
		$js .= '
	},
	relations: {
		// 各省份
		';
		foreach ( $list as $p ) {
			$c_list = array_column ( $p ['children_list'], 'region_id' );
			$js .= '"' . $p ['region_id'] . '":["' . implode ( '","', $c_list ) . '"],' . "\r\n";
		}
		// "050": ["050020", "050030", "050040", "050050"],
		$p_list = array_column ( $list, 'region_id' );
		$js .= '
	},
	category: {
		"provinces" : ["' . implode ( '","', $p_list ) . '"],
		"gangaotai" : [],
	}
};
';
		die ( $js );
	}
	
	/**
	 * 获取任务分类JS
	 * 
	 * @author 谭武云
	 *         @date 2017年9月20日
	 */
	public function getCateDataJs() {
		$list = db ( 'task_category' )->order ( 'parent_id' )->select ();
		array2tree ( $list );
		$data = [ ];
		$data ['86'] = [ 
			'项目' => [ ] 
		];
		foreach ( $list as $v ) {
			$data ['86'] ['项目'] [] = [ 
				'code' => $v ['id'],
				'address' => $v ['category_name'] 
			];
			$data [$v ['id']] = [ ];
			foreach ( $v ['children_list'] as $s ) {
				$data [$v ['id']] [$s ['id']] = $s ['category_name'];
			}
		}
		$js = '(function (factory) {
    if (typeof define === \'function\' && define.amd) {
        // AMD. Register as anonymous module.
        define(\'ChineseDistricts\', [], factory);
    } else {
        // Browser globals.
        factory();
    }
})(function () {

    var ChineseDistricts = ';
		$js .= json_encode ( $data );
		$js .= ';

    if (typeof window !== \'undefined\') {
        window.ChineseDistricts = ChineseDistricts;
    }

    return ChineseDistricts;

});
';
		die ( $js );
	}
	
	public function test(){
	   $mod = new MTaskCategory();
	   \anywhere\FW::debug($mod);
	}
	
	
	/**
	 *
	 * @param unknown $task_info
	 * @param VOUser $user
	 * @throws \Exception
	 * @return boolean
	 */
	private function checkCanApplyTask($task_info, $user = null){
		if(!$user){
			$user = $this->user;
			if(!$user){
				throw new \Exception('用户错误', 8001);
			}
		}
		if($user->mobile_bind == 0){
			throw new \Exception('请先绑定手机', 8002);
		}
		if($task_info['advertiser_id'] == $user->user_id){
			throw new \Exception('不能领取自己发布的任务', 8003);
		}
		if($task_info['rank_id'] > 0 && $task_info['rank_id']>$user->user_rank){
			throw new \Exception('达人级别不够，不能领取', 8004);
		}
		return true;
	}
	
	/**
	 * 申请任务
	 * @author darkcloud.tan
	 */
	public function apply(){
		$rs = new VORsAjax();
		try{
			$post = request()->post();
			$task_id = intval($post['task_id']);
			$mod_task = new MTask();
			//获取任务详情信息
			$task = $mod_task->getInfo($task_id);
			$user = is_object($this->user)? $this->user->toArray() : null;
			if(!$user){
				$rs->data['url'] = url('login/index', ['from' => urlencode('index/marketing/info/key/' . $task_id)]);
				throw new \Exception('请先登录', 8000);
			}
			//检查用户是否允许接任务
			$this->checkCanApplyTask($task, $this->user);
			
			//是否已领取该任务
			$task_user_total = db('task_user')->where([
				'user_id'=>$user['user_id'],
				'task_id'=>$task['id'],
				'status'=>1
			])->field('count(*) total')->find();
			if($task_user_total['total'] > 0){
				throw new \Exception('你已领取过该任务', 9201);
			}
			
			//限定领取次数
			if(empty($task['qty'])){
				throw new \Exception('此任务已被领完', 9202);
			}
			
			//查询已领取次数
			$receive_number = db('task_user')->where([
				'task_id'=>$task['id'],
				'status' => ['in', [1,2,3]]
			])->field('sum(qty) as total')->find();
			if(!empty($receive_number) && $receive_number['total'] >= $task['qty']){
				throw new \Exception('此任务已被领完', 9203);
			}
			
			//每日限定领取数量
			if(!empty($task['qty_day_limit'])){
				$today_time_start = strtotime(date('Y-m-d'));
				$today_time_end = $today_time_start + 86400;
				$data = db('task_user')->field('sum(qty) total')
				->where('add_time',['>=', $today_time_start], ['<', $today_time_end])
				->where('task_id', $task['id'])
				->where('status', 'in', [1,2,3])
				->find();
				if($data['total']>= $task['qty_day_limit']){
					throw new \Exception('此任务今天已被领完', 9204);
				}
			}
			
			//会员限制领取次数
			if(!empty($task['share_limit'])){
				$data = db('task_user')
					->field('count(*) total')
					->where('task_id', $task_id)
					->where('user_id', $user['user_id'])
					->find();
				if($task['share_limit']<=$data['total']){
					throw new \Exception('任务领取次数已超出会员限制领取次数', 9205);
				}
			}
			
			switch ($task['parent_promotion_id']){
				case 1 :
				case 2 :
					throw new \Exception('模式错误', 9100);
					break;
				case 3 :
					$insert_data = [
						'task_id'     => $task_id,
						'user_id'     => $user['user_id'],
						'qty'         => 1,
						'status'      => 1,
						'add_time'  => time()
					];
					db('task_user')->insert($insert_data);
					if($task['share_type']==2){
						$rs->data = ['url' => shop_url($task['goods_id'])];
					}elseif($task['share_type']==1){
						$rs->data = ['url' => $task['share_url']];
					}
					break;
				case 4 :
				case 5 :
				case 6 :
					//获取一个申请
					$data = db('task_adv_apply')
					->where('task_id', $task_id)
					->where('user_id', $user['user_id'])
					->find();
					if($data){
						switch ($data['status']){
							case 2:
								throw new \Exception('你已申请该任务,申请未通过，不能再申请。', 9022);
								break;
							case 3:
								throw new \Exception('你已申请该任务,申请已被取消，不能再申请。', 9023);
								break;
							default:
								throw new \Exception('你已申请该任务', 9021);
								break;
						}
					}
					//任务申请个数
					$qty = !empty($post['qty']) ? trim($post['qty']) : 1;
					//联系人
					if(empty($post['contact_name'])){
						throw new \Exception('联系人不能为空', 9031);
					}
					$contact_name = trim($post['contact_name']);
					//联系人手机号
					if(empty($post['contact_phone'])){
						throw new \Exception('联系人手机号不能为空', 9032);
					}
					$contact_phone = trim($post['contact_phone']);
					if(!preg_match("/^1(3|4|5|7|8)\d{9}$/",$contact_phone)){
						throw new \Exception('请输入正确的联系人手机号', 9033);
					}
					//申请说明
					if(empty($post['content'])){
						throw new \Exception('申请说明不能为空', 9033);
					}
					$content = trim($post['content']);
					//插入数据
					$insert_data = [
						'task_id' 				=> $task_id,
						'parent_promotion_id' 	=> $task['parent_promotion_id'],
						'user_id' 				=> $user['user_id'],
						'qty' 					=> $qty,
						'contact_name'			=> $contact_name,
						'contact_phone' 		=> $contact_phone,
						'content' 				=> $content,
						'add_time' 				=> time()
					];
					db('task_adv_apply')->insert($insert_data);
					break;
				default :
					throw new \Exception('数据异常', 9000);
					break;
			}
			$rs->status = 1;
			$rs->msg = '申请成功';
		}catch(DbException $e){
			//这里数据库操作抛出异常
			$rs->err = '9999';
			$rs->msg = '内部错误';
			$rs->status = 0;
		}catch(\Exception $e){
			$rs->err = $e->getCode();
			$rs->msg = $e->getMessage();
			$rs->status = 0;
		}
		$rs->outputJSON();
	}
	
	/**
	 * Ajax获取前几条任务。
	 */
	public function ajaxGetTop(){
		$rs = VORsAjax::getInstance();
		try{
			$mod = new MTask();
			$parent_id = request()->post('parent_id','');
			$limit= request()->post('limit', 5);
			$list = $mod->getList([
				'parent_promotion_id'=>$parent_id,
				'status'=>1,
				'order'=>'t.is_recommend desc, t.sort_order, t.add_time desc ',
				'pagesize' => $limit
			]);
			if(!is_array($list)){
				throw new \Exception('数据获取失败', 9001);
			}
			array_walk($list, function(&$task){
				$task['banner_img'] = get_image_path($task['banner_img']);
			});
// 			\anywhere\FW::debug(db()->getLastSql());
			$rs->status = 1;
			$rs->data = $list;
		}catch (\Exception $e){
			$rs->msg = $e->getMessage();
			$rs->err = $e->getCode();
		}
		$rs->outputJSON();
	}
}

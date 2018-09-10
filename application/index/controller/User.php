<?php
namespace app\index\controller;


use app\common\model\MTask;
use think\Controller;
use think\Exception;
use think\Request;
use OSS\Core\OssException;
use app\common\VORsAjax;
use app\common\user\MUser;

class User extends Common{
	
	/**
	 * 性别
	 * @var array
	 */
	protected $sex_name_list = ['保密', '男', '女'];
	
	public function __construct(Request $request = null){
		parent::__construct($request);
		if(!$this->user){
			$this->redirect('login/index', ['from' => urlencode(request()->path())]);
		}
		$this->assign('sex_name_list', $this->sex_name_list);
	}
	
	
	/**
	 * 个人中心-首页
	 * @author 谭武云
	 * @date 2017年9月13日
	 * @return mixed|string
	 */
	public function index(){
		//我领取的任务
		$my_task_list = db('task_user')//->join('task', 'task.id=task_id')
		->where('user_id', $this->user->user_id)->limit(3)->select();
		$this->assign('my_task_list', $my_task_list);
		//我的订单
		//我的分享
		$my_share = db('task_share')->where('user_id', $this->user->user_id)->limit(5)->select();
		//我的收藏
		$my_collect = db('task_collect')->where('user_id', $this->user->user_id)->limit(5)->select();
		$this->assign('my_collect', $my_collect);
		
		//获取相关的任务数据
		$task_ids = array_merge(
						array_column($my_task_list, 'task_id'), 
						array_column($my_share, 'task_id'), 
						array_column($my_collect, 'task_id')
					);
		$task_list = db('task')->whereIn('id', $task_ids)->column('id,name,logo_img,banner_img,exam_time_type_id,reward_value,share_limit');
		$this->assign('task_list', $task_list);
		$type_id_list = array_column($task_list, 'exam_time_type_id');
		$exam_time_type_list = db('task_exam_time_type')->whereIn('id', $type_id_list)->column('id,name');
		$this->assign('exam_time_type_list', $exam_time_type_list);
		return $this->fetch();
	}

	/**
	 * 个人中心-负责商务
	 * @author 廖家益
	 * @date 2018年2月8日
	 * @return mixed|string
	 */
	public function business(){
		//用户ID
		$user_id = $this->user->user_id;
		//开始时间
		$statDate = input('start',date('Y-m-d',strtotime('-7day')));
		//结束时间
		$endDate = input('end',date('Y-m-d'));
		$pages = db('task_adv_packet_data')->alias('apd')
				->join('task_adv_packet ap', "apd.adv_packet_id = ap.id", 'left')
				->join('task_packet p', "ap.task_packet_id = p.id", 'left')
				->join('users u', "p.advertiser_id = u.user_id", 'left')
				->join('task t', "p.task_id = t.id", 'left')
				->where([
					't.business_user_id' => $user_id,
					'apd.data_lock'       => 1,
					'apd.add_time'        => [['>=',strtotime($statDate)],['<',strtotime($endDate)+86400]]
				])
				->field('t.name,t.init_price,t.cost_unit_price,p.advertiser_id,p.packet_number,ap.task_user_id,ap.channel_user_id,apd.add_time,apd.adv_qty,apd.checkout_qty,apd.status')
				->order('apd.add_time','desc')
				->paginate(15, null, ['page'=>input('page',1,'int')])->toArray();
		$list = $pages['data'];
		foreach($list as $key=>$val){
			//广告主
			$advertiser = db('users')->where('user_id', $val['advertiser_id'])->field('nick_name,company_nick_name')->find();
			$list[$key]['advertiser_name'] = empty($advertiser['company_nick_name'])?$advertiser['nick_name']:$advertiser['company_nick_name'];
			//渠道主
			$task_user_info = db('task_user')->where('id', $val['task_user_id'])->field('user_id')->find();
			$channel_info = db('users')->where('user_id', $task_user_info['user_id'])->field('nick_name')->find();
			$list[$key]['channel_name'] = $channel_info['nick_name'];
			//负责渠道
			$responsible_channel_info = db('users')->where('user_id', $val['channel_user_id'])->field('nick_name')->find();
			$list[$key]['responsible_channel_name'] = $responsible_channel_info['nick_name'];
			//收款金额
			$list[$key]['receipt_money'] = $val['init_price'] * $val['adv_qty'];
			//付款金额
			$list[$key]['pay_money'] = $val['cost_unit_price'] * $val['checkout_qty'];
			//利润
			$list[$key]['profit_money'] = $list[$key]['receipt_money'] - $list[$key]['pay_money'];
			//状态
			if($val['status'] == 0){
				$list[$key]['status'] = '未收款';
			}elseif($val['status'] == 1){
				$list[$key]['status'] = '已收款，待申请结算';
			}elseif($val['status'] == 2){
				$list[$key]['status'] = '已申请结算，待审核';
			}elseif($val['status'] == 3){
				$list[$key]['status'] = '已结算';
			}elseif($val['status'] == 4){
				$list[$key]['status'] = '取消并停止结算';
			}
		}
		unset($pages['data']);
		$this->assign([
			'start'          => $statDate,
			'end'            => $endDate,
			'business_list' => $list,
			'pages'          => $pages,
			'page_url_pre'  => preg_replace("/\\.html$/i",'',Url('')) . '/page/',
		]);
		return $this->fetch();
	}

	/**
	 * 个人中心-负责渠道
	 * @author 廖家益
	 * @date 2018年2月8日
	 * @return mixed|string
	 */
	public function channel(){
		//用户ID
		$user_id = $this->user->user_id;
		//开始时间
		$statDate = input('start',date('Y-m-d',strtotime('-7day')));
		//结束时间
		$endDate = input('end',date('Y-m-d'));
		$pages = db('task_adv_packet_data')->alias('apd')
				->join('task_adv_packet ap', "apd.adv_packet_id = ap.id", 'left')
				->join('task_packet p', "ap.task_packet_id = p.id", 'left')
				->join('users u', "p.advertiser_id = u.user_id", 'left')
				->join('task t', "p.task_id = t.id", 'left')
				->where([
					'ap.channel_user_id' => $user_id,
					'apd.data_lock'       => 1,
					'apd.add_time'        => [['>=',strtotime($statDate)],['<',strtotime($endDate)+86400]]
				])
				->field('t.name,t.init_price,t.cost_unit_price,t.business_user_id,p.advertiser_id,p.packet_number,ap.task_user_id,apd.add_time,apd.adv_qty,apd.checkout_qty,apd.status')
				->order('apd.add_time','desc')
				->paginate(15, null, ['page'=>input('page',1,'int')])->toArray();
		$list = $pages['data'];
		foreach($list as $key=>$val){
			//广告主
			$advertiser = db('users')->where('user_id', $val['advertiser_id'])->field('nick_name,company_nick_name')->find();
			$list[$key]['advertiser_name'] = empty($advertiser['company_nick_name'])?$advertiser['nick_name']:$advertiser['company_nick_name'];
			//渠道主
			$task_user_info = db('task_user')->where('id', $val['task_user_id'])->field('user_id')->find();
			$channel_info = db('users')->where('user_id', $task_user_info['user_id'])->field('nick_name')->find();
			$list[$key]['channel_name'] = $channel_info['nick_name'];
			//负责商务
			$responsible_business_info = db('users')->where('user_id', $val['business_user_id'])->field('nick_name')->find();
			$list[$key]['responsible_business_name'] = $responsible_business_info['nick_name'];
			//收款金额
			$list[$key]['receipt_money'] = $val['init_price'] * $val['adv_qty'];
			//付款金额
			$list[$key]['pay_money'] = $val['cost_unit_price'] * $val['checkout_qty'];
			//利润
			$list[$key]['profit_money'] = $list[$key]['receipt_money'] - $list[$key]['pay_money'];
			//状态
			if($val['status'] == 0){
				$list[$key]['status'] = '未收款';
			}elseif($val['status'] == 1){
				$list[$key]['status'] = '已收款，待申请结算';
			}elseif($val['status'] == 2){
				$list[$key]['status'] = '已申请结算，待审核';
			}elseif($val['status'] == 3){
				$list[$key]['status'] = '已结算';
			}elseif($val['status'] == 4){
				$list[$key]['status'] = '取消并停止结算';
			}
		}
		unset($pages['data']);
		$this->assign([
			'start'         => $statDate,
			'end'           => $endDate,
			'channel_list' => $list,
			'pages'         => $pages,
			'page_url_pre' => preg_replace("/\\.html$/i",'',Url('')) . '/page/',
		]);
		return $this->fetch();
	}
	
	/**
	 * 个人中心-我的需求
	 * @author 谭武云
	 * @date 2017年9月13日
	 * @return mixed|string
	 */
	public function myNeed(){
		$t_id = input('key', 0, 'int');
		//顶级营销方式
		$top_prom_list = db('task_promotion')->where('parent_id', 0)->order('sort_order')
		->column('id,promotion_name,sort_order');
		if(isset($top_prom_list[$t_id])){
			$this_prom = $top_prom_list[$t_id];
		}else{
			$this_prom = current($top_prom_list);
		}
		//获取需求
		$pages = db('demand')->alias('d')
		->join('task t', "t.demand_id = d.id", 'left')
		->join('task_promotion tp', "tp.id=t.parent_promotion_id", 'left')
		->join('task_exam_time_type tett', "tett.id=t.exam_time_type_id", 'left')
		->where([
			'd.user_id'=> $this->user->user_id,
			't.parent_promotion_id'=>$this_prom['id']
		])
		->field('d.*,tp.promotion_name, t.demand_id,t.id as task_id,t.name as task_name,t.logo_img,t.brought_total,t.qty,t.cost_price,t.cost_unit_price,t.exam_time_type_id, tett.name as exam_time_type_name')
		->order('add_time', 'desc')
		->paginate(10, null, ['page'=>input('page',1,'int')])->toArray();
		;
		$demand_list = $pages['data'];
		unset($pages['data']);
		$this->assign([
			'this_prom' => $this_prom,
			'top_pro_list' => $top_prom_list,
			'demand_list' => $demand_list,
			'pages' => $pages,
			'page_url_pre' => preg_replace("/\\.html$/i",'',Url('')) . '/page/',
		]);
		return $this->fetch();
	}
	
	/**
	 * 我的任务
	 * @author 谭武云
	 * @date 2017年9月15日
	 * @return mixed|string
	 */
	public function myTask(){
		$t_id = input('key', 0, 'int');//顶级营销方式
		$top_prom_list = db('task_promotion')->where('parent_id', 0)->order('sort_order')
		->column('id,promotion_name,sort_order');
		if(isset($top_prom_list[$t_id])){
			$this_prom = $top_prom_list[$t_id];
		}else{
			$this_prom = current($top_prom_list);
		}
		$this->assign('this_prom', $this_prom);
		$this->assign('top_pro_list', $top_prom_list);
		
		//收藏的任务列表
		$list = db("task_user")->alias('tu')->join("task t", "t.id=tu.task_id", 'left')
		->join("task_adv_packet tap", "tap.task_user_id = tu.id", 'left')
		->field('tu.*,'
				. 't.id,t.name,t.logo_img,t.parent_promotion_id,t.promotion_id,t.task_category_id,t.cost_price,'
				. 'tap.id as tap_id,tap.status as tap_status, tap.task_packet_id')
		->where([
			"tu.user_id"=>$this->user->user_id,
			"t.parent_promotion_id"=>$this_prom['id'],
			't.status'=>1
		])
		->group('tu.id')
		->select();
		$this->assign('list', $list);
		$this->assign('status_names',[
			'','已领取','完成','已返利','已取消'
		]);
		$this->getPromShortName();
		return $this->fetch();
	}
	
	/**
	 * 营销、分销、广告的详情页
	 * @author 谭武云
	 * @date 2017年9月21日
	 */
	public function advinfo(){
		$tap_id= input('key', 0, 'int');
		$t_id = input('t_id',0, 'int');
		$task_adv_packet= db('task_adv_packet')->where('id', $tap_id)->find();
		if(!$task_adv_packet){
			$this->error('参数错误！');
		}
		$task_user = db('task_user')->where('id', $task_adv_packet['task_user_id'])->find();
		if(!$task_user){
			$this->error('数据错误：不存在的task_id:' . $task_adv_packet['task_user_id']);
		}
		$task = db('task')->where('id', $task_user['task_id'])->find();
		if(!$task){
			$this->error('数据错误：不存在的task_id:' . $task_user['task_id']);
		}
		/*	$field = 'apd.add_time,tp.packet_number,t.cost_unit_price,apd.checkout_qty,apd.checkout_money';
		$leftJoin = ' LEFT JOIN '.$task->tbName('task_adv_packet').' as ap ON apd.adv_packet_id = ap.id'.
					' LEFT JOIN '.$task->tbName('task_packet').' as tp ON ap.task_packet_id = tp.id'.
					' LEFT JOIN '.$task->tbName('task_user').' as tu ON ap.task_user_id = tu.id'.
					' LEFT JOIN '.$task->tbName('task').' as t ON tu.task_id = t.id';
		//判断是否为企业*/
		$list = db('task_adv_packet_data')->where('adv_packet_id', $tap_id)->order('add_time', 'desc')->select();
		$this->assign([
			'list' => $list,
			't_id' => $t_id,
			'task_adv_packet' => $task_adv_packet,
			'task_user' => $task_user,
			'task' => $task
		]);
		$this->getPromShortName();
		return $this->fetch();
	}
	
	/**
	 * 申请结算
	 * @author 谭武云
	 * @date 2017年9月26日
	 */
	public function checkout(){
		$t_id = input('t_id',0, 'int');
		$this->assign('t_id', $t_id);
		$where = [
			'tu.user_id' => $this->user->user_id,
			'ap.status' => 1,
			'apd.data_lock' => 1,
			'apd.status' => 1,
			't.parent_promotion_id' => $t_id
		];
		$money = db('task_adv_packet_data')->alias('apd')
		->join('task_adv_packet ap', 'apd.adv_packet_id = ap.id')
		->join('task_user tu', 'ap.task_user_id = tu.id')
		->join('task t', 'tu.task_id = t.id')
		->where($where)->value('sum(apd.checkout_money) as checkout_money');
		;
		$data['checkout_money'] = number_format(floatval($money), 2);
		$data['is_apply'] = 1;
		
		//结算列表
		$name = input('name', '');
		if($name != ''){
			$where['t.name'] = $name;
		}
		$field = 'ap.id,t.name,sum(apd.checkout_qty) as checkout_qty,sum(apd.checkout_money) as checkout_money';
		$data['list'] = db('task_adv_packet_data')->alias('apd')
		->join('task_adv_packet ap', 'apd.adv_packet_id = ap.id')
		->join('task_user tu', 'ap.task_user_id = tu.id')
		->join('task t', 'tu.task_id = t.id')
		->where($where)->field($field)->group('apd.adv_packet_id')
		->select();
		$this->assign($data);
		$this->getPromShortName();
		return $this->fetch();
	}
	
	/**
	 * 6个顶级营销方式的缩短名称
	 * @author 谭武云
	 * @date 2017年9月15日
	 * @return string[]
	 */
	private function getPromShortName(){
		$short = [
			'1'=>'福利',
			'2'=>'体验',
			'3'=>'活动',
			'4'=>'营销',
			'5'=>'分销',
			'6'=>'广告',
		];
		$this->assign('prom_short_name', $short);
		return $short;
	}
	
	/**
	 * 我的收藏
	 * @author 谭武云
	 * @date 2017年9月15日
	 * @return mixed|string
	 */
	public function myCollect(){
		$t_id = input('key', 0, 'int');//顶级营销方式
		$top_prom_list = db('task_promotion')->where('parent_id', 0)->order('sort_order')
			->column('id,promotion_name,sort_order');
		if(isset($top_prom_list[$t_id])){
			$this_prom = $top_prom_list[$t_id];
		}else{
			$this_prom = current($top_prom_list);
		}
		$this->assign('this_prom', $this_prom);
		$this->assign('top_pro_list', $top_prom_list);
		
		//收藏的任务列表
		$list = db("task_collect")->alias('tu')->join("task t", "t.id=tu.task_id")
			->field('tu.*,t.id,t.name,t.logo_img,t.parent_promotion_id,t.promotion_id,t.task_category_id')
			->where([
				"tu.user_id"=>$this->user->user_id
				,"t.parent_promotion_id"=>$this_prom['id']
				,'t.status'=>1
			])->select();
		$this->assign('list', $list);
		
		$this->getPromShortName();
		return $this->fetch();
		
	}
	
	/**
	 * 我的分享
	 * @author 谭武云
	 * @date 2017年9月15日
	 */
	public function myShare(){
		$t_id = input('key', 0, 'int');//顶级营销方式
		$top_prom_list = db('task_promotion')->where('parent_id', 0)->order('sort_order')
		->column('id,promotion_name,sort_order');
		if(isset($top_prom_list[$t_id])){
			$this_prom = $top_prom_list[$t_id];
		}else{
			$this_prom = current($top_prom_list);
		}
		$this->assign('this_prom', $this_prom);
		$this->assign('top_pro_list', $top_prom_list);
		
		//收藏的任务列表
		$list = db("task_share")->alias('ts')->join("task t", "t.id=ts.task_id")
		->field('ts.*,t.id,t.name,t.logo_img,t.parent_promotion_id,t.promotion_id,t.task_category_id')
		->where([
			"ts.user_id"=>$this->user->user_id
			,"t.parent_promotion_id"=>$this_prom['id']
			,'t.status'=>1
		])->select();
		$this->assign('list', $list);
		
		$this->getPromShortName();
		return $this->fetch();
	}
	
	/**
	 * 我的钱包
	 * @author 谭武云
	 * @date 2017年9月19日
	 * @return mixed|string
	 */
	public function wallet(){
		if(request()->isPost()){
			$page_size = 6;
			$post = request()->post();
			$page = ker('page', $post, 1);
			$limit = $page_size * ($page-1);
			$where = [
				'process_type'=>1,
				'user_id' =>$this->user->user_id
			];
			$data = db('user_account')->where($where)->order('id', 'desc')->limit($limit, $page_size)->select();
			if(is_array($data) && count($data) > 0){
				$this->returnJson($data, 1);
			}else{
				$this->returnJson('', 0);
			}
		}else{
			return $this->fetch();
		}
	}
	
	
	/**
	 * 我的足迹
	 * @author 谭武云
	 * @date 2017年9月19日
	 * @return mixed|string
	 */
	public function footprints(){
		//获取我的足迹(20条)
        $statDate = input('start',date('Y-m-d',strtotime('-3day')));//默认三天前的足迹
        $endDate = input('end',date('Y-m-d'));
		$list = db('user_task_record')->where('user_id', $this->user->user_id)->where('add_time','between',[strtotime($statDate),strtotime($endDate)])->order('id', 'desc')->select();
		$data = '';
		foreach($list as $v){
			$date = date("Y-m-d", $v['add_time']);
			$v['time'] = date("H:i:s", $v['add_time']);
			$data[$date][] = $v;
		}
		$this->assign([
            'list'=>$data,
            'start'=>$statDate,
            'end'=>$endDate
        ]);
		return $this->fetch();
	}
	
	/**
	 * 绑定银行卡
	 * @author 谭武云
	 * @date 2017年9月21日
	 */
	public function bindcard(){
		if(request()->isPost()){
			$post = request()->post();
			$bank_user = ker('bank_user', $post, '');
			$bank_name = ker('bank_name', $post, '');
			$bank_number = ker('bank_number', $post, '');
			if (empty($bank_user)) {
				$this->error('银行户名为空');
			} else {
				if (!preg_match("/^[a-zA-Z\x{4e00}-\x{9fa5}]{2,}$/u", $bank_user)) {
					$this->error( '请输入正确的银行户名');
				}
			}
			if (empty($bank_name)) {
				$this->error('开户行名称为空');
			} else {
				if (!preg_match("/^[a-zA-Z\x{4e00}-\x{9fa5}]{4,}$/u", $bank_name)) {
					$this->error( '请输入正确的开户行名称');
				}
			}
			if (empty($bank_number)) {
				$this->error('银行卡号为空');
			} else {
				if (!preg_match("/^[0-9]{12,19}$/", $bank_number)) {
					$this->error( '请输入正确的银行卡号');
				}
			}
			$rs = db('users')->where('user_id', $this->user->user_id)
				->update([
					'bank_user' => $bank_user,
					'bank_name' => $bank_name,
					'bank_number' => $bank_number,
					'bank_bind' => 1
				]);
			if($rs){
				$this->user->bank_user = $bank_user;
				$this->user->bank_name = $bank_name;
				$this->user->bank_number = $bank_number;
				$this->user->bank_bind = 1;
				session('user',$this->user);
				$this->success('绑定成功',url('user/wallet'));
			}else{
				$this->error('绑定失败');
			}
		}else{
			return $this->fetch();
		}
	}
	
	/**
	 * 绑定微信
	 * @author 谭武云
	 * @date 2017年9月27日
	 * @return mixed|string
	 */
	public function bindwx(){
		$post = request()->post();
		if($post){
			if($this->user->wechat_bind == 1){
				$this->error('已绑定过，请勿重复提交');
			}
			$wechat_name = ker('wechat_name', $post, '');
			if(!$wechat_name){
				$this->error('参数错误!');
			}
			if (!preg_match("/^[a-zA-Z0-9\.\@_-]{6,}$/", $wechat_name)) {
				$this->error('请输入正确的微信号');
			}
			$rs = db('users')->where('user_id', $this->user->user_id)
			->update([
				'wechat_name' => $wechat_name,
				'wechat_bind' => 1
			]);
			if($rs){
				$this->user->wechat_name = $wechat_name;
				$this->user->wechat_bind = 1;
				session('user',$this->user);
				$this->success('绑定成功',url('user/wallet'));
			}else{
				$this->error('绑定失败');
			}
		}else{
			return $this->fetch();
		}
	}
	
	/**
	 * 绑定支付宝账号
	 * @author 谭武云
	 * @date 2017年9月27日
	 * @return mixed|string
	 */
	public function bindali(){
		$post = request()->post();
		if($post){
			if($this->user->alipay_bind == 1){
				$this->error('已绑定过，请勿重复提交');
			}
			$alipay_name= ker('alipay_name', $post, '');
			if(!$alipay_name){
				$this->error('参数错误!');
			}
			if (!preg_match("/^[a-zA-Z0-9-_\.\@]{5,}$/", $alipay_name)) {
				$this->error('请输入正确的支付宝账户');
			}
			$rs = db('users')->where('user_id', $this->user->user_id)
			->update([
				'alipay_name' => $alipay_name,
				'alipay_bind' => 1
			]);
			if($rs){
				$this->user->alipay_name = $alipay_name;
				$this->user->alipay_bind = 1;
				session('user',$this->user);
				$this->success('绑定成功',url('user/wallet'));
			}else{
				$this->error('绑定失败');
			}
			
		}else{
			return $this->fetch();
		}
	}
	
	/**
	 * 提现
	 * @author 谭武云
	 * @date 2017年9月27日
	 */
	public function withdraw(){
		$post = request()->post();
		if($post){
			$rs = new VORsAjax();
			try{
				$payment = ker('payment', $post);
				if(!$payment){
					throw new \Exception('参数错误！', 9001);
// 					$this->error('参数错误！');
				}
				$money = ker('money', $post);
				if(!$money){
					throw new \Exception('金额错误！', 9002);
// 					$this->error('金额错误！');
				}
				$user_note = ker('user_note', $post, '');
				//如果有提现未审核金额，则减掉。
				$apply_money = db('user_account')->where([
					'user_id' => $this->user->user_id,
					'process_type'=>1,
					'is_paid'=>0,
					'status' => ['<', 2]
				])->value('sum(abs(amount))');
				$user_money = yibi2money($this->user->yi_currency_total);
				if($apply_money>0){
					$user_money -= $apply_money;
				}
				if($money< 10){
					$n = db('user_account')->where([
						'user_id' => $this->user->user_id,
						'process_type'=>1,
						'status' => ['<', 2]
					])
					->value('count(*)');
					if($n>0){
						throw new \Exception('提现金额小于10元',9003);
					}elseif($money< 5){
						throw new \Exception('提现金额小于5元', 9004);
					}
				}
				if($money > $user_money){
					throw new \Exception('提现金额大于可提现金额', 9000);
// 					$this->error('提现金额大于可提现金额');
				}
				$insert_data = [
					'user_id'      => $this->user->user_id,
					'admin_user'  => 'admin',
					'amount'       => -($money),
					'admin_note'  => '',
					'user_note'   => $user_note,
					'add_time'     => time(),
					'process_type' => 1,
					'payment'      => $payment,
					'is_paid'      => 0
				];
				if(db('user_account')->insert($insert_data)){
					$rs->status = 1;
					$rs->msg = '提现成功';
					$rs->data = [
						'url' => url('user/log_withdraw')
					];
					//$this->success('提现成功', url('user/log_withdraw'));
				}else{
					throw new \Exception('操作失败', 9000);
// 					$this->error('操作失败');
				}
			}catch(\Exception $e){
				$rs->err = $e->getCode();
				$rs->msg = $e->getMessage();
			}
			$rs->outputJSON();
		}else{
			return $this->fetch();
		}
	}
	
	public function log_withdraw(){
		
	}
	
	/**
	 * 个人信息
	 */
	public function information(){
		$post = request()->post();
		if($post){
			$rs = new VORsAjax();
			$code = 0;
			$msg = '';
			try{
				$data = [];
				if(isset($post['nick_name'])){
					if($post['nick_name'] == ''){
						throw new \Exception('昵称不能为空', 9001);
					}
					$data['nick_name'] = $post['nick_name'];
				}
				if(isset($post['sex'])){
					$post['sex'] = intval($post['sex']);
					if($post['sex']<=0 || $post['sex'] >2){
						throw new \Exception('性别数据错误', 9002);
					}
					$data['sex'] = $post['sex'];
				}
				if(isset($post['qq'])){
					if(!preg_match("/[0-9]{5,12}/", $post['qq'])){
						throw new \Exception('请输入正确的QQ号', 9003);
					}
					$data['qq'] = $post['qq'];
				}
				if(isset($post['profession_id'])){
					$post['profession_id'] = intval($post['profession_id']);
					$data['profession_id'] = $post['profession_id'];
				}
				if(isset($post['year']) && isset($post['month']) && isset($post['day'])){
					$data['birthday'] = sprintf("%04d", $post['year']) . '-' . sprintf("%02d", $post['month']) . '-' . sprintf("%02d", $post['day']);
				}
				if(empty($data)){
					throw new \Exception('无可更新数据', 9004);
				}
				try{
					db('users')->where('user_id', $this->user->user_id)->update($data);
				}catch(\Exception $e){
					throw new \Exception('资料更新失败！', 9005);
				}
				foreach($data as $k => $v){
					$this->user->$k = $v;
				}
				$mod_user = MUser::getInstance();
				$mod_user->setLogined($this->user);
				$rs->status = 1;
			}catch(\Exception $e){
				$rs->msg = $e->getMessage();
				$rs->err = $e->getCode();
			}
			$rs->outputJSON();
		}else{
			$user = db('users')->where('user_id', $this->user->user_id)->find();
			if(!$user){
				$user = $this->user;
			}
			$this->assign('user', $user);
			//职业
			$profession_list = db('user_profession')->order('sort_order')->column('name', 'id');
			$this->assign('profession_list', $profession_list);
			return $this->fetch();
		}
	}
	/**
	 * 上传头像
	 */
	public function uploadhead(){
		// 获取表单上传文件 例如上传了001.jpg
		try{
			$file = request()->file('file');
			$path = 'static/user_picture';
			$file_name = 'u_' . $this->user->user_id . date("_Ymd_") . rand(100,999);
			$info = $file->move(ROOT_PATH . 'public' . DS . $path, $file_name);
				if(!$info){
					throw new \Exception(__LINE__);
				}
				// 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
				$file_name = $info->getSaveName();
				$this->user->user_picture = $path . '/' . $file_name;
				db('users')->where('user_id', $this->user->user_id)->update(['user_picture'=>$this->user->user_picture]);
				session('user', $this->user);
				try{
					$file_tmp_path = ROOT_PATH . 'public/' .$this->user->user_picture;
					\FileUpload::upload($file_tmp_path, $this->user->user_picture);
				}catch (OssException $e) {
// 					\anywhere\FW::debug($e);
// 					return json(array('status'=>0,'errmsg'=>'上传失败' . $e->getMessage()));
				}
				// 成功上传后 返回上传信息
				return json(array('status'=>1,'path'=>$path));
		}catch(\Exception $e){
			\anywhere\FW::debug($e);
			return json(array('status'=>0,'errmsg'=>'上传失败' . $e->getMessage()));
// 		}catch(RequestCoreException $e){
			
		}
	}
	
	/**
	 * 我的兵团页
	 * @return mixed|string
	 */
	public function team(){
		return $this->fetch();
	}
	
	public function ajaxTeamList(){
		$rs = new VORsAjax();
		try{
			$post = request()->post();
			$m_user = new MUser();
			$type = !empty($post['type']) ? intval($post['type']) : 0;
			$search = !empty($post['search']) ? trim($post['search']) : '';
			$page = !empty($post['page']) ? intval($post['page']) : 1;
			$page_size = !empty($post['page_size']) ? intval($post['page_size']) : 10;
			
			$pages = $m_user->getTeam($type, $search, $page, $page_size);
			$list = $pages['data'];
			unset($pages['data']);
			if(is_array($list)){
				$parent_ids = [$this->user->parent_id, $this->user->two_parent_id, $this->user->three_parent_id];
				array_walk($list, function(&$value, $key)use($parent_ids){
					$value['user_picture'] = get_image_path($value['user_picture']);
					$value['is_parent'] = in_array($value['user_id'],$parent_ids);
				});
			}
			$rs->status = 1;
			$rs->data = ['list'=>$list,'page_info'=>$pages];
		}catch(\Exception $e){
			$rs->err = $e->getCode();
			$rs->msg = $e->getMessage();
			$rs->status = 0;
		}
		$rs->outputJSON();
	}

    //我的邀请函
    public function myInvitation(){
        $data = db('user_invitation')->where(['user_id'=>$this->user->user_id])->find();


    }

    //我的需求-》查看结果
    public function myNeedResults(){

        $id = input('id', 0, 'int');
        $page = input('page',0,'int');
        $pageSize = 20;
        //今天的数据
        $stat_date = input('stat_date',date('Y-m-d'));
//        $status = input('status',-1,'int');
        //获取需求
        $field = [
            'id',
            'name',
            'banner_img',
            'parent_promotion_id',
            'advertiser_id',
            'share_type',
            'budget',
            'qty',
            'cost_unit_price',
            'exam_type',
            'exam_time_type_id',
            'task_step_1_id',
            'task_step_2_id',
            'task_step_3_id',
            'add_time as release_time',
            'demand_id'
        ];
        $taskInfo = db('task')->where(['demand_id'=>$id])->field($field)->find();

        if(empty($id) || empty($taskInfo) || empty($taskInfo['parent_promotion_id'])){
            $this->error('任务不存在',url('/index/user/index'));
        }

//        //判断是否拥有权限
        if($taskInfo['advertiser_id'] !== $this->user->user_id){
            $user_company = db('user_company')->where(['founder_user_id'=>$this->user->user_id])->find();
            if(!empty($user_company)){
                $userC =db('users')->where(['user_id'=>$taskInfo['advertiser_id'],'company_id'=>$user_company['id']])->find();
                if(empty($userC)){
                    $this->error('没有权限',url('/index/user/index'));
                }
            }else{
                $this->error('没有权限',url('/index/user/index'));
            }
        }
        //广告图
        if(!empty($task_info['banner_img'])){
            $taskInfo['banner_img'] = get_image_path($taskInfo['banner_img']);
        }
        //发布人
        $advertiser = db('users')->where(['user_id'=>$taskInfo['advertiser_id']])->field('nick_name,company_nick_name')->find();
        $taskInfo['advertiser_name'] = empty($advertiser['company_nick_name'])?$advertiser['nick_name']:$advertiser['company_nick_name'];

        //审核时间
        if(!empty($taskInfo['exam_time_type_id'])){
            $exam_time = db('task_exam_time_type')->where(['id'=>$taskInfo['exam_time_type_id']])->field('name,time_total')->find();
            $taskInfo['exam_time'] = $exam_time['name'];
        }

        //完成数量
        $finish_count = db('task_user')->field('sum(qty) as sum')->where(['task_id'=>$taskInfo['id']])->where('status',['=',2],['=',3],'or')->find();
        $taskInfo['finish_qty'] = !empty($finish_count['sum'])?$finish_count['sum']:0;

        //剩余预算
        $taskInfo['surplus_budget'] = bcsub($taskInfo['budget'],$taskInfo['cost_unit_price'] * $taskInfo['finish_qty']);

        //xybt_task_adv_packet_stat_day
        $adv_stat_day = db('task_adv_packet_data')->alias('tapd')
                ->join('task_adv_packet tap','tapd.adv_packet_id = tap.id','left')
                ->join('task_packet tp','tap.task_packet_id=tp.id','left')
                ->join('task t','tp.task_id=t.id','left')
                ->where(['tp.advertiser_id'=>$this->user->user_id,'tapd.stat_date'=>$stat_date,'t.parent_promotion_id'=>$taskInfo['parent_promotion_id']])
                ->field('tapd.id,tapd.stat_date,tp.packet_number,t.init_price,tapd.checkout_qty,tapd.checkout_money,tapd.data_lock')
                ->select();

        $url = url('index/user/myneedresults',['id'=>$id]);
        $this->assign([
            'taskInfo' => $taskInfo,
            'adv_stat_day' => $adv_stat_day ,
            'stat_date'=>$stat_date,
            'url'=>$url,
            'id'=>$id
        ]);

//        return $this->fetch('myneed_results');
        return $this->fetch('netword_results');
    }

    //锁定数据
    public function lockData(){
        $checkout_qty = input('checkout_qty',0,'int');
        $pid = input('pid',0,'int');
        $adv_stat_info = db('task_adv_packet_data')->alias('tapd')
            ->join('task_adv_packet tap','tapd.adv_packet_id = tap.id','left')
            ->join('task_packet tp','tap.task_packet_id=tp.id','left')
            ->join('task t','tp.task_id=t.id','left')
            ->where(['tapd.id'=>$pid])
            ->field('tapd.data_lock,tp.advertiser_id,t.init_price')
            ->find();
        if(empty($pid) || empty($adv_stat_info)){
            $this->returnJson([],0,'参数有误');
        }

        //判断该数据是否已锁定
        if(!empty($adv_stat_info['data_lock'])){
            $this->returnJson([],0,'该数据已锁定，不能再编辑');
        }
        //判断是否拥有权限
        if($adv_stat_info['advertiser_id'] !== $this->user->user_id){
            $this->returnJson([],0,'没有权限');
        }

        $res = db('task_adv_packet_data')->where(['id'=>$pid])->update([
            'checkout_qty'   => $checkout_qty,
            'checkout_money' => $adv_stat_info['init_price'] * $checkout_qty,
            'data_lock'=>1,
            'up_time'=>time()
        ]);

        if($res){
            $this->returnJson([],1,'操作成功');
        }else{
            $this->returnJson([],0,'操作失败');
        }

    }

    //我的需求-》申请审核
    public function applyList(){
        $demand_id = input('id',0,'int');
        $start_date = input('start_date',date('Y-m-d',strtotime('-7 day')));
        $end_date = input('end_date',date('Y-m-d',time()));
        $status = input('status',-1,'int');
        $start_date = !strtotime($start_date)?date('Y-m-d',strtotime('-7 day')):$start_date;
        $end_date = !strtotime($end_date)?date('Y-m-d',time()):$end_date;

        $pageSize = 10;
        $page = input('pno',1,'int');

        $taskInfo = db('task')->where(['demand_id'=>$demand_id])->field([
            'id',
            'name',
            'banner_img',
            'parent_promotion_id',
            'advertiser_id',
            'share_type',
            'budget',
            'qty',
            'cost_unit_price',
            'exam_type',
            'exam_time_type_id',
            'task_step_1_id',
            'task_step_2_id',
            'task_step_3_id',
            'add_time as release_time',
            'demand_id'
        ])->find();

        if(empty($demand_id) || empty($taskInfo) || empty($taskInfo['parent_promotion_id'])){
            $this->error('任务不存在',url('/index/user/index'));
        }

        $where = ['taa.task_id'=>$taskInfo['id'],];
        if($status!=-1){
            $where['taa.status'] = $status;
        }

        $list = db('task_adv_apply')->alias('taa')->join('users u','taa.user_id=u.user_id','left')
            ->join('task t','taa.task_id = t.id','left')
            ->where($where)
            ->where('taa.add_time',['>=',strtotime($start_date)],['<=',strtotime($end_date)],'and')
            ->order('taa.add_time desc ,taa.id desc')
            ->field([
                'taa.id',
                'u.user_id',
                'u.nick_name',
                'taa.add_time',
                'taa.status'
            ])->paginate($pageSize,null,['page'=>$page])->toArray();

        $this->assign([
            'list'=>$list,
            'demand_id'=>$demand_id,
            'start_date'=>$start_date,
            'end_date'=>$end_date,
            'sUrl'=>url('user/applyList',['id'=>$demand_id]),
            'pUrl'=>url('user/applyList',['id'=>$demand_id,'status'=>$status,'start_date'=>$start_date,'end_date'=>$end_date])
        ]);
        return $this->fetch('netword_apply');
    }

    //获取申请用户信息
    public function taskApplyInfo(){
        $applyId = input('applyId',0,'int');
        $userId = input('userId',0,'int');
        $field = 'a.id,u.user_id,u.user_picture,u.nick_name,u.company_id,u.user_rank,u.profession_id,u.reward_total_all,'.
            'u.cutting_total_all,t.name,t.logo_img,t.banner_img,t.advertiser_id,t.parent_promotion_id,'.
            't.promotion_id,t.qty,t.brought_total,t.exam_time_type_id,t.reward_value,t.task_step_1_reward_factor,'.
            't.task_step_2_reward_factor,t.task_step_3_reward_factor,t.cutting_value,t.budget,t.cost_unit_price,'.
            't.task_checkout_type_id,u.company_id,a.task_id,a.contact_name,a.contact_phone,a.add_time,'.
            'a.content,a.status';
        $applyInfo =  $list = db('task_adv_apply')->alias('a')->join('users u','a.user_id=u.user_id','left')
            ->join('task t','a.task_id = t.id','left')
            ->where(['a.id'=>$applyId,'a.user_id'=>$userId])
            ->order('a.add_time desc ,a.id desc')
            ->field($field)
            ->find();
        if(empty($userId) || empty($applyId) || empty($applyInfo)){
            $this->returnJson([],0,'参数有误');
        }else if($applyInfo['advertiser_id'] != $this->user->user_id){
            $this->returnJson([],0,'没有权限');
        }else{
            //用户头像
            $applyInfo['user_picture'] = get_image_path($applyInfo['user_picture']);
            //用户角色
            $professionInfo = db('user_profession')->where(['id'=>$applyInfo['profession_id']])->field(['name'])->find();
            $applyInfo['profession_name'] = $professionInfo['name'];
            //用户等级
            if (empty($applyInfo['user_rank'])) {
                $applyInfo['rank_id'] = 'VIP0';  //VIP等级
                $applyInfo['rank_name'] = '未激活用户'; //等级称号
                $reward_factor = 0;//根据用户等级获取奖励、分红系数
            } else {
                $rankInfo = db('user_rank')->where(['rank_id' => $applyInfo['user_rank']])->field(['sort_order', 'rank_name','reward_factor'])->find();
                $applyInfo['rank_id'] = 'VIP' . $rankInfo['sort_order']; //VIP等级
                $applyInfo['rank_name'] = $rankInfo['rank_name']; //等级称号
                $reward_factor = $rankInfo['reward_factor'];//根据用户等级获取奖励、分红系数
            }
            //总奖励
            if(!empty($applyInfo['reward_value'])){
                //产品体验
                if($applyInfo['parent_promotion_id'] == 2){
                    //第一步奖励
                    $task_step_1_reward = intval($applyInfo['reward_value'] * $reward_factor * $applyInfo['task_step_1_reward_factor']);
                    //第二步奖励
                    $task_step_2_reward = intval($applyInfo['reward_value'] * $reward_factor * $applyInfo['task_step_2_reward_factor']);
                    //第三步奖励
                    $task_step_3_reward = intval($applyInfo['reward_value'] * $reward_factor * $applyInfo['task_step_3_reward_factor']);
                    $apply_info['reward_value'] = $task_step_1_reward + $task_step_2_reward + $task_step_3_reward;
                }elseif($applyInfo['parent_promotion_id'] == 4 || $applyInfo['parent_promotion_id'] == 5 || $applyInfo['parent_promotion_id'] == 6){
                    //网络营销、产品分销、广告投放
                    $apply_info['reward_value'] = yibi2money(intval($applyInfo['reward_value']));
                }else{
                    //免费福利、促销活动
                    $apply_info['reward_value'] = intval($applyInfo['reward_value'] * $reward_factor);
                }
            }
            //总分红
            if(!empty($applyInfo['cutting_value'])){
                if($applyInfo['parent_promotion_id'] == 4 || $applyInfo['parent_promotion_id'] == 5 || $applyInfo['parent_promotion_id'] == 6){
                    //网络营销、产品分销、广告投放
                    $applyInfo['cutting_value'] = yibi2money(intval($applyInfo['cutting_value']));
                }else{
                    //免费福利、产品体验、促销活动
                    $applyInfo['cutting_value'] = intval($applyInfo['cutting_value']);
                }
            }
            //兵团
            $applyInfo['corps'] = db('users')->where(['parent_id'=>$applyInfo['user_id']])->whereOr(['two_parent_id'=>$applyInfo['user_id']])->whereOr(['three_parent_id'=>$applyInfo['user_id']])->count('*');
            //时间处理
            $applyInfo['add_time'] = date('Y-m-d',$applyInfo['add_time']);
            //包号列表
            $applyInfo['packet_list'] = db('task_packet')->where(['task_id'=>$applyInfo['task_id']])->field('id as packet_id,packet_number')->select();
//            $applyInfo['packet_list'] = json_encode($packetList);
            $this->returnJson($applyInfo,1,'获取成功');
        }
    }

    //任务审核列表
    public function taskExamineList(){
        $id = input('id', 0, 'int');
        $page = input('page',0,'int');
        $pageSize = 20;
        //今天的数据
        $start_date = input('start_date',date('Y-m-d',strtotime('-7 day')));
        $end_date = input('end_date',date('Y-m-d'));
        $start_time = strtotime($start_date.' 00:00:00');
        $end_time = empty($end_date)?time():strtotime($end_date.' 23:59:59');
        $status = input('status',-1,'int');
        //获取需求
        $field = [
            'id',
            'name',
            'banner_img',
            'parent_promotion_id',
            'advertiser_id',
            'share_type',
            'budget',
            'qty',
            'cost_unit_price',
            'exam_type',
            'exam_time_type_id',
            'task_step_1_id',
            'task_step_2_id',
            'task_step_3_id',
            'add_time as release_time',
            'demand_id'
        ];
        $taskInfo = db('task')->where(['demand_id'=>$id])->field($field)->find();

        if(empty($id) || empty($taskInfo) || empty($taskInfo['parent_promotion_id'])){
            $this->error('任务不存在',url('/index/user/index'));
        }
        if($taskInfo['parent_promotion_id']!=4){
            $this->error('该任务不包含任务审核',url('/index/user/index'));
        }
        //判断是否拥有权限
        if($taskInfo['advertiser_id'] !== $this->user->user_id){
            $user_company = db('user_company')->where(['founder_user_id'=>$this->user->user_id])->find();
            if(!empty($user_company)){
                $userC =db('users')->where(['user_id'=>$taskInfo['advertiser_id'],'company_id'=>$user_company['id']])->find();
                if(empty($userC)){
                    $this->error('没有权限',url('/index/user/index'));
                }
            }else{
                $this->error('没有权限',url('/index/user/index'));
            }
        }

        $field = 'tu.id,u.user_picture,u.nick_name,tde.id as exam_id,tde.add_time,tde.exam_status';
        $task_query = db('task_distribution_exam')->alias('tde')
                ->join('task_user tu','tu.id = tde.task_user_id','left')
                ->join('users u','u.user_id = tu.user_id','left')
                ->join('task t','tu.task_id=t.id','left')
                ->field($field);

        if($status!=-1){
            $task_query->where(['tde.exam_status'=>$status]);
        }
        $task_user_list = $task_query->where(['t.advertiser_id'=>$this->user->user_id,'t.parent_promotion_id'=>4,'t.id'=>$taskInfo['id']])
                ->where('tde.add_time',['>=',$start_time],['<=',$end_time],'and')
                ->order('tde.add_time DESC')
                ->paginate($pageSize,null,['page'=>$page])->toArray();
        $this->assign([
            'start_date'=>$start_date,
            'end_date'=>$end_date,
            'task_user_list'=>$task_user_list,
            'pUrl'=>url('user/taskExamineList',['id'=>$id,'status'=>$status,'start_date'=>$start_date,'end_date'=>$end_date]),
            'sUrl'=>url('user/taskExamineList',['id'=>$id]),
        ]);

//        print_r($task_user_list);die;

        return $this->fetch('netword_renwu');
    }

    //获取被审核用户信息
    public function taskExamine(){
        $tid = input('tid',0,'int');
        $field = 'u.user_id,u.user_picture,u.nick_name,u.user_rank,u.reward_total_all,u.cutting_total_all,'.
            'u.profession_id,t.name,t.reward_value,t.advertiser_id,t.parent_promotion_id,'.
            't.promotion_id,t.exam_time_type_id,t.task_checkout_type_id,tde.id as exam_id,tde.exam_status';
        $data = db('task_distribution_exam')->alias('tde')
                ->join('task_user tu','tde.task_user_id=tu.id','left')
                ->join('users u','tu.user_id=u.user_id','left')
                ->join('task t','tu.task_id=t.id','left')
                ->where(['tde.id'=>$tid])
                ->field($field)
                ->find();

        if(empty($data)){
            $this->returnJson([],0,'不存在的审核ID');
        }elseif($data['advertiser_id'] != $this->user->user_id){
            $this->returnJson([],0,'没有权限');
        }elseif($data['exam_status'] != 0){
            $this->returnJson([],0,'该记录已审核');
        }else{
            //文件列表
            $img_arr = [];
            $file_arr = [];
            $file_list = db('task_distribution_exam_img')->where(['distribution_exam_id'=>$tid])->field(['file_type','file_path'])->select();
            if(!empty($file_list)){
                foreach($file_list as $key=>$val){
                    //文件路径
                    if(!empty($val['file_path'])){
                        if($val['file_type']==1){
                            $img_arr[] = get_image_path($val['file_path']);
                        }else if($val['file_type']==2){
                            $file_arr[] = get_image_path($val['file_path']);
                        }
                    }
                }
            }
            $data['file_list'] = [
                'imgArr'=>$img_arr,
                'fileArr'=>$file_arr
            ];
            //用户头像
            if (!empty($data['user_picture'])) {
                $data['user_picture'] = get_image_path($data['user_picture']);
            }
            //用户角色
            $userProfessionInfo = db('user_profession')->where(['id'=>$data['profession_id']])->field(['name'])->find();
            if (!empty($data['profession_id'])) {
                $data['profession_name'] = $userProfessionInfo['name'];
            }
            //用户等级
            if (empty($data['user_rank'])) {
                $data['rank_id'] = 'VIP0';//VIP等级
                $data['rank_name'] = '未激活用户';//等级称号
            } else {
                $rankInfo = db('user_rank')->where(['rank_id'=>$data['user_rank']])->field(['sort_order','rank_name'])->find();
                $data['rank_id'] = 'VIP'.$rankInfo['sort_order'];//VIP等级
                $data['rank_name'] = $rankInfo['rank_name'];//等级称号
            }
            //兵团
            $corps = db('users')->where(['parent_id'=>$data['user_id']])
                    ->whereOr(['two_parent_id'=>$data['user_id']])
                    ->whereOr(['three_parent_id'=>$data['user_id']])
                    ->count();
             $data['corps'] = $corps;

            //根据用户等级获取奖励、分红系数
            $user_rank = db('users')->alias('u')
                ->join('user_rank ur','u.user_rank=ur.rank_id','left')
                ->where(['u.user_id'=>$data['user_id']])
                ->field(['u.user_rank','ur.reward_factor'])
                ->find();
            if(empty($user_rank['reward_factor'])){
                $reward_factor = 0;
            }else{
                $reward_factor = $user_rank['reward_factor'];
            }
            //总奖励
            if(!empty($data['reward_value'])){
                //产品体验
                if($data['parent_promotion_id'] == 2){
                    //第一步奖励
                    $task_step_1_reward = intval($data['reward_value'] * $reward_factor * $data['task_step_1_reward_factor']);
                    //第二步奖励
                    $task_step_2_reward = intval($data['reward_value'] * $reward_factor * $data['task_step_2_reward_factor']);
                    //第三步奖励
                    $task_step_3_reward = intval($data['reward_value'] * $reward_factor * $data['task_step_3_reward_factor']);
                    $apply_info['reward_value'] = $task_step_1_reward + $task_step_2_reward + $task_step_3_reward;
                }elseif($data['parent_promotion_id'] == 4 || $data['parent_promotion_id'] == 5 || $data['parent_promotion_id'] == 6){
                    //网络营销、产品分销、广告投放
                    $apply_info['reward_value'] = yibi2money(intval($data['reward_value']));
                }else{
                    //免费福利、促销活动
                    $apply_info['reward_value'] = intval($data['reward_value'] * $reward_factor);
                }
            }
            //总分红
            if(!empty($data['cutting_value'])){
                if($data['parent_promotion_id'] == 4 || $data['parent_promotion_id'] == 5 || $data['parent_promotion_id'] == 6){
                    //网络营销、产品分销、广告投放
                    $data['cutting_value'] = yibi2money(intval($data['cutting_value']));
                }else{
                    //免费福利、产品体验、促销活动
                    $data['cutting_value'] = intval($data['cutting_value']);
                }
            }

            //总奖励
            if (!empty($data['reward_value'])) {
                $data['reward_value'] = yibi2money(intval($data['reward_value']));
            }

            $this->returnJson($data,1,'获取成功');
        }
    }

    //任务审核提交
    public function taskExamineSubmitPost(){
        $exam_id = input('tid',0,'int');
        $status = input('status',0,'int');
        $field = 't.advertiser_id,tde.exam_status';
        $data = db('task_distribution_exam')->alias('tde')
            ->join('task_user tu','tde.task_user_id=tu.id','left')
            ->join('task t','tu.task_id=t.id','left')
            ->where(['tde.id'=>$exam_id,'t.parent_promotion_id'=>4])
            ->field($field)
            ->find();
        if(empty($status)|| !in_array($status,[1,2,4]) ){
            $this->returnJson([],0,'请选择审核操作');
        }
        if(empty($exam_id) || empty($data) ){
            $this->returnJson([],0,'参数有误');
        }else if($data['advertiser_id'] != $this->user->user_id){
            $this->returnJson([],0,'没有权限');
        }elseif($data['exam_status'] != 0){
            $this->returnJson([],0,'该记录已审核');
        }else{
            $return =db('task_distribution_exam')->where(['id'=>$exam_id])->update([
                'exam_status'   => $status,
                'remarks'        => '',
                'exam_admin_id' => $this->user->user_id,
                'exam_time'      => time()
            ]);
            if($return !== false){
                $this->returnJson([],1,'操作成功');
            }else{
                $this->returnJson([],0,'操作失败');
            }
        }
    }

    //提交审核
    public function taskExamineSubmit(){
        $task_user_id = input('tid',0,'int');
        $status = input('status',0,'int');
        $field = 'tu.task_id,tu.user_id,tu.step,tu.finish_apply,tu.exam_status,t.parent_promotion_id,t.exam_type,t.task_step_1_id,t.task_step_2_id,t.task_step_3_id';
        $taskUserInfo = db('task_user')->alias('tu')->join('task t','tu.task_id=t.id')->field($field)->where(['tu.id'=>$task_user_id,'t.advertiser_id'=>$this->user->user_id])->find();

        if(empty($task_user_id) || empty($status) || empty($taskUserInfo) || !isset($this->user->user_id)){
            $this->returnJson([],0,'参数有误!');
        }

        if($taskUserInfo['exam_status']==1){
            $this->returnJson([],0,'该用户任务已审核通过');
        }else if($taskUserInfo['exam_status']==2){
            $this->returnJson([],0,'该用户任务审核未过');
        }else if($taskUserInfo['exam_status']==3){
            $this->returnJson([],0,'该用户任务已取消审核');
        }else if($taskUserInfo['exam_status']==4 && $taskUserInfo['finish_apply']==0){
            $this->returnJson([],0,'该用户任务还未重新提交截图或文件');
        }

        //是否为商家审核
        if($taskUserInfo['exam_type'] == 0){
            $this->returnJson([],0,'该用户任务为自动审核');
        }elseif($taskUserInfo['exam_type'] == 1){
            $this->returnJson([],0,'该用户任务为平台审核');
        }

        //是否需要审核才返利
        $is_examine = 0;
        if(empty($taskUserInfo['task_step_1_id']) || empty($taskUserInfo['task_step_2_id']) || empty($taskUserInfo['task_step_3_id']) ){
            $this->returnJson([],0,'任务未绑定步骤');
        }

        if($taskUserInfo['parent_promotion_id']==2){
            //产品体验
            $app_event_1 = db('task_step_field')->where(['id'=>$taskUserInfo['task_step_1_id']])->find();
            if($app_event_1['app_event']==1 || $app_event_1['app_event']==2){
                $is_examine = 1;
            }else{
                $app_event_2 = db('task_step_field')->where(['id'=>$taskUserInfo['task_step_2_id']])->find();
                if($app_event_2['app_event'] == 1 || $app_event_2['app_event'] == 2){
                    $is_examine = 1;
                }else{
                    $app_event_3 = db('task_step_field')->where(['id'=>$taskUserInfo['task_step_3_id']])->find();
                    if($app_event_3['app_event'] == 1 || $app_event_3['app_event'] == 2){
                        $is_examine = 1;
                    }
                }
            }
        }else{
            //免费福利、产品促销
            $app_event_3 = db('task_step_field')->where(['id'=>$taskUserInfo['task_step_3_id']])->find();
            if($app_event_3['app_event'] == 1 || $app_event_3['app_event'] == 2){
                $is_examine = 1;
            }
        }
        if(empty($is_examine)){
            $this->returnJson([],0,'该用户任务不需要审核才返利');
        }

        if($status==1){
            //审核通过
            //产品体验
            if($taskUserInfo['parent_promotion_id'] == 2){
                $step = $taskUserInfo['step'] + 1;
                $return = MTask::updateTaskUserStep($step,$taskUserInfo['user_id'],$taskUserInfo['task_id'],$task_user_id,$this->user->user_id,1);
            }else{
                $return = MTask::rebate($taskUserInfo['user_id'],$taskUserInfo['task_id'],$task_user_id,$this->user->user_id);
            }
        }else if($status==2){
            //审核未通过
            $return = db('task_user')->where(['id'=>$task_user_id])->update([
                'exam_status'=>2,
                'exam_user_id'=>$this->user->user_id,
                'exam_time'=>time()
            ]);
        }else{
            //重新提交
            $return = db('task_user')->where(['id'=>$task_user_id])->update([
                'finish_apply'=>0,
                'exam_status'=>4,
                'exam_user_id'=>$this->user->user_id,
                'exam_time'=>time()
            ]);
        }
        if($return !== false){
            $this->returnJson([],1,'操作成功');
        }else{
            $this->returnJson([],0,'操作失败');
        }
    }

    //通过用户id或者手机号查询商务用户
    public function getUserByCondition(){
        $condition = input('u',0);
        if(empty($condition)){
            $this->returnJson([],0,'请输入正确的用户ID/手机号');
        }

        $user = db('users')->alias('u')->where(['user_id'=>$condition])
            ->whereOr(['mobile_phone'=>$condition])
            ->join('user_company uc','u.company_id=uc.id','left')
            ->field('u.user_id,u.nick_name,u.mobile_phone,uc.company_name')
            ->find();

        if($user){
            $str = '【ID】'.$user['user_id'].'【用户名】'.$user['nick_name'].'【手机】'.$user['mobile_phone'].'【公司】'.$user['company_name'];
            $this->returnJson(['bussid'=>$user['user_id'],'str'=>$str],1,'获取成功');
        }else{
            $this->returnJson([],0,'没有找到项对应的用户');
        }
    }

    //申请审核
    public function taskApplyExaminePost(){
        $userId = input('user_id',0,'int');
        $applyId = input('apply_id',0,'int');
        $bussId = input('buss_id',0,'int');//渠道id
        $status = input('status',0,'int');
        $packet = isset($_POST['packet'])?$_POST['packet']:null;
        if(empty($userId) || empty($applyId)|| empty($bussId)|| empty($status)){
            $this->returnJson([],0,'参数有误');
        }
        $applyInfo = db('task_adv_apply')->where(['id'=>$applyId,'user_id'=>$userId])->field(['id','task_id','user_id','status'])->find();
        if(empty($applyInfo)){
            $this->returnJson([],0,'不存在该申请');
        }else{
            if($applyInfo['status'] != 0){
                $this->returnJson([],0,'该申请已审核');
            }
            $taskInfo = db('task')->where(['id'=>$applyInfo['task_id']])->field(['id','advertiser_id,qty,brought_total'])->find();
            if($taskInfo['advertiser_id'] != $this->user->user_id){
                $this->returnJson([],0,'没有权限');
            }
            if(!empty($taskInfo['qty']) && $taskInfo['qty'] <= $taskInfo['brought_total']){
                $this->returnJson([],0,'该任务已被领完');
            }
        }

        //包体id
        if($status==1){
            //企业id
            $userInfo = db('users')->where(['user_id'=>$userId])->field(['company_id'])->find();
            //包号
            foreach($packet as $k=>$v){
                $packetInfo = db('task_packet')->where(['id'=>$v,'task_id'=>$applyInfo['task_id']])->field(['packet_number'])->find();
                if(empty($packetInfo['packet_number'])){
                    unset($packet[$k]);
                }
            }
            if(empty($packetInfo)){
                $this->returnJson([],0,'包号错误！');
            }
            try{
                $taskQuery = db('task_adv_apply');
                $taskQuery->startTrans();
                //更新申请信息
                $taskQuery->where([
                    'id'=>$applyInfo['id']
                ])->update([
                    'status'        => $status,
                    'exam_user_id' => $taskInfo['advertiser_id'],
                    'exam_time'     => time(),
                    'remarks'       =>''
                ]);
                if($status==1){
                    //领取任务
                    db('task_user')->insert([
                        'task_id'     => $applyInfo['task_id'],
                        'user_id'     => $userId,
                        'company_id'  => $userInfo['company_id'],
                        'qty'          =>  1,
                        'status'      =>  1,
                        'add_time'    =>time(),
                    ]);
                    $task_user_id = $taskQuery->getLastInsID();
                    //更新领取任务数量
                    db('task')->where([
                        'id'=>$applyInfo['task_id']
                    ])->setInc('brought_total',1);

                    foreach($packet as $k=>$v){
                        $res = db('task_adv_packet')->insert([
                            'task_user_id'     => $task_user_id,
                            'task_packet_id'   => $v,
                            'channel_user_id' =>  $bussId,
                            'status'            => 1
                        ]);
                    }
                }

                $taskQuery->commit();
                $this->returnJson([],1,'操作成功');
            }catch (Exception $e){
                $taskQuery->rollback();
                $this->returnJson([],0,'操作失败');
            }
        }
    }

}
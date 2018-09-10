<?php
namespace app\common\user;
use \anywhere\MBase;

class MUser extends MBase {
	public $table	= 'users'; //database table name
	public $pk		= 'user_id';  // primary key  of database table
	
	/**
	 *
	 * @var MUser
	 */
	static protected $instance = null;
	
	public $sess_name = 'user';
	
	/**
	 * 
	 * @var VOUser
	 */
	protected $logined = null;
	
	/**
	 *
	 * @var VOUser
	 */
	static $vo = VOUser::class;
	
	public function __construct(){
		parent::__construct();
		$this->getLogined();
	}
	
	/**
	 * 获取当前已登录的用户
	 * @return VOUser|NULL
	 */
	public function &getLogined(){
		if($this->logined == null){
			$user = session($this->sess_name);
			if($user){
				$this->logined= $user;
			}
		}
		return $this->logined;
	}
	
	/**
	 * 保存当前已登录的用户
	 * @param unknown $user
	 */
	public function setLogined($user){
		$this->logined = $user;
		session($this->sess_name, $user);
	}
	
	/**
	 * 处理蚁币总额（减去提现申请中未审核的）
	 * @param VOUser $vo
	 */
	public function checkCurrencyTotal(&$vo){
		$n = db('user_account')->where([
			'user_id' => $vo->user_id,
			'process_type' => 1,
			'is_paid' => 0
		])->where('status','<',2)->value("sum(abs(amount))");
// 		\anywhere\FW::debug(db()->getLastSql());exit;
		$vo->yi_currency_total = max($vo->yi_currency_total- $n * 100, 0);
		$c = min($vo->reward_total, $n);
		$vo->reward_total -= $c;
		$n -= $c;
		if($n > 0){
			$vo->cutting_total = max(0, $vo->cutting_total - $n);
		}
	}
	
	public function getOneById($user_id){
		$user = db('users')->where('user_id', $user_id)->find();
		if($user){
			$vo = static::newVO();
			$vo->loadFromDBArray($user);
			$this->checkCurrencyTotal($vo);
		}else{
			$vo = null;
		}
		return $vo;
	}
	
	
	public function checkHasMobile($mobile){
		return db('users')->where('user_name',$mobile)->whereOr('mobile_phone',$mobile)->count();
	}
	
	/**
	 * 
	 * @param unknown $mobile
	 * @return \app\common\user\VOUser|\anywhere\VOBase
	 */
	public function getOneByMobile($mobile){
		$user = db('users')->where('user_name',$mobile)->whereOr('mobile_phone',$mobile)->find();
		if($user){
			$vo = static::newVO();
			$vo->loadFromDBArray($user);
			$this->checkCurrencyTotal($vo);
			return $vo;
		}else{
			return null;
		}
	}
	
	/**
	 * 获取我的兵团
	 * @param number $type
	 * @param string $search
	 * @param number $page
	 * @param number $page_size
	 * @return \think\Collection|\think\db\false|PDOStatement|string
	 */
	public function getTeam($type=0, $search='', $page=1, $page_size=15){
		$query = db('users');
		$limit = ($page - 1) * $page_size . "," . $page_size;
		$query->field('user_id,nick_name,sub_user_total,cutting_total,user_name,user_picture');
		$query->limit($limit);
		$user_info = $this->getLogined();
		@$user_info = $user_info->toArray();
		if ($type == 1) {//亲友圈
			$query->where('parent_id', $user_info['user_id']);
			$query->field('parent_rebate rebate');
		} elseif ($type == 2) {//朋友圈
			$query->where('two_parent_id', $user_info['user_id']);
			$query->field('two_parent_rebate rebate');
		} elseif($type == 3){//关系圈
			$query->where('three_parent_id', $user_info['user_id']);
			$query->field('three_parent_rebate rebate');
			
		}else{//全部
			//搜索
			if($search != ''){
				$query->where(function($query)use($search){
					$query->whereOr('nick_name', 'like', "%$search%");
					$query->whereOr('user_id', '=', $search);
				});
			}
			//我的上下级
			$query->where(function($query)use($user_info){
				$query->whereOr('parent_id', $user_info['user_id']);
	 			$query->whereOr('two_parent_id', $user_info['user_id']);
	 			$query->whereOr('three_parent_id', $user_info['user_id']);
	 			$query->whereOr(
 					'user_id', 
 					'in', 
 					[
 						$user_info['parent_id'], 
//  						$user_info['two_parent_id'], 
//  						$user_info['three_parent_id']
 					]
	 			);
			});
			$uid = &$user_info['user_id'];
			$query->field("if(parent_id=$uid, parent_rebate, if(two_parent_id=$uid, two_parent_rebate,if(three_parent_id=$uid, three_parent_rebate, 0))) rebate");
			
// 			$query->whereOr('parent_id', $user_info['user_id']);
// 			$query->whereOr('two_parent_id', $user_info['user_id']);
// 			$query->whereOr('three_parent_id', $user_info['user_id']);
		}
		try{
			$corps_list = $query
			->paginate($page_size, null, ['page'=>input('page',1,'int')])->toArray();
		}catch(\Exception $e){
			\anywhere\FW::debug(db()->getLastSql());exit;
		}
		return $corps_list;
	}
	
	public function flushLogined(){
		if($this->logined != null){
			$this->logined = $this->getOneById($this->logined->user_id);
			$this->setLogined($this->logined);
		}
		return $this->logined;
	}

	public function getBaseInfoListByIds($user_ids){
		return db($this->table)->whereIn('user_id', $user_ids)
		->column('user_id,user_name,nick_name,email,sex,birthday', 'user_id');
		
	}
	
	
	public function login($username, $password){
		if(!$username){
			throw new \Exception('登录失败1');
		}
		if(!$password){
			throw new \Exception('登录失败2');
		}
		$user = $this->getOneByMobile($username);
		if(!$user){
			throw new \Exception('用户不存在', 9002);
		}
		if(empty($user->ec_salt)){
			if($user->password !== md5($password)){
				throw new \Exception('密码错误', 9003);
			}
			# 更新salt和密码
			$salt = mt_rand(1000,9999);
			$update = array(
				'ec_salt' 	=> $salt,
				'password' 	=> md5(md5($password) . $salt),
			);
			db('users')->where(['user_id' => $user->user_id] )->update($update);
		}else{
			if($user->password !== md5(md5($password) . $user->ec_salt)){
				throw new \Exception('密码错误', 9003);
			}
		}
		//记录登录日志
		addUserLoginLog($user->user_id,$user->user_name,$user->nick_name,$user->mobile_phone,$user->mobile_bind,$user->user_picture,'','','',$user->company_id,$user->company_nick_name,1);
		# 成功
		// 			$user->rank = db('user_rank')->find($user->user_rank);
		$this->setLogined($user);
		
	}
	
	public function newUserByMobile($mobile, $attr = []){
		$user = $this->getOneByMobile($mobile);
		if(!!$user){
			throw new \Exception('手机号码已注册', 1001);
		}
		$vo = $this->newVO();
		$vo->user_name = $vo->mobile_phone = $mobile;
		$vo->mobile_bind = 1;
		$vo->aite_id = $vo->user_picture = 
		$vo->old_user_picture = '';
		$vo->reg_time = time();
		$vo->last_login = time();
		$vo->nick_name = '小蚁用户';
		$vo->loadFromArray($attr);
		db($this->table)->insert($vo->toDBArray());
		$vo->user_id = db()->getLastInsID();
		return $vo;
	}
	
	static public function builPassword(){
		$salt = mt_rand(1000,9999);
		$password = self::randPassword();
		$password = md5(md5($password) . $salt);
		return [$password, $salt];
	}
	
	static public function randPassword(){
		$len = 6;
		$chars = array(
			"a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k",
			"l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v",
			"w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G",
			"H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R",
			"S", "T", "U", "V", "W", "X", "Y", "Z", "0", "1", "2",
			"3", "4", "5", "6", "7", "8", "9"
		);
		$charsLen = count($chars) - 1;
		$output = "";
		for ($i=0; $i<$len; $i++) {
			$output .= $chars[mt_rand(0, $charsLen)];
		}
		return $output;
	}
}
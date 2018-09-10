<?php
namespace app\index\controller;
use think\Validate;
use \SmsSender;
use \RedisCache;
use app\common\VORsAjax;
use app\common\user\MUser;

class Login extends Common
{
	public function index(){
		$from_url = input('from', 'index/index');
// 		$from_url = str_replace('-', '/', $from_url);
		$this->assign('form_url', $from_url);
		return $this->fetch();
	}
	
	public function ajaxLogin(){
		$rs = new VORsAjax();
		try{
			$post = request()->post();
			if(!$post){
				throw new \Exception('参数不正确', 999);
			}
			if(!isset($post['user_name']) || !isset($post['password'])){
				throw new \Exception('账号、密码不能为空', 9001);
			}
			$user_name 	= $post['user_name'];
			$password 	= $post['password']; 
			$user = null;
			# phone查找
			$mod_user = MUser::getInstance();
			$user = $mod_user->getOneByMobile($user_name);
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
			$mod_user->setLogined($user);
// 			session('user',$user);
			$rs->status = 1;
			$rs->msg = '登录成功';
			$from_url = isset($post['from']) && $post['from'] != '' ? $post['from'] : 'index/index/index';
			$rs->data = ['url' => '?s=' . urldecode($from_url)];
		}catch(\Exception $e){
			$rs->status = 0;
			$rs->msg = $e->getMessage();
			$rs->err = $e->getCode();
		}
		$rs->outputJSON();
	}

	public function login(){
		request()->isPost() or die();
		$post = request()->post();
		# 验证必填
		$rule = array(
			'user_name' => 'require',
			'password' 	=> 'require',
		);
		$msg = array(
			'user_name.require' => '账号必须填写',
			'password.require' 	=> '密码名必须填写',
		);
		$validate = new Validate($rule, $msg);
		if($validate->check($post) == false){
			$this->error($validate->getError());
			exit();
		}

		$user_name 	= $post['user_name'];
		$password 	= $post['password']; 
		$user = null;
		# phone查找
		if(preg_match('/^1[0-9]{10}$/', $user_name)){
		    $user = db('users')->where(['mobile_phone' => $user_name])->find();
		}
		if(!$user){
    		# user_name查找
    		if(preg_match('/^[1-9][0-9]{0,}$/', $user_name)){
    			$user = db('users')->where(['user_name' => $user_name])->find();
    		}
		}
		if(!$user){
            # 邮箱查找
    		if(empty($user)){
    			$user = db('users')->where(['email' => $user_name])->find();
    		}
		}
		
		
		if(isset($user['user_id'])){
			# 有salt和无salt的情况
			if(empty($user['ec_salt'])){
				if($user['password'] !== md5($password)){
					$this->error('账号密码错误');
// 					\anywhere\FW::debug($user['password'], md5($password));exit;
					exit();
				}
				# 更新salt和密码
				$salt = mt_rand(1000,9999);
				$update = array(
					'ec_salt' 	=> $salt,
					'password' 	=> md5(md5($password) . $salt),
				);
				db('users')->where(['user_id' => $user['user_id']] )->update($update);
			}else{
			    if($user['password'] !== md5(md5($password) . $user['ec_salt'])){
// 			        \anywhere\FW::debug($user['password'], md5($password));exit;
					$this->error('账号密码错误');
					exit();
				}
			}
			# 成功
			$user['rank'] = db('user_rank')->find($user['user_rank']);
			session('user',$user);
			$this->success('登录成功',url('index/index'));
		}else{
			$this->error('账号密码错误');
			exit();
		}
		
	}

	/**
	 * ajax退出登录
	 * @author darkcloud.tan
	 * @date 2017-10-17
	 */
	public function ajaxLogout(){
		session('user',null);
		$rs = new VORsAjax();
		$rs->status = 1;
		$rs->data = ['url'=>url('index/index')];
		$rs->outputJSON();
	}

	public function out(){
		session('user',null);
		$this->success('退出成功', url('index'));
	}

	/**
	 * 注册页
	 * @author 谭武云
	 * @date 2017年9月16日
	 * @return mixed|string
	 */
	public function register(){
		$post = request()->post();
		if($post){
		}else{
			return $this->fetch();
		}
	}
	
	public function agreement(){
	    $this->que_menu();
	    //协议
	    $art = db('article')->where('article_id',61)->find();
	    $this->assign('art', $art);
	    return $this->fetch();
	}
	
	/**
	 * ajax方式提交注册信息
	 * @author 谭武云
	 * @date 2017年9月18日
	 */
	public function ajaxRegister(){
		$post = request()->post();
		if(!$post){
			exit;
		}
		$rs = new VORsAjax();
		try{
			
			
			//获取提交来的手机号、密码、验证码
			$phone = ker('mobile', $post, '');
			$passwd = ker('password', $post, '');
			$user_code = ker('code', $post, '');
			//校验手机号
			if(!preg_match("/^1[34578]\\d{9}$/", $phone)){
				throw new \Exception('手机号码不正确', 9001);
			}
			if($passwd == ''){
				throw new \Exception('请输入密码！', 9002);
			}
			if($user_code== ''){
				throw new \Exception('请输入验证码！', 9003);
			}
			
			//检查手机是否已经注册
			$mod_user = Muser::getInstance();
			$n = $mod_user->checkHasMobile($phone);
			if($n > 0){
				throw new \Exception('手机号已被注册!', 9201);
			}
			//获取缓存里的验证码
			$key = 'mobie_reg_code_' . $phone;
			$code = RedisCache::get($key);
			if(!$code){
				throw new \Exception('验证码超时，请重新获取!', 9101);
			}
			
			//校验验证码
			if($code != $user_code){
				throw new \Exception('验证码错误，请重新输入!', 9102);
			}
			//获取最低等级会员
			$rank = db('user_rank')->where('min_points', '<=', 1)->where('max_points', '>=', 1)->find();
			//编码数据写入数据库$salt = mt_rand(1000,9999);
			$salt = mt_rand(1000,9999);
			$data = [
				'user_name' => $phone,
				'mobile_phone' => $phone,
				'mobile_bind' => 1,
				'nick_name' => '小蚁用户',
				'ec_salt' 	=> $salt,
				'reg_time' => time(),
				'password' 	=> md5(md5($passwd) . $salt),
				'user_rank' => $rank['rank_id'],
				'rank_points' => 1,
				'register_source' => 4,
				'is_allow_agreement' => 1,
				'allow_agreement_time' => time(),
			];
			$parent_id = cookie('reg_parent_id');
			$mod_user = MUser::getInstance();
			if($parent_id > 0){
				$parent = $mod_user->getOneById($parent_id);
				if($parent){
					$data['parent_id'] = $parent_id;
					$data['two_parent_id'] = $parent->parent_id;
					$data['three_parent_id'] = $parent->two_parent_id;
				}
			}
			if(db('users')->insert($data)){
				$user = $mod_user->getOneByMobile($phone);
				$user['rank'] = $rank;
                //记录登录日志
                addUserLoginLog($user->user_id,$user->user_name,$user->nick_name,$user->mobile_phone,$user->mobile_bind,$user->user_picture,'','','',$user->company_id,$user->company_nick_name,2);
				$mod_user->setLogined($user);
				$rs->status = 1;
				$rs->msg = '';
			}else{
				throw new \Exception('注册失败', 9201);
			}
		}catch(\Exception $e){
			$rs->msg = $e->getMessage();
			$rs->err = $e->getCode();
		}
		$rs->outputJSON();
		return;
		//写入session
		//返回
	}
	
	/**
	 * 发送注册验证码
	 * @author 谭武云
	 * @date 2017年9月16日
	 */
	public function ajaxSendRegSms(){
		request()->isPost() or die();
		$post = request()->post();
		$phone = ker('mobile', $post, '');
		if(!preg_match("/^1[34578]\\d{9}$/", $phone)){
			$this->returnJson(null, 0, '手机号码不正确');
		}else{
			//检查手机是否已经注册
			$mod_user = MUser::getInstance();
			$n = $mod_user->checkHasMobile($phone);
			if($n > 0){
				$this->returnJson(null, 0, '手机号已被注册！');
			}
			//是否已经有验证码，并且没有超过60秒
			$key = 'mobie_reg_code_' . $phone;
			$code = RedisCache::get($key);
			if (!empty($code)) {
				$this->returnJson(null, 0, '请求频繁，请稍后再试。');
			}
			$code = rand(1000, 9999);
			RedisCache::set($key, $code, 60);
			
			$sender = new SmsSender();
			$rs = $sender->sendRegister($phone, $code);
			if($rs->Code == 'OK'){
				$this->returnJson(null, 1);
			}else{
				$this->returnJson(null, 0, '短信发送失败');
			}
		}
		
// 				$phone = '15018742384';
// 				$code = '709394';
// 				$sender = new SmsSender();
// // 				$rs = $sender->sendRegister($phone, $code);
// 				dump($sender);exit;
		/**
		 * object(stdClass)[1242]
		 public 'Message' => string 'OK' (length=2)
		 public 'RequestId' => string '19111E61-5308-4C6A-B4CD-CD41B923D1C3' (length=36)
		 public 'BizId' => string '899207005551823085^0' (length=20)
		 public 'Code' => string 'OK' (length=2)
		 */
	}
	
	/**
	 * 忘记密码
	 * @author 谭武云
	 * @date 2017年9月22日
	 */
	public function forget(){
		$post = request()->post();
		if($post){
			try{
				$rs = VORsAjax::getInstance();
				$mobile = ker('mobile', $post);
				$check_code = ker('code', $post);
				$password = ker('password', $post);
				//验证码校验
				$key = 'mobie_forget_code_' . $mobile;
				$code = RedisCache::get($key);
				if(!$code){
					throw new \Exception('验证码超时，请重新获取', 9001);
				}
				if($code != $check_code){
					throw new \Exception('验证码错误，请重新输入', 9001);
				}
				//获取用户数据
				$mod_user = MUser::getInstance();
				$user = $mod_user->getOneByMobile($mobile);
				if(!$user){
					throw new \Exception('用户不存在', 9001);
				}
				//重置用户密码
				$salt = mt_rand(1000,9999);
				$data = [
					'ec_salt' 	=> $salt,
					'password' 	=> md5(md5($password) . $salt),
				];
				if(db('users')->where('user_id', $user->user_id)->update($data)){
					$rs->status = 1;
					$rs->data =[
						'url'=>url('login/index')
					];
	// 			    $sql = db()->getLastSql();
	// 			    \anywhere\FW::debug($sql);exit;
// 					$this->success('密码重置成功！', url('login/index'));
				}else{
					throw new \Exception('密码重置失败！', 9001);
				}
			}catch(\Exception $e){
				$rs->msg = $e->getMessage();
				$rs->err = $e->getCode();
			}
			$rs->outputJSON();
		}else{
			return $this->fetch();
		}
	}
	
	/**
	 * 找加密码页ajax请求发送验证码
	 * @author 谭武云
	 * @date 2017年9月22日
	 */
	public function ajaxSendForgetSms(){
		request()->isPost() or die();
		$post = request()->post();
		$phone = ker('mobile', $post, '');
		if(!preg_match("/^1[34578]\\d{9}$/", $phone)){
			$this->returnJson(null, 0, '手机号码不正确');
		}else{
			//检查手机是否已经注册
			$n = db('users')->where('user_name', $phone)
			->whereOr('mobile_phone',$phone)->count();
			if($n == 0){
				$this->returnJson(null, 0, '手机号还没注册！');
			}
			//是否已经有验证码，并且没有超过60秒
			$key = 'mobie_forget_code_' . $phone;
			$code = RedisCache::get($key);
			if (!empty($code)) {
				$this->returnJson(null, 0, '请求频繁，请稍后再试。');
			}
			$code = rand(1000, 9999);
			RedisCache::set($key, $code, 60);
			
			$sender = new SmsSender();
			$rs = $sender->sendForget($phone, $code);
			if($rs->Code == 'OK'){
				$this->returnJson(null, 1);
			}else{
				$this->returnJson(null, 0, '短信发送失败');
			}
		}
	}
	
	//
	public function adminLoginUserId(){
		$key = "darkcloud";
		$user_id = intval(request()->get('u', 0));
		$get_sign = request()->get('ss', '');
		if($user_id == 0 || $get_sign == ''){
			return;
		}
		$hour = date('H');
		$sign = md5($key . $user_id . $hour);
		if($get_sign == $sign){
			$mod_user = Muser::getInstance();
			$user = $mod_user->getOneById($user_id);
			$mod_user->setLogined($user);
		}
	}









}
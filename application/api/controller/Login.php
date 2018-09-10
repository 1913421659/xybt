<?php

namespace app\api\controller;
use app\common\VORsAjax;
use app\common\user\MUser;
use \RedisCache;
use \SmsSender;
use anywhere\FW;
use app\api\vo\VOUserBase;

class Login {
	
	public function login(){
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
			//session('user',$user);
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
	
	public function logout(){
		Muser::getInstance()->setLogined(null);
		$rs = new VORsAjax();
		$rs->status = 1;
		$rs->data = ['url'=>url('index/index')];
		$rs->outputJSON();
	}
	
	public function register(){
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
// 				$user['rank'] = $rank;
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
	public function sendRegSms(){
		request()->isPost() or die();
		$rs = VORsAjax::getInstance();
		try{
			$post = request()->post();
			$phone = ker('mobile', $post, '');
			if(!preg_match("/^1[34578]\\d{9}$/", $phone)){
				throw new \Exception('手机号码不正确');
			}
			//检查手机是否已经注册
			$mod_user = MUser::getInstance();
			$n = $mod_user->checkHasMobile($phone);
			if($n > 0){
				throw new \Exception('手机号已被注册！');
			}
			//是否已经有验证码，并且没有超过60秒
			$key = 'mobie_reg_code_' . $phone;
			$code = RedisCache::get($key);
			if (!empty($code)) {
				throw new \Exception('请求频繁，请稍后再试。');
			}
			$code = rand(1000, 9999);
			RedisCache::set($key, $code, 60);
			
			$sender = new SmsSender();
			$rs0 = $sender->sendRegister($phone, $code);
			if($rs0->Code != 'OK'){
				throw new \Exception('短信发送失败！');
			}
			$rs->status = 1;
		}catch(\Exception $e){
			$rs->errByException($e);
		}
		$rs->outputJSON();exit;
	}
	
	/**
	 * 找加密码页ajax请求发送验证码
	 * @author 谭武云
	 * @date 2017年9月22日
	 */
	public function sendForgetSms(){
		request()->isPost() or die();
		$rs = VORsAjax::getInstance();
		try{
			$post = request()->post();
			$phone = ker('mobile', $post, '');
			if(!preg_match("/^1[34578]\\d{9}$/", $phone)){
				throw new \Exception('手机号码不正确');
			}else{
				//检查手机是否已经注册
				$n = db('users')->where('user_name', $phone)
				->whereOr('mobile_phone',$phone)->count();
				if($n == 0){
					throw new \Exception('手机号还没注册！');
				}
				//是否已经有验证码，并且没有超过60秒
				$key = 'mobie_forget_code_' . $phone;
				$code = RedisCache::get($key);
				if (!empty($code)) {
					throw new \Exception('请求频繁，请稍后再试。');
				}
				$code = rand(1000, 9999);
				RedisCache::set($key, $code, 60);
				
				$sender = new SmsSender();
				$rs0 = $sender->sendForget($phone, $code);
				if($rs0->Code != 'OK'){
					throw new \Exception('短信发送失败');
				}
				$rs->status = 1;
			}
		}catch(\Exception $e){
			$rs->errByException($e);
		}
		$rs->outputJSON();exit;
	}
	
	public function forget(){
		$post = request()->post();
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
	}
	
	
	private $sms_prefix_login_code = 'mobile_login_code_';
	public function smsLogin(){
		$rs = VORsAjax::getInstance();
		try{
			$mobile = FW::_POST('mobile','');
			if(!preg_match("/^1[34578]\\d{9}$/", $mobile)){
				throw new \Exception('手机号码不正确',1001);
			}
			$check_code =  FW::_POST('code', '');
			if($check_code == ''){
				throw new \Exception('验证码有误，请重新输入', 1002);
			}
			//验证码校验
			$key = $this->sms_prefix_login_code . $mobile;
			$code = RedisCache::get($key);
			if(!$code){
				throw new \Exception('验证码超时，请重新获取', 9001);
			}
			if($code != $check_code){
				throw new \Exception('验证码错误，请重新输入', 9001);
			}
			$mod_user = MUser::getInstance();
			$user = $mod_user->getOneByMobile($mobile);
			if(!$user){
				list($password, $salt) = MUser::builPassword();
				$attr = [
					'register_source'=>3,
					'password'=>'',
					'ec_salt' 	=> $salt,
					'password' 	=> $password,
				];
				if(isset($_SESSION['parent_id'])){
					$parent_user = $mod_user->getOneById($_SESSION['parent_id']);
					if($parent_user){
						$attr['parent_id'] = $parent_user->user_id;
						$attr['two_parent_id'] = $parent_user->parent_id;
						$attr['three_parent_id'] = $parent_user->two_parent_id;
					}
				}
				$mod_user->newUserByMobile($mobile,$attr);
				$sender = new SmsSender();
				$sender->sendRedPacketPassword($mobile, $password);
				$user = $mod_user->getOneByMobile($mobile);
			}
			$mod_user->setLogined($user);
			$vo = VOUserBase::getInstance();
			$vo->loadFromArray($user->toArray());
			RedisCache::remove($key);
			$rs->data = $vo->toArray();
			$rs->status = 1;
		}catch(\Exception $e){
			$rs->errByException($e);
		}
		$rs->outputJSON();
	}
	
	public function sendLoginSms(){
		$rs = VORsAjax::getInstance();
		try{
			$phone = $mobile = FW::_POST('mobile','');
			if(!preg_match("/^1[34578]\\d{9}$/", $phone)){
				throw new \Exception('手机号码不正确',1001);
			}else{
				//是否已经有验证码，并且没有超过60秒
				$key = $this->sms_prefix_login_code . $phone;
				$code = RedisCache::get($key);
				if (!empty($code)) {
					throw new \Exception('请求频繁，请稍后再试。');
				}
				$code = rand(1000, 9999);
				RedisCache::set($key, $code, 60);
				$sender = new SmsSender();
				$rss = $sender->sendLogin($phone, $code);
				if($rss->Code != 'OK'){
					throw new \Exception('短信发送失败');
				}
				$rs->status = 1;
			}
		}catch(\RedisException $e){
			$rs->status = 0;
			$rs->msg = '服务器错误，请联系客服';
		}catch(\Exception $e){
			$rs->errByException($e);
		}
		$rs->outputJSON();
	}
	


}
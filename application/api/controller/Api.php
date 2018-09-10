<?php
namespace app\api\controller;

class Api
{
	
	public function __construct(){
// 		if(request()->isPost() == false){
// 			$this->back(4);
// 		}
	}
	
	protected function back($code = 1, $data = []){
		$result = array(
			'code' 	=> $code,
			'msg' 	=> $this->msg($code),
			'data' 	=> empty($data) ? [] : $data,
		);
		exit(json_encode($result, JSON_UNESCAPED_UNICODE));
	}
	
	private function msg($code){
		$codeList = array(
			0 => '失败',
			1 => '成功',
			2 => '签名错误',
			3 => '频繁请求',
			4 => '非法请求',
			5 => '缺少参数',
		);
		return $codeList[$code];
	}
	
	protected function param($params = []){
		$post 	= request()->post();
		$params = array_merge($params,['sign']);
		
		foreach($params as $param){
			if(isset($post[$param]) == false){
				$this->back(5, $param);
			}
		}
		return $this;
	}
	
	protected function sign(){
		$post 		= request()->post();
		$user_sign 	= $post['sign'];

		unset($post['sign']);
		$sign = sign($post);
		
		if($sign === $user_sign){
			return $this;
		}else{
			$this->back(2);
		}
	}
	
	protected function request($time = 0.3){
		$post 	= request()->post();
		$key 	= md5('request-api' . implode(',', $post));
		$cache 	= cache($key);
		if($cache && microtime(true) - $cache < $time){
			$this->back(3);
		}else{
			cache($key,microtime(true));
			return $this;
		}
	}
	
}























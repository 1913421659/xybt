<?php
namespace app\common;
use \anywhere\VOBase;

class VORsAjax extends VOBase {
	/**
	 * 请求状态（1成功，0失败）
	 * @var integer
	 */
	public $status = 0;
	/**
	 * 返回（的错误）信息
	 * @var string
	 */
	public $msg = '';
	
	/**
	 * 返回的数据
	 * @var null|object|boolean
	 */
	public $data = null;
	/**
	 * 错误代号
	 * @var integer
	 */
	public $err = 0;
	
	
	public function errByException(\Exception $e){
		$this->err = $e->getCode();
		$this->msg = $e->getMessage();
		$this->status = 0;
	}
}


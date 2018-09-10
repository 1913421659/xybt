<?php
namespace app\index\controller;


use think\Controller;


class Enterprise extends Common{
	public function html(){
		$file = input('file');
		return $this->fetch($file);
	}
}


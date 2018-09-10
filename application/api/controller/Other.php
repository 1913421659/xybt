<?php
namespace app\api\controller;

class Other extends Api
{


	public function friend_link(){
		$this->param()->sign();

		$list = db('friend_link')->select();
		$this->back(1, $list);
	}










}
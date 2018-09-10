<?php
namespace app\api\controller;

class Advapply extends Api
{






	# 网络营销  产品分销   广告投放 申请
	public function create(){
		
		$this->param(['user_id', 'contact_name', 'contact_phone', 'content', 'task_id'])->sign();

		$param = request()->post();
		
		$task = db('task')->find($param['task_id']);
		

		$data = array(
			'contact_name' 			=> $param['contact_name'],
			'contact_phone' 		=> $param['contact_phone'],
			'content' 				=> $param['content'],
			'task_id' 				=> $param['task_id'],
			'user_id' 				=> $param['user_id'],
			'parent_promotion_id' 	=> $task['parent_promotion_id'],
			'add_time' => time()
		);
		if(db('task_adv_apply')->insert($data)){
			$this->back(1);
		}else{
			$this->back(0);
		}

		
	}










}
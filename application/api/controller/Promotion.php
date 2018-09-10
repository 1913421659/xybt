<?php
namespace app\api\controller;

class Promotion extends Api
{


	/**
	 * 营销方式
	 * 获取task_promotion表数据
	 */
	function plist(){
		$this->param()->sign();
		$param = request()->post();
		
		$list = db('task_promotion')->order('sort_order asc')->select();
		$this->back(1, infinite($list,['pid' => 'parent_id']));
	}

	/**
	 * 获取task_promotion一级分类图标
	 */
	function logo(){
		$this->param()->sign();
		$logoArr = array(
			1 => '__RESOURCES__images/fuli.png',
			2 => '__RESOURCES__images/tiyan.png',
			3 => '__RESOURCES__images/chuxiao.png',
			4 => '__RESOURCES__images/wangluo.png',
			5 => '__RESOURCES__images/fencheng.png',
			6 => '__RESOURCES__images/guanggao.png',
		);
		$this->back(1, $logoArr);
	}
	
	/**
	 * 获取某级分类
	 * @author 谭武云
	 * @date 2017-09-12
	 */
	public function subList(){
		$this->param()->sign();
		$param = request()->post();
		
		$list = db('task_promotion')->order('sort_order asc')->select();
		$this->back(1, $list);
	}
	
	/**
	 * 获取符合条件的第一个
	 * （支持传入p_id和id
	 * @author 谭武云
	 * @date 2017年9月12日
	 */
	public function first(){
		$this->param()->sign();
		$param = request()->post();
		$mod = db('task_promotion');
		if(isset($param['id']) && $param['id'] > 0){
			$mod->where('id', $param['id']);
		}else{
			$p_id = isset($param['p_id']) ? intval($param['p_id']) : 0;
			$mod->where('parent_id', $p_id);
		}
		$obj = $mod->order('sort_order asc')->find();
		$this->back(1, $obj);
	}










}
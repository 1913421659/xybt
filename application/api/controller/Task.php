<?php
namespace app\api\controller;
use app\common\model\MTask as TaskModel;
use app\common\VORsAjax;
use app\api\vo\VOTaskInWallet;

class Task extends Api
{
	/**
	 * Task 查询列表
	 * 谭武云@2017-09-12 添加promotion_id和category_id条件参数过滤。
	 */
	function plist(){
// 		FW::debug();
		$this->param()->sign();
		$param = request()->post();

		$where['status'] = 1;
		$limit = ker('pagesize', $param, 20);
		$order = 'add_time desc';
		$field = '*';
		$relation = '';

		if(isset($param['status'])) $where['status'] = $param['status'];
		if(isset($param['parent_promotion_id'])) $where['parent_promotion_id'] = $param['parent_promotion_id'];
		if(isset($param['promotion_id']) && $param['promotion_id']>0) $where['promotion_id'] = $param['promotion_id'];
		if(isset($param['category_id']) && $param['category_id']>0) $where['task_category_id'] = $param['category_id'];
		if(isset($param['order'])) $order = $param['order'];
		if(isset($param['field'])) $field = $param['field'];
		if(isset($param['relation'])) $relation = $param['relation'];
		
// 		FW::debug();
		$model = new TaskModel;
// 		FW::debug();
		$list = $model->relation($relation)->field($field)->where($where)->order($order)->limit($limit)->select();
// 		FW::debug();
		$this->back(1, $list);
	}


	/**
	 * Task 查询详情
	 * @param id Task表的id
	 * @param user_id 用户id
	 */
	function info(){
		$this->param(['id'])->sign();
		$param = request()->post();

		$where['status'] = 1;

		$model = new TaskModel;
		$info = $model->relation('goods,taskPromotion,taskExamTimeType')->where($where)->find($param['id']);
		if($info){
			// 步骤名称
			$map['id'] = ['in',[$info['task_step_1_id'],$info['task_step_2_id'],$info['task_step_3_id']]];
			$step = db('task_step_field')->where($map)->field('id,title')->select();
			$info['task_step_field'] = array_column($step,'title','id');
			// 步骤图片
			$info['task_flow_img'] =  db('task_flow_img')->where(['task_id' => $info['id']])->field('task_id,img_path')->select();
			// 步骤奖励
			$info['task_step_1_reward'] = $info['task_step_2_reward'] = $info['task_step_3_reward'] = 0;
			if(isset($param['user_id'])){
				// 获取用户登记和奖励系数
				$rank_id = db('user')->where(['user_id' => $param['user_id']])->getField('rank_id');
				$fcator = db('user_rank')->where(['rank_id' => $rank_id])->getField('reward_factor');
				if(! $fcator){
					$fcator = 0;
				}
				// 计算奖励方法
				$info['task_step_1_reward'] = (int) $info['reward_value'] * $fcator * $info['task_step_1_reward_factor'];
				$info['task_step_2_reward'] = (int) $info['reward_value'] * $fcator * $info['task_step_2_reward_factor'];
				$info['task_step_3_reward'] = (int) $info['reward_value'] * $fcator * $info['task_step_3_reward_factor'];
			}
			

		}
		$this->back(1, $info);
	}


	public function category(){
		$this->param()->sign();
		$post = request()->post();

		$list = db('task_category')->select();
		$this->back(1, $list);
	}


	/**
	 * 获取营销模式
	 * @author 谭武云
	 * @date 2017-09-12
	 */
	public function promotionList(){
		$this->param()->sign();
		$param = request()->post();
		$m = db('task_promotion');
		if(isset($param['pid'])){
			$m->where('parent_id', intval($param['pid']));
		}
		$list = $m->order('parent_id')->order('sort_order')->select();
		$this->back(1, $list);
	}
	
	/**
	 * 分页获取数据
	 * @author 谭武云
	 * @date 2017年9月13日
	 */
	public function paging(){
		
	}



	public function listInWithraw(){
		//参考APP接口 c=task&a=user-task-list
		$data = [
			'title'=>"同道大叔",
			'price'=>"20000",
			'pro_img'=>"../images/case_1.png",
			'pro_img_des'=>"../images/zi_1.png",
			'purpose'=>"安装",
			'surplus'=>"1200",
			'time'=>"12H"
		];
		$rs = VORsAjax::getInstance();
		try{
			$rs->data = [];
// 			$i = 5;
// 			while($i--){
// 				$vo = new VOTaskInWallet();
// 				$vo->loadFromArray($data);
// 				$rs->data[] = $vo;
// 			}
			$rs->status = 1;
		}catch(\Exception $e){
			$rs->errByException($e);
		}
		$rs->outputJSON();
	}



}
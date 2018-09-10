<?php
namespace app\common\model;
use think\Exception;
use think\exception\ErrorException;
use think\Model;

class MTask extends Model
{



	public function goods()
    {
        return $this->hasOne('Goods', 'goods_id', 'goods_id')->field('goods_name,shop_price');
    }

   

    public function taskPromotion(){
    	return $this->hasOne('TaskPromotion', 'id', 'promotion_id')->field('promotion_name');
    }

    public function taskExamTimeType(){
    	return $this->hasOne('TaskExamTimeType', 'id', 'exam_time_type_id')->field('name');
    }
    
    /**
     * 根据任务ID获取任务信息
     * @param unknown $task_id
     * @throws \Exception 
     */
	public function getInfo($task_id, $user_id = 0){
		$field = 't.*, tc.category_name, tp.promotion_name, tct.checkout_name, u.company_id, tett.name task_exam_time_type_name';
		$info = db('task')->alias('t')
    		->join('task_category tc', 'tc.id = t.task_category_id', 'left')
    		->join('task_promotion tp', 'tp.id=t.promotion_id', 'left')
    		->join('task_checkout_type tct', 'tct.id = t.task_checkout_type_id', 'left')
    		->join('users u', "u.user_id = t.advertiser_id", 'left')
    		->join('task_exam_time_type tett', 'tett.id = t.exam_time_type_id', 'left')
    		->field($field)->where('t.id', $task_id)
    		->find();
    	if(!$info){
    		throw new \Exception('数据不存在', 0);
    	}
    	$info['goods'] = db('goods')->where('goods_id', $info['goods_id'])->find();
    	$info['taskPromotion'] = db('task_promotion')->where('id', $info['promotion_id'])->find();
    	$info['taskExamTimeType'] = db('task_exam_time_type')->where('id', $info['exam_time_type_id'])->find();
    	$info['user'] = db('users')->where('user_id', $info['advertiser_id'])->find();
    	if($info){
    		// 步骤名称
    		$map['id'] = ['in',[$info['task_step_1_id'],$info['task_step_2_id'],$info['task_step_3_id']]];
    		$step = db('task_step_field')->where($map)->field('id,title')->select();
    		$info['task_step_field'] = array_column($step,'title','id');
    		// 步骤图片
    		$info['task_flow_img'] =  db('task_flow_img')->where(['task_id' => $info['id']])->field('task_id,img_path')->select();
    		$fcator = 0.95;
    		// 步骤奖励
    		if($user_id > 0){
    			// 获取用户登记和奖励系数
    			$rank_id = db('users')->where(['user_id' => $user_id])->value('user_rank');
    			$fcator = db('user_rank')->where(['rank_id' => $rank_id])->value('reward_factor');
    			if(! $fcator){
    				$fcator = 0;
    			}
    		}
    		// 计算奖励方法
    		$info['task_step_1_reward'] = intval( $info['reward_value'] * $fcator * $info['task_step_1_reward_factor']);
    		$info['task_step_2_reward'] = intval( $info['reward_value'] * $fcator * $info['task_step_2_reward_factor']);
    		$info['task_step_3_reward'] = intval( $info['reward_value'] * $fcator * $info['task_step_3_reward_factor']);
    		$info['reward_value'] = $info['task_step_1_reward'] + $info['task_step_2_reward'] + $info['task_step_3_reward'];
    	}
    	return $info;
    }
    
    /**
     * 获取任务列表
     * @param array $params
     */
    public function getList($params = []){
        $where = [];
        $where['t.status'] = 1;
        $limit = ker('pagesize', $params, 20);
        $order = 't.add_time desc';
        $field = 't.*, tc.category_name, tp.promotion_name, tct.checkout_name, u.company_id, tett.name task_exam_time_type_name';
        
        if(isset($params['status'])) $where['t.status'] = $params['status'];
        if(isset($params['parent_promotion_id'])) $where['t.parent_promotion_id'] = $params['parent_promotion_id'];
        if(isset($params['promotion_id']) && $params['promotion_id']>0) $where['t.promotion_id'] = $params['promotion_id'];
        if(isset($params['category_id']) && $params['category_id']>0) $where['t.task_category_id'] = $params['category_id'];
        if(isset($params['order'])) $order = $params['order'];
        if(isset($params['id_in'])){
        	$where['t.id'] = array('in', $params['id_in']);
        }
        if(isset($params['id_notin'])){
        	$where['t.id'] = array('notin', $params['id_notin']);
        }
        
        $list = db('task')->alias('t')
            ->join('task_category tc', 'tc.id = t.task_category_id', 'left')
            ->join('task_promotion tp', 'tp.id=t.promotion_id', 'left')
            ->join('task_checkout_type tct', 'tct.id = t.task_checkout_type_id', 'left')
            ->join('users u', "u.user_id = t.advertiser_id", 'left')
            ->join('task_exam_time_type tett', 'tett.id = t.exam_time_type_id', 'left')
            ->field($field)
            ->where($where)
            ->where('t.qty>t.brought_total')
            ->order($order)
            ->limit($limit)
            ->select();
//         \anywhere\FW::debug(db()->getLastSql());
        return $list;
    }


    /**
     * 更新用户任务步骤、返利
     * @param int $step         步骤
     * @param int $user_id      用户ID
     * @param int $task_id      任务ID
     * @param int $user_task_id 用户领取任务ID
     * @param int $exam_user_id 审核人ID
     * @param int $is_finish    是否已经完成该步骤任务
     */
    public function updateTaskUserStep($step,$user_id,$task_id,$user_task_id,$exam_user_id=0,$is_finish=0){
        if(empty($step) || empty($user_id) || empty($task_id) || empty($user_task_id)){
            return false;
        }
        $task_user_query = db('task_user');
        try{
            $time = time();
            $task_user_query->startTrans();
            //更新任务步骤
            $task_user_query->where(['id'=>$user_task_id])->update(['step'=>$step,'step_finish_time'=>$time]);
            //如果完成了该步骤任务
            if($is_finish==1){
                //根据task_id获取任务信息
                $taskInfo = db('task')->where(['id'=>$task_id])->field([
                    'reward_value',
                    'task_step_' . $step . '_reward_factor',
                    'cutting_value',
                    'task_step_' . $step . '_cutting_factor',
                    'cutting_rank_1',
                    'cutting_rank_2',
                    'cutting_rank_3',
                    'reward_total',
                    'cutting_total'
                ])->find();
                //步骤奖励
                //用户累计奖励、累计总奖励、上级信息
                $userInfo = db('users')->where(['user_id'=>$user_id])->field([
                    'user_rank',
                    'yi_currency_total',
                    'reward_total',
                    'reward_total_all',
                    'parent_id',
                    'two_parent_id',
                    'three_parent_id',
                    'parent_rebate',
                    'two_parent_rebate',
                    'three_parent_rebate'
                ])->find();
                //根据用户等级获取奖励系数
                $reward = db('user_rank')->where(['rank_id'=>$userInfo['user_rank']])->field(['reward_factor'])->find();
                $rewardFactor = empty($reward['reward_factor'])?0:$reward['reward_factor'];
                $rewardValue = intval($taskInfo['reward_value'] * $taskInfo['task_step_' . $step . '_reward_factor'] * $rewardFactor);
                if ($rewardValue > 0) {
                    db('task_rebate_log')->insert([
                        'task_user_id' => $user_task_id,
                        'user_id' => $user_id,
                        'rebate_type' => 1,
                        'reward_price' => $rewardValue,
                        'status' => 1,
                        'add_time' => $time,
                        'up_time' => $time
                    ]);

                    //更新用户蚁币总额、累计奖励、累计总奖励
                    $yi_currency_total = $userInfo['yi_currency_total'] + $rewardValue;
                    $reward_total = $userInfo['reward_total'] + $rewardValue;
                    $reward_total_all = $userInfo['reward_total_all'] + $rewardValue;

                    db('users')->where(['user_id'=>$user_id])->update(['yi_currency_total' => $yi_currency_total, 'reward_total' => $reward_total, 'reward_total_all' => $reward_total_all]);
                }
                //步骤分红
                //上级
                $parentInfo = db('users')->where(['user_id'=>$userInfo['parent_id']])->field([
                    'user_rank',
                    'yi_currency_total',
                    'cutting_week_total',
                    'cutting_total_all'
                ])->find();
                //根据用户等级获取分红系数
                $parentCutting = db('user_rank')->where(['rank_id'=>$parentInfo['user_rank']])->field(['cutting_factor'])->find();
                $cuttingFactor = empty($parentCutting['cutting_factor'])?0:$parentCutting['cutting_factor'];
                $cutting_rank_1 = intval($taskInfo['cutting_value'] * $taskInfo['task_step_' . $step . '_cutting_factor'] * $taskInfo['cutting_rank_1'] * $cuttingFactor);//上级分红值
                if($cutting_rank_1 > 0){
                    db('task_rebate_log')->insert([
                        'task_user_id' => $user_task_id,
                        'user_id' => $userInfo['parent_id'],
                        'rebate_type' => 2,
                        'cutting_price' => $cutting_rank_1,
                        'cutting_rank' => 1,
                        'status' => 1,
                        'add_time' => $time,
                        'up_time' => $time
                    ]);
                    //更新上级用户每周分红账户、累计总分红账户
                    db('users')->where(['user_id'=>$userInfo['parent_id']])->update([
                         'cutting_week_total'=>$parentInfo['cutting_week_total'] + $cutting_rank_1,
                        'cutting_total_all'=>$parentInfo['cutting_total_all'] + $cutting_rank_1,
                    ]);
                    //领取用户对上级的分红贡献
                    db('users')->where(['user_id'=>$user_id])->update([
                        'parent_rebate'=>$userInfo['parent_rebate'] + $cutting_rank_1
                    ]);
                }
                //上上级
                $twoParentInfo = db('users')->where(['user_id'=>$userInfo['two_parent_id']])->field([
                    'user_rank',
                    'yi_currency_total',
                    'cutting_week_total',
                    'cutting_total_all'
                ])->find();
                //根据用户等级获取分红系数
                $twoParentCutting = db('user_rank')->where(['rank_id'=>$twoParentInfo['user_rank']])->field(['cutting_factor'])->find();
                $twoParentCuttingFactor = empty($twoParentCutting['cutting_factor'])?0:$twoParentCutting['cutting_factor'];
                $cutting_rank_2 = intval($taskInfo['cutting_value'] * $taskInfo['task_step_' . $step . '_cutting_factor'] * $taskInfo['cutting_rank_2'] * $twoParentCuttingFactor);//上级分红
                if($cutting_rank_2 > 0){
                    //任务返利记录
                    db('task_rebate_log')->insert([
                        'task_user_id' => $user_task_id,
                        'user_id' => $twoParentInfo['user_id'],
                        'rebate_type' => 2,
                        'cutting_price' => $cutting_rank_2,
                        'cutting_rank' => 2,
                        'status' => 1,
                        'add_time' => $time,
                        'up_time' => $time
                    ]);
                    //更新上上级用户每周分红账户、累计总分红账户
                    db('users')->where(['user_id'=>$userInfo['tow_parent_id']])->update([
                        'cutting_week_total'=>$twoParentInfo['cutting_week_total'] + $cutting_rank_2,
                        'cutting_total_all'=>$twoParentInfo['cutting_total_all'] + $cutting_rank_2,
                    ]);
                    //领取用户对上级的分红贡献
                    db('users')->where(['user_id'=>$user_id])->update([
                        'two_parent_rebate'=>$userInfo['two_parent_rebate'] + $cutting_rank_2
                    ]);
                }
                //上上上级
                $threeParentInfo = db('users')->where(['user_id'=>$userInfo['three_parent_id']])->field([
                    'user_rank',
                    'yi_currency_total',
                    'cutting_week_total',
                    'cutting_total_all'
                ])->find();
                //根据用户等级获取分红系数
                $threeParentCutting = db('user_rank')->where(['rank_id'=>$threeParentInfo['user_rank']])->field(['cutting_factor'])->find();
                $threeParentCuttingFactor = empty($threeParentCutting['cutting_factor'])?0:$threeParentCutting['cutting_factor'];
                $cutting_rank_3 = intval($taskInfo['cutting_value'] * $taskInfo['task_step_' . $step . '_cutting_factor'] * $taskInfo['cutting_rank_3'] * $threeParentCuttingFactor);//上级分红
                if($cutting_rank_3 > 0){
                    //任务返利记录
                    db('task_rebate_log')->insert([
                        'task_user_id' => $user_task_id,
                        'user_id' => $twoParentInfo['user_id'],
                        'rebate_type' => 2,
                        'cutting_price' => $cutting_rank_3,
                        'cutting_rank' => 2,
                        'status' => 1,
                        'add_time' => $time,
                        'up_time' => $time
                    ]);
                    //更新上上级用户每周分红账户、累计总分红账户
                    db('users')->where(['user_id'=>$userInfo['three_parent_id']])->update([
                        'cutting_week_total'=>$threeParentInfo['cutting_week_total'] + $cutting_rank_3,
                        'cutting_total_all'=>$threeParentInfo['cutting_total_all'] + $cutting_rank_3,
                    ]);
                    //领取用户对上级的分红贡献
                    db('users')->where(['user_id'=>$user_id])->update([
                        'three_parent_rebate'=>$userInfo['three_parent_rebate'] + $cutting_rank_3
                    ]);
                }

                //更新任务累计奖励、累计分红
                db('task')->where(['id'=>$task_id])->udpate([
                    'reward_total'=>$taskInfo['reward_total'] + $rewardValue,
                    'cutting_total'=>$taskInfo['cutting_total'] + $cutting_rank_1 + $cutting_rank_2 + $cutting_rank_3
                ]);
            }
            //更新用户任务状态（已返利）
            if($step == 3 ){
                //审核人
                if(empty($exam_user_id)){
                    $exam_user_id = $task_user_query->where(['id'=>$user_task_id])->field(['exam_user_id'])->find();
                    $task_user_query->where(['id'=>$user_task_id])->update([
                        'finish_qty' => 1,
                        'checkout_qty' => 1,
                        'status' => 3,
                        'exam_status' => 1,
                        'exam_user_id' => $exam_user_id['exam_user_id'],
                        'exam_time' => $time,
                        'finish_time' => $time,
                        'rebate_time' => $time
                    ]);
                }
            }
            $task_user_query->commit();
            return true;
        }catch (Exception $e){
            $task_user_query->rollback();
            return false;
        }
    }

    /**
     * 完成任务返利
     * @param int $user_id      用户ID
     * @param int $task_id      任务ID
     * @param int $user_task_id 用户领取任务ID
     * @param int $exam_user_id 审核人ID
     */
    public function rebate($user_id,$task_id,$user_task_id,$exam_user_id=0){
        if(empty($user_id) || empty($task_id) || empty($user_task_id)){
            return false;
        }
        //根据task_id获取任务信息
        $task_query = db('task');
        $taskInfo = $task_query->where(['id'=>$task_id])->field([
            'reward_value',
            'cutting_value',
            'cutting_rank_1',
            'cutting_rank_2',
            'cutting_rank_3',
            'reward_total',
            'cutting_total'
        ])->find();
        $time = time();//添加时间
        if(!empty($taskInfo)){
            try{
                $task_query->startTrans();
                //查询用户累计奖励、累计总奖励、上级信息
                $userInfo = db('users')->where(['user_id'=>$user_id])->find([
                    'user_rank',
                    'yi_currency_total',
                    'reward_total',
                    'reward_total_all',
                    'parent_id',
                    'two_parent_id',
                    'three_parent_id',
                    'parent_rebate',
                    'two_parent_rebate',
                    'three_parent_rebate'
                ])->find();
                //奖励，根据用户等级获取奖励系数
                $reward = db('user_rank')->where(['rank_id'=>$userInfo['user_rank']])->field(['reward_factor'])->find();
                $rewardFactor = empty($reward['reward_factor'])?0:$reward['reward_factor'];
                $rewardValue = intval($taskInfo['reward_value'] * $rewardFactor);//奖励值
                if($rewardValue >0 ){
                    //任务返利记录
                    db('task_rebate_log')->insert([
                        'task_user_id' => $user_task_id,
                        'user_id'      => $user_id,
                        'rebate_type'  => 1,
                        'reward_price' => $rewardValue,
                        'status'        => 1,
                        'add_time'      => $time,
                        'up_time'       => $time
                    ]);
                    //更新用户蚁币总额、累计奖励、累计总奖励
                    db('users')->where(['user_id' => $user_id])->update([
                        'yi_currency_total'=>$userInfo['yi_currency_total'] + $rewardValue,
                        'reward_total'=>$userInfo['reward_total'] + $rewardValue,
                        'reward_total_all'=>$userInfo['reward_total_all'] + $rewardValue
                    ]);
                }
                //分红
                //上级
                $parentInfo = db('users')->where(['user_id'=>$userInfo['parent_id']])->field([
                    'user_rank',
                    'yi_currency_total',
                    'cutting_week_total',
                    'cutting_total_all'
                ])->find();
                //根据用户等级获取分红系数
                $userRankInfo = db('user_rank')->where(['rank_id'=>$parentInfo['user_rank']])->field(['cutting_factor'])->find();
                $cuttingFactor = empty($userRankInfo['cutting_factor'])?0:$userRankInfo['cutting_factor'];
                $cutting_rank_1 = intval($taskInfo['cutting_value']* $taskInfo['cutting_rank_1'] * $cuttingFactor);
                if($cutting_rank_1 > 0){
                    db('task_rebate_log')->insert([
                        'task_user_id' => $user_task_id,
                        'user_id'       => $userInfo['parent_id'],
                        'rebate_type'  => 2,
                        'cutting_price' => $cutting_rank_1,
                        'cutting_rank' => 1,
                        'status'        => 1,
                        'add_time'      => $time,
                        'up_time'       => $time
                    ]);
                    //更新上级用户每周分红帐户、累积总分红账户
                    db('users')->where(['user_id' => $userInfo['parent_id']])->update([
                        'cutting_week_total' => $parentInfo['cutting_week_total'] + $cutting_rank_1,
                        'cutting_total_all' => $parentInfo['cutting_total_all'] + $cutting_rank_1
                    ]);
                    //领取用户对上级的分红贡献
                    db('users')->where(['user_id'=>$user_id])->update(['parent_rebate'=>$userInfo['parent_rebate'] + $cutting_rank_1]);
                }
                //上上级
                $twoParentInfo = db('users')->where(['user_id'=>$userInfo['two_parent_id']])->field([
                    'user_rank',
                    'yi_currency_total',
                    'cutting_week_total',
                    'cutting_total_all'
                ])->find();
                //根据用户等级获取分红系数
                $userRankInfo = db('user_rank')->where(['rank_id'=>$twoParentInfo['user_rank']])->field(['cutting_factor'])->find();
                $cuttingFactor = empty($userRankInfo['cutting_factor'])?0:$userRankInfo['cutting_factor'];
                $cutting_rank_2 = intval($taskInfo['cutting_value']* $taskInfo['cutting_rank_2'] * $cuttingFactor);
                if($cutting_rank_2 > 0){
                    db('task_rebate_log')->insert([
                        'task_user_id' => $user_task_id,
                        'user_id'       => $userInfo['two_parent_id'],
                        'rebate_type'  => 2,
                        'cutting_price' => $cutting_rank_2,
                        'cutting_rank' => 2,
                        'status'        => 1,
                        'add_time'      => $time,
                        'up_time'       => $time
                    ]);
                    //更新上上级用户每周分红帐户、累积总分红账户
                    db('users')->where(['user_id' => $userInfo['two_parent_id']])->update([
                        'cutting_week_total' => $parentInfo['cutting_week_total'] + $cutting_rank_2,
                        'cutting_total_all' => $parentInfo['cutting_total_all'] + $cutting_rank_2
                    ]);
                    //领取用户对上上级的分红贡献
                    db('users')->where(['user_id'=>$user_id])->update(['two_parent_rebate'=>$userInfo['two_parent_rebate'] + $cutting_rank_2]);
                }
                //上上上级
                $threeParentInfo = db('users')->where(['user_id'=>$userInfo['three_parent_id']])->field([
                    'user_rank',
                    'yi_currency_total',
                    'cutting_week_total',
                    'cutting_total_all'
                ])->find();
                //根据用户等级获取分红系数
                $userRankInfo = db('user_rank')->where(['rank_id'=>$threeParentInfo['user_rank']])->field(['cutting_factor'])->find();
                $cuttingFactor = empty($userRankInfo['cutting_factor'])?0:$userRankInfo['cutting_factor'];
                $cutting_rank_3 = intval($taskInfo['cutting_value']* $taskInfo['cutting_rank_2'] * $cuttingFactor);
                if($cutting_rank_3 > 0){
                    db('task_rebate_log')->insert([
                        'task_user_id' => $user_task_id,
                        'user_id'       => $userInfo['three_parent_id'],
                        'rebate_type'  => 2,
                        'cutting_price' => $cutting_rank_3,
                        'cutting_rank' => 3,
                        'status'        => 1,
                        'add_time'      => $time,
                        'up_time'       => $time
                    ]);
                    //更新上上上级用户每周分红帐户、累积总分红账户
                    db('users')->where(['user_id' => $userInfo['three_parent_id']])->update([
                        'cutting_week_total' => $parentInfo['cutting_week_total'] + $cutting_rank_3,
                        'cutting_total_all' => $parentInfo['cutting_total_all'] + $cutting_rank_3
                    ]);
                    //领取用户对上上上级的分红贡献
                    db('users')->where(['user_id'=>$user_id])->update(['three_parent_rebate'=>$userInfo['three_parent_rebate'] + $cutting_rank_3]);
                }
                //更新任务累计奖励、累计分红
                db('task')->where(['id'=>$task_id])->upate([
                    'reward_total'=>$taskInfo['reward_total'] + $rewardValue,
                    'cutting_total'=>$taskInfo['cutting_total'] + $cutting_rank_1 + $cutting_rank_2 + $cutting_rank_3
                ]);
                //更新用户任务状态（已返利）
                ddb('task_user')->where(['id'=>$user_task_id])->update([
                    'finish_qty'   => 1,
                    'checkout_qty' => 1,
                    'status'        => 3,
                    'exam_status'  => 1,
                    'exam_user_id' => $exam_user_id,
                    'exam_time'    => $time,
                    'finish_time'  => $time,
                    'rebate_time'  => $time
                ]);

                $task_query->commit();
                return true;
            }catch (Exception $e){
                $task_query->rollback();
                return false;
            }
        }else{
            return false;
        }
    }

} 
<?php
namespace app\common\model;
use think\Model;

class MTaskPromotion extends Model{
    
    /**
     * 根据ID获取一条记录
     * @param unknown $id
     * @return array|\think\db\false|PDOStatement|string|\think\Model
     */
    public function getOneById($id){
        $id = intval($id);
        return db('task_promotion')->where('id', $id)->find();
    }
    
    /**
     * 获取子模式及子模式的任务数
     * @param unknown $parent_id
     * @return \think\Collection|\think\db\false|PDOStatement|string
     */
    public function getChildListHasTask($parent_id, $limit = 999){
    	$list = db('task_promotion')->alias('tp')
    		->join('task t', "t.promotion_id = tp.id", 'left')
    		->where('tp.parent_id', $parent_id)
    		->where('t.status', 1)
    		->field('tp.*')
    		->group("tp.id")
    		->select()
    		;
    	return $list;
    }
    
    public function getAllChildListHasTask(){
    	$arr = db('task_promotion')->alias('tp')
    	->join('task t', "t.promotion_id = tp.id", 'left')
    	->where('tp.parent_id', '>', 0)
    	->where('t.status', 1)
    	->field('tp.*')
    	->order('sort_order')
    	->group("tp.id")
    	->select()
    	;
    	$list = [
    		1 =>[],
    		2 =>[],
    		3 =>[],
    		4 =>[],
    		5 =>[],
    		6 =>[],
    	];
    	foreach ($arr as $v){
    		$list[$v['parent_id']][] = $v;
    	}
    	return $list;
    }
}
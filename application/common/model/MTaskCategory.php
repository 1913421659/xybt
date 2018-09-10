<?php
namespace app\common\model;

use think\Model;

class MTaskCategory extends Model{
    
    /**
     * 获取树型分类树
     * @return \think\Collection|\think\db\false|PDOStatement|string
     */
    public function getTreeList(){
        $list = db('task_category')->where('is_show', 1)->order('parent_id')->order('sort_order')->select();
        array2tree($list);
        return $list;
    }
}


<?php

namespace app\common\model;
use think\Model;

/**
 * 
 * @author 
 *
 */
class MRegion extends Model {
	
	public function getTree($layer = 2){
		$list = db('region')->where('region_type','>',0)->where('region_type','<=', $layer)->order('region_type')->select();
		array2tree($list,'region_id');
		return $list;
	}
}


<?php
namespace app\common\media\attr ;
use \anywhere\MBase;
use \app\common\media\attr\group\Model as MMediaAttrGroup;

/**
 * 
 * @author 乌大湿 
 * @date 2017年12月13日
 * 神兽庇佑，bug無有~！
┏┛┻━━━┛┻┓
┃｜｜｜｜｜｜｜┃
┃　　　━　　　┃
┃　┳┛　┗┳　┃ 
┃　　　　　　　┃
┃　　　┻　　　┃
┃　　　　　　　┃
┗━┓　　　┏━┛
　　┃　　　┃
　　┃　　　┃
　　┃　　　┃
　　┃　　　┃
　　┃　　　┗━━━┓
　　┃　　　　　　　┣┓
　　┃　　　　　　　┃
　　┗┓┓┏━┳┓┏┛
　　　┃┫┫　┃┫┫
　　　┗┻┛　┗┻┛
 */

class Model extends MBase {
	public $table	= 'channel_media_attr'; //database table name
	public $pk		= 'id';  // primary key  of database table
	
	/**
	 * 
	 * @var VO
	 */
	static $vo = VO::class;
	
	/**
	 *
	 * @var Model
	 */
	static protected $instance = null;
	 	
	/**
	 * 获取某类型媒体资源的分类
	 * @param unknown $type_id
	 * @param number $limit
	 * @param unknown $vo_name
	 * @return unknown|unknown[]
	 * @author darkcloud.tan
	 */
	public function getCateListByTypeId($type_id, $limit = 99, $vo_name = null){
		$list = db($this->table)->alias('a')
		->join(MMediaAttrGroup::getInstance()->table . ' ag', 'a.group_id=ag.id', 'left')
		->where('ag.media_type_id',$type_id)
		->where('ag.id', $type_id * 100)
		->field('a.*')
		->order('a.sort_order')
		->limit($limit)
		->select();
		;
		return $this->arraySetVo($list, $vo_name);
	}
}

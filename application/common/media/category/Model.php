<?php
namespace app\common\media\category ;
use \anywhere\MBase;
/**
 * 
 * @author 乌大湿 
 * @date 2017年11月10日
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
	public $table	= 'channel_media_cat'; //database table name
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
	 * 获取某个类型下的分类
	 * @param number $type_id
	 * @return array
	 */
	public function getListByTypeId($type_id = 0){
		$rs = db($this->table)->where('parent_id', $type_id)
		->order('sort_order')->select();
		return $rs;
	}

	public function getTypeList(){
		return $this->getListByTypeId(0);
	}
}

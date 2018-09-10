<?php
namespace app\common\media\category ;
use \anywhere\VOBase;
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
 */class VO extends VOBase {
	public $id 	= null;
	public $name 	= null;
	public $parent_id 	= null;
	public $sort_order 	= null;

	static protected $db_fields = array(
		'id' 	=> 'id',
		'name' 	=> 'name',
		'parent_id' 	=> 'parent_id',
		'sort_order' 	=> 'sort_order'
	);
}
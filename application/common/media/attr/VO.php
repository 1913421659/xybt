<?php
namespace app\common\media\attr ;
use \anywhere\VOBase;
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
 */class VO extends VOBase {
	public $id 	= null;
	public $group_id 	= null;
	public $name 	= null;
	public $sort_order 	= null;

	static protected $db_fields = array(
		'id' 	=> 'id',
		'group_id' 	=> 'group_id',
		'name' 	=> 'name',
		'sort_order' 	=> 'sort_order'
	);
}
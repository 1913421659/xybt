<?php
namespace app\common\channel\company\category ;
use \anywhere\VOBase;
/**
 * 
 * @author 乌大湿 
 * @date 2017年12月14日
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
	public $category_name 	= null;
	public $is_show 	= null;
	public $sort_order 	= null;

	static protected $db_fields = array(
		'id' 	=> 'id',
		'category_name' 	=> 'category_name',
		'is_show' 	=> 'is_show',
		'sort_order' 	=> 'sort_order'
	);
}
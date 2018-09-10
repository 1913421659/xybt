<?php
namespace app\common\media\attr\group ;
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
	public $name 	= null;
	public $media_type_id 	= null;
	public $sort_order 	= null;
	public $url 	= null;

	static protected $db_fields = array(
		'id' 	=> 'id',
		'name' 	=> 'name',
		'media_type_id' 	=> 'media_type_id',
		'sort_order' 	=> 'sort_order',
		'url' 	=> 'url'
	);
}
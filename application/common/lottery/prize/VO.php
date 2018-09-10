<?php
namespace app\common\lottery\prize ;
use \anywhere\VOBase;
/**
 * 
 * @author 乌大湿 
 * @date 2018年02月01日
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
	public $awards_id 	= null;
	public $name 	= null;
	public $everybody_limit 	= null;
	public $inventory 	= null;
	public $link 	= null;
	public $sort_order 	= null;

	static protected $db_fields = array(
		'id' 	=> 'id',
		'awards_id' 	=> 'awards_id',
		'name' 	=> 'name',
		'everybody_limit' 	=> 'everybody_limit',
		'inventory' 	=> 'inventory',
		'link' 	=> 'link',
		'sort_order' 	=> 'sort_order'
	);
}
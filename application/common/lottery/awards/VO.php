<?php
namespace app\common\lottery\awards ;
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
	public $lottery_id 	= null;
	public $name 	= null;
	public $intro 	= null;
	public $inventory 	= null;
	public $sort_order 	= null;

	static protected $db_fields = array(
		'id' 	=> 'id',
		'lottery_id' 	=> 'lottery_id',
		'name' 	=> 'name',
		'intro' 	=> 'intro',
		'inventory' 	=> 'inventory',
		'sort_order' 	=> 'sort_order'
	);
}
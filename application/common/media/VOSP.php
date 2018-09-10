<?php
namespace app\common\media;
use \anywhere\VOBase;
/**
 * 
 * @author 乌大湿 
 * @date 2017年11月08日
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
 */class VOSP extends VOBase {
	public $id 	= null;
	public $type_id 	= 6;
	public $cat_id 	= null;
	public $old_id 	= null;
	public $enter_level 	= null;//入口级别
	public $enter 	= null;//入口位置
	public $link 	= null;
	public $time 	= null;
	public $price 	= null;
	public $history 	= null;
	public $site_name = null;//网站

	static protected $db_fields = array(
		'id' 	=> 'id',
		'type_id' 	=> 'type_id',
		'cat_id' 	=> 'cat_id',
		'old_id' 	=> 'old_id',
		'enter_level' 	=> 'title',
		'enter' 	=> 'title_sub',
		'link' 	=> 'link',
		'time' 	=> 'desc',
		'price' 	=> 'price_1',
		'history' 	=> 'history',
		'site_name' 	=> 'profession',
	);
}
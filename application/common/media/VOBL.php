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
 */class VOBL extends VOBase {
	public $id 	= null;
	public $type_id 	= 5;
	public $cat_id 	= null;
	public $old_id 	= null;
	public $site_name 	= null;
	public $nick_name 	= null;
	public $logo 	= null;
	public $qr_img 	= null;
	public $link 	= null;
	public $level 	= null;//级别：万、百万
	public $power 	= null;
	public $price 	= null;
	public $history 	= null;

	static protected $db_fields = array(
		'id' 	=> 'id',
		'type_id' 	=> 'type_id',
		'cat_id' 	=> 'cat_id',
		'old_id' 	=> 'old_id',
		'site_name' 	=> 'title',
		'nick_name' 	=> 'title_sub',
		'logo' 	=> 'logo',
		'qr_img' 	=> 'qr_img',
		'link' 	=> 'link',
		'level' 	=> 'desc',
		'power' 	=> 'power',
		'price' 	=> 'price_1',
		'history' 	=> 'history',
	);
}
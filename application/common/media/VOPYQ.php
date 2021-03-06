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
 */class VOPYQ extends VOBase {
	public $id 	= null;
	public $type_id 	= 4;
	public $cat_id 	= null;
	public $old_id 	= null;
	public $nickname 	= null;
	public $wx_id 	= null;
	public $logo 	= null;
	public $qr_img 	= null;
	public $link 	= null;
	public $desc 	= null;
	public $power 	= null;
	public $price 	= null;
// 	public $price_2 	= null;
// 	public $price_3 	= null;
	public $history 	= null;
	public $profession = null;//职业
	public $age = null;//年龄

	static protected $db_fields = array(
		'id' 	=> 'id',
		'type_id' 	=> 'type_id',
		'cat_id' 	=> 'cat_id',
		'old_id' 	=> 'old_id',
		'nickname' 	=> 'title',
		'wx_id' 	=> 'title_sub',
		'logo' 	=> 'logo',
		'qr_img' 	=> 'qr_img',
		'link' 	=> 'link',
		'description' 	=> 'description',
		'power' 	=> 'power',
		'price' 	=> 'price_1',
// 		'price_2' 	=> 'price_2',
// 		'price_3' 	=> 'price_3',
		'history' 	=> 'history',
		'profession' 	=> 'profession',
		'age' 	=> 'age'
	);
}
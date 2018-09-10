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
 */class VOWB extends VOBase {
	public $id 	= null;
	public $type_id 	= 2;
	public $cat_id 	= null;
	public $old_id 	= null;
	public $site_name 	= null;
	public $nickname= null;
	public $logo 	= null;
	public $link 	= null;
	public $desc 	= null;
	public $power 	= null;
	public $price_1 	= null;
	public $price_2 	= null;
	public $history 	= null;

	static protected $db_fields = array(
		'id' 	=> 'id',
		'type_id' 	=> 'type_id',
		'cat_id' 	=> 'cat_id',
		'old_id' 	=> 'old_id',
		'nickname' 	=> 'title_sub',
		'site_name' 	=> 'title',
		'logo' 	=> 'logo',
		'link' 	=> 'link',
		'description' 	=> 'description',
		'power' 	=> 'power',
		'price_1' 	=> 'price_1',
		'price_2' 	=> 'price_2',
		'history' 	=> 'history'
	);
}
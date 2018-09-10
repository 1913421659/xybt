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
 */class VO extends VOBase {
	public $id 	= null;
	public $type_id 	= null;
	public $cat_id 	= null;
	public $old_id 	= null;
	public $title 	= null;
	public $title_sub 	= null;
	public $logo 	= null;
	public $qr_img 	= null;
	public $link 	= null;
	public $desc 	= null;
	public $power 	= null;
	public $power_2 	= null;
    public $power_3 	= null;
	public $price_1 	= null;
	public $price_2 	= null;
	public $price_3 	= null;
	public $history 	= null;
	public $profession = null;//职业
	public $age = null;//年龄
    public $is_collect = 0;
    public $is_collect_name = '收藏';

	static protected $db_fields = array(
		'id' 	=> 'id',
		'type_id' 	=> 'type_id',
		'cat_id' 	=> 'cat_id',
		'old_id' 	=> 'old_id',
		'title' 	=> 'title',
		'title_sub' 	=> 'title_sub',
		'logo' 	=> 'logo',
		'qr_img' 	=> 'qr_img',
		'link' 	=> 'link',
		'description' 	=> 'description',
		'power' 	=> 'power',
		'power_2' 	=> 'power_2',
        'power_3'   => 'power_3',
		'price_1' 	=> 'price_1',
		'price_2' 	=> 'price_2',
		'price_3' 	=> 'price_3',
		'history' 	=> 'history',
		'profession' 	=> 'profession',
		'age' 	=> 'age'
	);
}
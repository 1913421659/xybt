<?php
namespace app\common\media\order ;
use \anywhere\VOBase;
/**
 * 
 * @author 乌大湿 
 * @date 2017年11月14日
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
 */
class VO extends VOBase {
	public $id 	= null;
	public $user_id 	= null;
	public $add_time 	= null;
	public $media_type_id 	= null;
	public $media_id 	= null;
	public $status 	= null;
	public $price_1 	= null;
	public $price_2 	= null;
	public $price_3 	= null;
	public $order_type 	= null;
	public $order_type_sub 	= null;
	public $price_type 	= null;
	public $price_sum 	= null;
	public $begin_date 	= null;
	public $begin_hour 	= null;
	public $begin_minute 	= null;
	public $timeout 	= null;
	public $days 	= null;
	public $kpi_time 	= null;
	public $title 	= null;
	public $cover 	= null;
	public $cover_in 	= null;
	public $desc 	= null;
	public $author 	= null;
	public $content 	= null;
	public $link 	= null;
	public $accessory 	= null;
	public $remarks 	= null;
	
	static protected $db_fields = array(
		'id' 	=> 'id',
		'user_id' 	=> 'user_id',
		'add_time' 	=> 'add_time',
		'media_type_id' 	=> 'media_type_id',
		'media_id' 	=> 'media_id',
		'status' 	=> 'status',
		'price_1' 	=> 'price_1',
		'price_2' 	=> 'price_2',
		'price_3' 	=> 'price_3',
		'order_type' 	=> 'order_type',
		'order_type_sub' 	=> 'order_type_sub',
		'price_type' 	=> 'price_type',
		'price_sum' 	=> 'price_sum',
		'begin_date' 	=> 'begin_date',
		'begin_hour' 	=> 'begin_hour',
		'begin_minute' 	=> 'begin_minute',
		'timeout' 	=> 'timeout',
		'days' 	=> 'days',
		'kpi_time' 	=> 'kpi_time',
		'title' 	=> 'title',
		'cover' 	=> 'cover',
		'cover_in' 	=> 'cover_in',
		'description' 	=> 'description',
		'author' 	=> 'author',
		'content' 	=> 'content',
		'link' 	=> 'link',
		'accessory' 	=> 'accessory',
		'remarks' 	=> 'remarks'
	);
}
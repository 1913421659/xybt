<?php
namespace app\common\channel\company ;
use \anywhere\VOBase;
/**
 * 
 * @author 乌大湿 
 * @date 2017年11月28日
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
	public $category_id 	= null;
	public $province_id 	= null;
	public $city_id 	= null;
	public $district_id 	= null;
	public $address 	= null;
	public $company_id 	= null;
	public $contact_name 	= null;
	public $contact_phone 	= null;
	public $is_auth 	= null;//认证，企业表取
	public $company_grade 	= null;
	public $price 	= null;
	public $order_total 	= null;
	public $content 	= null;
	public $add_time 	= null;
	public $company_head_portrait = null;//头像（企业）

	static protected $db_fields = array(
		'id' 	=> 'id',
		'name' 	=> 'name',
		'category_id' 	=> 'category_id',
		'province_id' 	=> 'province_id',
		'city_id' 	=> 'city_id',
		'district_id' 	=> 'district_id',
		'address' 	=> 'address',
		'company_id' 	=> 'company_id',
		'contact_name' 	=> 'contact_name',
		'contact_phone' 	=> 'contact_phone',
		'company_grade' 	=> 'company_grade',
		'price' 	=> 'price',
		'order_total' 	=> 'order_total',
		'content' 	=> 'content',
		'add_time' 	=> 'add_time'
	);
}
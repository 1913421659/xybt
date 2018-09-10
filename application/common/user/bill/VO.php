<?php
namespace app\common\user\bill ;
use \anywhere\VOBase;
/**
 * 
 * @author 乌大湿 
 * @date 2017年12月12日
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
	public $ordersn 	= null;
	public $order_serial_number 	= null;
	public $bill_type 	= null;
	public $recharge_type 	= null;
	public $company_id 	= null;
	public $user_id 	= null;
	public $add_type 	= null;
	public $money 	= null;
	public $status 	= null;
	public $add_time 	= null;
	public $finish_time 	= null;

	static protected $db_fields = array(
		'id' 	=> 'id',
		'ordersn' 	=> 'ordersn',
		'order_serial_number' 	=> 'order_serial_number',
		'bill_type' 	=> 'bill_type',
		'recharge_type' 	=> 'recharge_type',
		'company_id' 	=> 'company_id',
		'user_id' 	=> 'user_id',
		'add_type' 	=> 'add_type',
		'money' 	=> 'money',
		'status' 	=> 'status',
		'add_time' 	=> 'add_time',
		'finish_time' 	=> 'finish_time'
	);
}
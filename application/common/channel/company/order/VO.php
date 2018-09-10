<?php
namespace app\common\channel\company\order ;
use \anywhere\VOBase;
/**
 * 
 * @author 乌大湿 
 * @date 2017年12月14日
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
	public $name 	= null;
	public $price 	= null;
	public $qty 	= null;
	public $total_price 	= null;
	public $content 	= null;
	public $channel_company_id 	= null;
	public $company_id 	= null;
	public $user_id 	= null;
	public $status 	= null;
	public $add_time 	= null;
	public $finish_time 	= null;
    public $promotion_price = null;
    public $promotion_budget = null;

	static protected $db_fields = array(
		'id' 	=> 'id',
		'ordersn' 	=> 'ordersn',
		'name' 	=> 'name',
		'price' 	=> 'price',
		'qty' 	=> 'qty',
		'total_price' 	=> 'total_price',
		'content' 	=> 'content',
		'channel_company_id' 	=> 'channel_company_id',
		'company_id' 	=> 'company_id',
		'user_id' 	=> 'user_id',
		'status' 	=> 'status',
		'add_time' 	=> 'add_time',
		'finish_time' 	=> 'finish_time',
        'promotion_price'=>'promotion_price',
        'promotion_budget'=>'promotion_budget'
	);
}
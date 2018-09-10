<?php

namespace app\api\vo;

use anywhere\VOBase;

class VOUserBase extends VOBase {
	public $user_id 	= null;
	public $user_name 	= null;
	public $nick_name = null;
	public $mobile_phone 	= null;
	public $mobile_bind 	= null;
	public $user_picture 	= null;
	public $user_rank = null;
	public $yi_currency_total = null;//总额
	public $reward_total = null;//奖励
	public $cutting_total = null;//分红
	public $user_money = null;//余额
	public $alipay_bind = null;
	public $alipay_name = null;
	
	static protected $db_fields = array(
		'user_id' 	=> 'user_id',
		'user_name' 	=> 'user_name',
		'mobile_phone' 	=> 'mobile_phone',
		'mobile_bind' 	=> 'mobile_bind',
		'user_picture' 	=> 'user_picture',
		'user_rank' 	=> 'user_rank',
		'yi_currency_total' 	=> 'yi_currency_total',
		'reward_total' 	=> 'reward_total',
		'cutting_total' 	=> 'cutting_total',
		'user_money' 	=> 'user_money',
		'alipay_bind' 	=> 'alipay_bind',
		'alipay_name' 	=> 'alipay_name',
	);
}


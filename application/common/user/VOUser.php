<?php
namespace app\common\user ;
use \anywhere\VOBase;
/**
 * 
 * @author 乌大湿 
 * @date 2017年11月07日
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
 */class VOUser extends VOBase {
	public $user_id 	= null;
	public $aite_id 	= null;
	public $email 	= null;
	public $user_name 	= null;
	public $nick_name 	= null;
	public $password 	= null;
	public $question 	= null;
	public $answer 	= null;
	public $sex 	= null;
	public $birthday 	= null;
	public $user_money 	= null;
	public $frozen_money 	= null;
	public $pay_points 	= null;
	public $rank_points 	= null;
	public $yi_currency_total 	= null;
	public $reward_total 	= null;
	public $cutting_total 	= null;
	public $cutting_week_total 	= null;
	public $reward_total_all 	= null;
	public $cutting_total_all 	= null;
	public $sub_user_total 	= null;
	public $address_id 	= null;
	public $reg_time 	= null;
	public $last_login 	= null;
	public $last_time 	= null;
	public $last_ip 	= null;
	public $visit_count 	= null;
	public $user_rank 	= null;
	public $is_special 	= null;
	public $ec_salt 	= null;
	public $salt 	= null;
	public $parent_id 	= null;
	public $two_parent_id 	= null;
	public $three_parent_id 	= null;
	public $flag 	= null;
	public $alias 	= null;
	public $msn 	= null;
	public $qq 	= null;
	public $office_phone 	= null;
	public $home_phone 	= null;
	public $mobile_phone 	= null;
	public $mobile_bind 	= null;
	public $alipay_name 	= null;
	public $alipay_bind 	= null;
	public $bank_user 	= null;
	public $bank_name 	= null;
	public $bank_number 	= null;
	public $bank_bind 	= null;
	public $wechat_name 	= null;
	public $wechat_bind 	= null;
	public $is_validated 	= null;
	public $credit_line 	= null;
	public $passwd_question 	= null;
	public $passwd_answer 	= null;
	public $user_picture 	= null;
	public $old_user_picture 	= null;
	public $phone_device_id 	= null;
	public $phone_brand 	= null;
	public $phone_model 	= null;
	public $company_id 	= null;
	public $company_nick_name 	= null;
	public $identity_auth 	= null;
	public $profession_id 	= null;
	public $user_qrcode_path 	= null;
	public $parent_rebate 	= null;
	public $two_parent_rebate 	= null;
	public $three_parent_rebate 	= null;
	public $is_allow_agreement 	= null;
	public $allow_agreement_time 	= null;
	public $register_source 	= null;

	static protected $db_fields = array(
		'user_id' 	=> 'user_id',
		'aite_id' 	=> 'aite_id',
		'email' 	=> 'email',
		'user_name' 	=> 'user_name',
		'nick_name' 	=> 'nick_name',
		'password' 	=> 'password',
		'question' 	=> 'question',
		'answer' 	=> 'answer',
		'sex' 	=> 'sex',
		'birthday' 	=> 'birthday',
		'user_money' 	=> 'user_money',
		'frozen_money' 	=> 'frozen_money',
		'pay_points' 	=> 'pay_points',
		'rank_points' 	=> 'rank_points',
		'yi_currency_total' 	=> 'yi_currency_total',
		'reward_total' 	=> 'reward_total',
		'cutting_total' 	=> 'cutting_total',
		'cutting_week_total' 	=> 'cutting_week_total',
		'reward_total_all' 	=> 'reward_total_all',
		'cutting_total_all' 	=> 'cutting_total_all',
		'sub_user_total' 	=> 'sub_user_total',
		'address_id' 	=> 'address_id',
		'reg_time' 	=> 'reg_time',
		'last_login' 	=> 'last_login',
		'last_time' 	=> 'last_time',
		'last_ip' 	=> 'last_ip',
		'visit_count' 	=> 'visit_count',
		'user_rank' 	=> 'user_rank',
		'is_special' 	=> 'is_special',
		'ec_salt' 	=> 'ec_salt',
		'salt' 	=> 'salt',
		'parent_id' 	=> 'parent_id',
		'two_parent_id' 	=> 'two_parent_id',
		'three_parent_id' 	=> 'three_parent_id',
		'flag' 	=> 'flag',
		'alias' 	=> 'alias',
		'msn' 	=> 'msn',
		'qq' 	=> 'qq',
		'office_phone' 	=> 'office_phone',
		'home_phone' 	=> 'home_phone',
		'mobile_phone' 	=> 'mobile_phone',
		'mobile_bind' 	=> 'mobile_bind',
		'alipay_name' 	=> 'alipay_name',
		'alipay_bind' 	=> 'alipay_bind',
		'bank_user' 	=> 'bank_user',
		'bank_name' 	=> 'bank_name',
		'bank_number' 	=> 'bank_number',
		'bank_bind' 	=> 'bank_bind',
		'wechat_name' 	=> 'wechat_name',
		'wechat_bind' 	=> 'wechat_bind',
		'is_validated' 	=> 'is_validated',
		'credit_line' 	=> 'credit_line',
		'passwd_question' 	=> 'passwd_question',
		'passwd_answer' 	=> 'passwd_answer',
		'user_picture' 	=> 'user_picture',
		'old_user_picture' 	=> 'old_user_picture',
		'phone_device_id' 	=> 'phone_device_id',
		'phone_brand' 	=> 'phone_brand',
		'phone_model' 	=> 'phone_model',
		'company_id' 	=> 'company_id',
		'company_nick_name' 	=> 'company_nick_name',
		'identity_auth' 	=> 'identity_auth',
		'profession_id' 	=> 'profession_id',
		'user_qrcode_path' 	=> 'user_qrcode_path',
		'parent_rebate' 	=> 'parent_rebate',
		'two_parent_rebate' 	=> 'two_parent_rebate',
		'three_parent_rebate' 	=> 'three_parent_rebate',
		'is_allow_agreement' 	=> 'is_allow_agreement',
		'allow_agreement_time' 	=> 'allow_agreement_time',
		'register_source' 	=> 'register_source'
	);
}
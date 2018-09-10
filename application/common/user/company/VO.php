<?php
namespace app\common\user\company ;
use \anywhere\VOBase;
/**
 * 
 * @author 乌大湿 
 * @date 2017年12月21日
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
	public $company_name 	= null;
	public $company_short_name 	= null;
	public $company_head_portrait 	= null;
	public $company_money 	= null;
	public $qrcode_path 	= null;
	public $founder_user_id 	= null;
	public $legal_person 	= null;
	public $register_date 	= null;
	public $register_capital 	= null;
	public $website 	= null;
	public $staff_number 	= null;
	public $contact_name 	= null;
	public $contact_phone 	= null;
	public $contact_email 	= null;
	public $credit_code 	= null;
	public $license_path 	= null;
	public $ticket_company_name 	= null;
	public $company_account 	= null;
	public $company_bank_name 	= null;
	public $tax_number 	= null;
	public $ticket_phone 	= null;
	public $company_address 	= null;
	public $business_address 	= null;
	public $personal_alipay_name 	= null;
	public $personal_alipay_bind 	= null;
	public $personal_bank_user 	= null;
	public $personal_bank_name 	= null;
	public $personal_bank_number 	= null;
	public $personal_bank_bind 	= null;
	public $personal_wechat_name 	= null;
	public $personal_wechat_bind 	= null;
	public $is_auth 	= null;
	public $status 	= null;
	public $exam_admin_id 	= null;
	public $exam_time 	= null;
	public $remarks 	= null;
	public $add_time 	= null;

	static protected $db_fields = array(
		'id' 	=> 'id',
		'company_name' 	=> 'company_name',
		'company_short_name' 	=> 'company_short_name',
		'company_head_portrait' 	=> 'company_head_portrait',
		'company_money' 	=> 'company_money',
		'qrcode_path' 	=> 'qrcode_path',
		'founder_user_id' 	=> 'founder_user_id',
		'legal_person' 	=> 'legal_person',
		'register_date' 	=> 'register_date',
		'register_capital' 	=> 'register_capital',
		'website' 	=> 'website',
		'staff_number' 	=> 'staff_number',
		'contact_name' 	=> 'contact_name',
		'contact_phone' 	=> 'contact_phone',
		'contact_email' 	=> 'contact_email',
		'credit_code' 	=> 'credit_code',
		'license_path' 	=> 'license_path',
		'ticket_company_name' 	=> 'ticket_company_name',
		'company_account' 	=> 'company_account',
		'company_bank_name' 	=> 'company_bank_name',
		'tax_number' 	=> 'tax_number',
		'ticket_phone' 	=> 'ticket_phone',
		'company_address' 	=> 'company_address',
		'business_address' 	=> 'business_address',
		'personal_alipay_name' 	=> 'personal_alipay_name',
		'personal_alipay_bind' 	=> 'personal_alipay_bind',
		'personal_bank_user' 	=> 'personal_bank_user',
		'personal_bank_name' 	=> 'personal_bank_name',
		'personal_bank_number' 	=> 'personal_bank_number',
		'personal_bank_bind' 	=> 'personal_bank_bind',
		'personal_wechat_name' 	=> 'personal_wechat_name',
		'personal_wechat_bind' 	=> 'personal_wechat_bind',
		'is_auth' 	=> 'is_auth',
		'status' 	=> 'status',
		'exam_admin_id' 	=> 'exam_admin_id',
		'exam_time' 	=> 'exam_time',
		'remarks' 	=> 'remarks',
		'add_time' 	=> 'add_time'
	);
}
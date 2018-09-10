<?php

namespace app\common\vo;

use anywhere\VOBase;

/**
 * 企业用户（JS用） 
 * @author darkcloud.tan
 *
 */
class VOCompanyUser extends VOBase{
	public $user_id 		= null;
	public $user_name 		= null;
	public $nick_name 		= null;
	public $user_rank 		= null;
	public $user_rank_level = 0;
	public $user_picture 	= null;
	public $mobile_phone 	= null;
	public $is_manage = false;
	public $is_me = false; //是否当前登录的用户
	
}
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
 */class VOUserInvitation extends VOBase {
	public $id 	= null;
	public $user_id 	= null;
	public $code 	= null;
	public $title 	= null;
	public $content 	= null;
	public $register_show 	= null;
	public $download_show 	= null;
	public $download_link 	= null;
	public $is_oss_upload 	= null;
	public $version 	= null;
	public $download_total 	= null;
	public $up_time 	= null;

	static protected $db_fields = array(
		'id' 	=> 'id',
		'user_id' 	=> 'user_id',
		'code' 	=> 'code',
		'title' 	=> 'title',
		'content' 	=> 'content',
		'register_show' 	=> 'register_show',
		'download_show' 	=> 'download_show',
		'download_link' 	=> 'download_link',
		'is_oss_upload' 	=> 'is_oss_upload',
		'version' 	=> 'version',
		'download_total' 	=> 'download_total',
		'up_time' 	=> 'up_time'
	);
}
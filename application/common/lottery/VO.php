<?php
namespace app\common\lottery ;
use \anywhere\VOBase;
/**
 * 
 * @author 乌大湿 
 * @date 2018年02月01日
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
	public $title 	= null;
	public $begin 	= null;
	public $end 	= null;
	public $status 	= null;

	static protected $db_fields = array(
		'id' 	=> 'id',
		'title' 	=> 'title',
		'begin' 	=> 'begin',
		'end' 	=> 'end',
		'status' 	=> 'status'
	);
}
<?php
namespace app\common\lottery\user\awards ;
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
	public $lottery_id 	= null;
	public $add_time 	= null;
	public $user_id 	= null;
	public $awards_id 	= null;
	public $prize_id 	= null;
	public $prize_time 	= null;
	public $status 	= null;

	static protected $db_fields = array(
		'id' 	=> 'id',
		'lottery_id' 	=> 'lottery_id',
		'add_time' 	=> 'add_time',
		'user_id' 	=> 'user_id',
		'awards_id' 	=> 'awards_id',
		'prize_id' 	=> 'prize_id',
		'prize_time' 	=> 'prize_time',
		'status' 	=> 'status'
	);
}
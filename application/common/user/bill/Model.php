<?php
namespace app\common\user\bill ;
use \anywhere\MBase;
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
 */

class Model extends MBase {
	public $table	= 'user_bill_record'; //database table name
	public $pk		= 'id';  // primary key  of database table
	
	/**
	 * 
	 * @var \app\common\user\bill\VO
	 */
	static $vo = VO::class;
	
	/**
	 *
	 * @var Model
	 */
	static protected $instance = null;
	 	
}

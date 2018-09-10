<?php
namespace app\common\media\order ;
use \anywhere\MBase;
/**
 * 
 * @author 乌大湿 
 * @date 2017年11月14日
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

class Model extends MBase{
	public $table	= 'channel_media_order'; //database table name
	public $pk		= 'id';  // primary key  of database table
	
	/**
	 * 
	 * @var VO
	 */
	static $vo = VO::class;
	
	/**
	 *
	 * @var MBase
	 */
	static protected $instance = null;
	 	
}

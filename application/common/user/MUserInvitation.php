<?php
namespace app\common\user ;
use \anywhere\MBase;
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
 */

class MUserInvitation extends MBase {
	public $table	= 'user_invitation'; //database table name
	public $pk		= 'id';  // primary key  of database table
	
	/**
	 * 
	 * @var \app\common\user\VOUserInvitation
	 */
	static $vo = VOUserInvitation::class;
	
	/**
	 *
	 * @var \app\common\user\MUserInvitation
	 */
	static protected $instance = null;
	 	
	/**
	 * 
	 * @param unknown $user_id
	 * @return \app\common\user\VOUserInvitation|\anywhere\VOBase
	 */
	public function getOneByUserId($user_id){
		$data = db($this->table)->where('user_id', $user_id)->find();
		$vo = static::newVO();
		if($data){
			$vo->loadFromDBArray($data);
		}
		return $vo;
	}
}

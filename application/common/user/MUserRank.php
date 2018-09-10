<?php
namespace app\common\user ;
use \anywhere\MBase;
/**
 * 
 * @author 乌大湿 
 * @date 2017年11月03日
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

class MUserRank extends MBase {
	public $table	= 'user_rank'; //database table name
	public $pk		= 'rank_id';  // primary key  of database table
	
	/**
	 * 
	 * @var \app\common\user\VOUserRank
	 */
	static$vo = VOUserRank::class;
	
	/**
	 *
	 * @var \app\common\user\MUserRank
	 */
	static protected $instance = null;
	
	 	
	public function getAll(){
		$rs = db($this->table)->order('rank_id')->select();
		$list = [];
		if($rs){
			foreach($rs as $row){
				$vo = self::newVO();
				$vo->loadFromDBArray($row);
				$list[$row['rank_id']] = $vo;
			}
		}
		return $list;
	}
}

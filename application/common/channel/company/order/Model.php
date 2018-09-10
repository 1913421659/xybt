<?php
namespace app\common\channel\company\order ;
use \anywhere\MBase;
use app\common\channel\company\category\Model as MChannelCompanyCategory;
use app\common\channel\company\Model as MChannelCompany;
use anywhere\VOParams;
/**
 * 
 * @author 乌大湿 
 * @date 2017年12月14日
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
	public $table	= 'channel_company_order'; //database table name
	public $pk		= 'id';  // primary key  of database table
	
	/**
	 * 
	 * @var VO
	 */
	static $vo = VO::class;
	
	/**
	 *
	 * @var Model
	 */
	static protected $instance = null;
	 
	public function getList(VOParams &$params, $vo_name=null){
		$query = db($this->table)->alias('o')
		->join(MChannelCompany::getInstance()->table . ' c', 'c.id=o.channel_company_id', 'left')
		->field('o.*')
		->where($params->where)->order($params->order);
		if($params->page_info->page_size > 0){
			$list = $this->queryListWithPage($query, $params->page_info);
		}else{
			if($params->limit){
				$query->limit($params->limit);
			}
			$list = $query->select();
		}
		return $this->arraySetVo($list, $vo_name);
		
	}
}

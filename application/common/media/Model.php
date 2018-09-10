<?php
namespace app\common\media;
use \anywhere\MBase;
use anywhere\VOParams;
use app\common\media\category\MMediaInCategory;
use anywhere\FW;
use think\db\Query;
/**
 * 
 * @author 乌大湿 
 * @date 2017年11月08日
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
	public $table	= 'channel_media'; //database table name
	public $pk		= 'id';  // primary key  of database table
	
	/**
	 * 
	 * @var VO
	 */
	static$vo = VO::class;
	
	/**
	 *
	 * @var MMedia
	 */
	static protected $instance = null;
	 	
	public $cache_class = '';
	
	/**
	 * 
	 * @param VO|VOBBS $vo
	 */
	public function saveFromGather($vo){
		$data = $this->getOneByOldId($vo->old_id);
		if($data && $data->type_id == $vo->type_id){
			//更新
			db($this->table)->where([
				'old_id'=> $vo->old_id,
				'type_id'=>$vo->type_id
			])->update($vo->toDBArray());
		}else{
			$data = $this->getOneById($vo->id);
			db($this->table)->insert($vo->toDBArray());
		}
	}
	
	/**
	 * 使用源ID获取
	 * @param int $id
	 * @return NULL|\app\common\media\VO|\anywhere\VOBase
	 */
	public function getOneByOldId($id){
		$vo = null;
		$data = $id>0 ? db($this->table)->where('old_id', $id)->find() : null;
		if($data){
			$vo = static::newVO();
			$vo->loadFromDBArray($data);
		}
		return $vo;
	}
	
	/**
	 * 使用ID获取
	 * @param int $id
	 * @return NULL|\app\common\media\VO|\anywhere\VOBase
	 */
	public function getOneById($id){
		$vo = null;
		$data = $id>0 ? db($this->table)->where($this->pk, $id)->find() : null;
		if($data){
			$vo = static::newVO();
			$vo->loadFromDBArray($data);
		}
		return $vo;
	}
	
	/**
	 * 
	 * @param VOParams $params
	 * @return \app\common\media\VO[]|\anywhere\VOBase[]
	 */
	public function getList(VOParams &$params, $vo_name = null){
		$query = db($this->table)->alias('c')
		->join(MMediaInCategory::getInstance()->table .' cic', 'cic.media_id = c.id', 'left')
        ->join('channel_media_in_attr cmia','c.id=cmia.media_id','left');
		$query->where($params->where)->order($params->order);
		if($params->page_info->page_size > 0){
			$page_info = &$params->page_info;
			$page_info->total = $total = $query->group('id')->count();
			$page_info->end 	=  max(1, ceil($total / $page_info->page_size));//最后一页页码，也是总页数
			$page_info->prev 	= ($page_info->page > 1) ? $page_info->page- 1 : 1;//上一页页码
			$page_info->next 	= min($page_info->page+ 1, $page_info->end);//下一页页码
			$begin 				= $page_info->page_size * ($page_info->page- 1);
			$page_info->from 	= min($begin + 1, $total);//当前页第一个序号
			$page_info->to 		=  min($begin + $page_info->page_size, $total);//当前页最后一个序号
			$page_info->this_total = $page_info->to - $page_info->from + 1;	//当前页总数
			$query = db($this->table)->alias('c')->join('channel_media_in_cat cic', 'cic.media_id = c.id', 'left')
                    ->join('channel_media_in_attr cmia','c.id=cmia.media_id','left');
			$query->where($params->where)->order($params->order)->group('id');
			$list = $query->limit($begin, $page_info->page_size)->select();
		}else{
			if($params->limit){
				$query->limit($params->limit);
			}
			$list = $query->group('id')->select();
		}
		$return = [];
		if($list && is_array($list)){
			foreach($list as $k => $v){
				$return[$k] = static::newVO();
				$return[$k]->loadFromDBArray($v);
			}
		}
		return $return;
	}
	
	/**
	 * 分组获取某一类媒体的所有扩展属性
	 * @param integer $type_id 媒体类型（1~8）
	 * @return array [['id'=>{分组id},'name'=>{分组名称},'attr'=>[['id'=>{属性ID},'name'=>{属性名称}],...],...]
	 * @author darkcloud.tan
	 */
	public function getAllAttrByGroup($type_id){
		$list = [];
		$rs= db('channel_media_attr')->alias('t1')
		->join('channel_media_attr_group t2', 't2.id=t1.group_id', 'left')
		->where('t2.media_type_id', $type_id)
		->order('t2.sort_order')->order('t1.sort_order')
		->field('t1.id,t1.name,t1.group_id,t2.name group_name')
		->select();
		
		foreach($rs as $k => $v){
			$list[$v['group_id']]['id'] = $v['group_id'];
			$list[$v['group_id']]['name'] = $v['group_name'];
			$list[$v['group_id']]['attr'][] = [
				'id'=>$v['id'],
				'name'=>$v['name']
			];
		}
		
		return array_values($list);
	}

	public function getDetail($media_id){
		$row = db('channel_media')->where('id', $media_id)->find();
		$row['attr'] = db('channel_media_in_attr')->alias('t1')
		->join('channel_media_attr t2', 't2.id = t1.attr_id', 'left')
		->join('channel_media_attr_group t3', 't3.id = t2.group_id', 'left')
		->where('t1.media_id', $media_id)
		->group('t3.id')
		->field('t3.id,t3.name,group_concat(t2.name) "values"')
		->select()
		;
		return $row;
	}
}

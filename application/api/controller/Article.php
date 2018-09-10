<?php

namespace app\api\controller;

/**
 * 文章接口
 *
 */
class Article extends Api {
	
	/**
	 * 分页获取文章列表
	 * @author 谭武云
	 * @date 2017年9月13日
	 */
	public function paging(){
		$this->param()->sign();
		$param = request()->post();
		$mod = db('article')->field('article_id,cat_id,title,author,keywords,article_type,is_open,add_time,file_url,open_type,link,description');
		$cat_id= ker('cat_id', $param, 0);
		if(strpos($cat_id, ',') !== false){
			$id_list = explode(',', $cat_id);
			$mod->wherein('cat_id', $id_list);
		}elseif ($cat_id != 0){
			$mod->where('cat_id', $cat_id);
		}
		
		$page = ker('page', $param, 1);
		$limit = ker('limit', $param, 20);
		$page_size = ker('pagesize', $param, 0);
		if($page_size){
			$data = $mod->paginate($page_size, false, ['page'=>$page]);
		}else{
			$data = $mod->limit($limit)->select();
		}
		$this->back(1, $data);
	}
	
	/**
	 * 获取一篇文章
	 * 支持传入文章id或者分类id
	 * @author 谭武云
	 * @date 2017年9月13日
	 */
	public function getOne(){
		$this->param()->sign();
		$param = request()->post();
		$id = ker('id', $param, 0);
		$data = null;
		if($id){
			$data = db('article')->where('article_id', $id)->find();
		}else{
			$cat_id = ker('cat_id', $param, 0);
			if($cat_id){
				$data = db('article')->where('cat_id', $cat_id)->find();
			}
		}
		if($data){
			$this->back(1, $data);
		}else{
			$this->back(5);
		}
	}
}


<?php
namespace app\index\controller;

use \Exception;
use anywhere\FW;
use app\common\VORsAjax;
use anywhere\VOParams;
use app\common\media\Model as MMedia;
use app\common\media\category\VO as VOMediaCat;
use app\common\media\category\Model as MMediaCat;
use app\common\media\order\VO as VOOrder;

class Media extends Common{

	public function ajaxCatList(){
		$rs = VORsAjax::getInstance();
		try{
			$vo = VOMediaCat::getInstance();
			$vo->parent_id = FW::_POST('type_id', 0);
			$params = VOParams::getInstance();
			$params->where = $vo->toDBArray();
			$params->order = FW::_POST('order', '');
			$mod = MMediaCat::getInstance();
			$list = $mod->getList($params);
			$rs->data = $list;
			$rs->status = 1;
		}catch(\Exception $e){
			$rs->err = $e->getCode();
			$rs->msg = $e->getMessage();
		}
		$rs->outputJSON();
	}

	/**
	 * Ajax请求媒体列表
	 *
	 * @author darkcloud.tan
	 */
	public function ajaxList(){
		$rs = new VORsAjax();
		try{
			$type_id = FW::_POST('type_id', 1);
			$params = VOParams::getInstance();
			$params->where['type_id'] = $type_id;

            $att_arr = FW::_POST('attr',[]);
            if(!empty($att_arr)){
                foreach($att_arr as $k=>$v){
                    if($v==0){
                        unset($att_arr[$k]);
                    }
                }
                if(!empty($att_arr)){
                    $params->where['attr_id'] = ['in',$att_arr];
                }
            }

			$price_1 = FW::_POST('price_1', '');
			if($price_1){
				$p1 = explode('-', $price_1);
				if(isset($p1[0]) && $p1[0]>0){
					if(isset($p1[1]) && $p1[1]>0){
						$params->where['price_1'] = [['<=', $p1[1]],['>=', $p1[0]]];
					}else{
						$params->where['price_1'] = ['>=', $p1[0]];
					}
				}
			}
			$price_2 = FW::_POST('price_2', '');
			if($price_2){
				$p2 = explode('-', $price_2);
				if(isset($p2[0]) && $p2[0]>0){
					if(isset($p2[1]) && $p2[1]>0){
						$params->where['price_2'] = [['<=', $p2[1]],['>=', $p2[0]]];
					}else{
						$params->where['price_2'] = ['>=', $p2[0]];
					}
				}
			}
			$cat_id = FW::_POST('cat_id', 0);
			if($cat_id > 0){
				$params->where['cat_id'] = $cat_id;
			}
			//搜索
			$title = trim(FW::_POST('title', ''));
			$title_sub = trim(FW::_POST('title_sub', ''));
			if($title != ''){
				$params->where['title'] = ['like','%'.$title.'%'];
			}
			if($title_sub != ''){
				$params->where['title_sub'] = ['like','%'.$title_sub.'%'];
			}


            //排序
            $order_arr = [0,1,2,3,4];
            $order_by = FW::_POST('order_by');
            $order_by = !empty($order_by)?$order_by:5;
            if(in_array($order_by,$order_arr)){
                switch($order_by){
                    case 0:
                        $params->order = " id ASC ";break;//默认升序
                    case 1:
                        $params->order = " price_1 ASC ";break;//价格升序
                    case 2:
                        $params->order = " price_1 DESC ";break;
                    case 3:
                        $params->order = " price_2 ASC ";break;
                    case 4:
                        $params->order = " price_2 DESC ";break;
                }
            }else{
                $params->order = 'sort_order DESC ,price_1 DESC,price_2 DESC,price_3 DESC,history DESC,power DESC,id DESC ';
            }
			$page_size = 20;
			$page = FW::_POST('page', 1);
			$params->page_info->page_size = $page_size;
			$params->page_info->page = $page;
            $params->where['status'] = ['=',1];
			$model = MMedia::getInstance();
			$list = $model->getList($params);
            if(!empty($list)){
                foreach($list as $k=>$v){
                    if(!empty($this->user->user_id)){
                        $mediaCollect = db('channel_media_collect')->where(['media_id'=>$v->id,'user_id'=>$this->user->user_id])->find();
                        $v->is_collect = !empty($mediaCollect)?1:0;
                        $v->is_collect_name = !empty($mediaCollect)?'取消收藏':'收藏';
                        $v->title = !empty($v->title)?$v->title:'--';
                        $v->title_sub = !empty($v->title_sub)?$v->title_sub:'--';
                    }
                    $v->logo = get_image_path($v->logo);
                    $list[$k] = $v;
                }
            }
			$rs->data = [
				'list' => $list,
				'page_info' => $params->page_info
			];
			$rs->status = 1;
		}catch(\Exception $e){
			$rs->err = $e->getCode();
			$rs->msg = $e->getMessage();
		}
		$rs->outputJSON();
	}

	/**
	 * 检查用户是否登录
	 *
	 * @author darkcloud.tan
	 */
	private function checkUser(){
		if(!$this->user){
			$id = request()->param('id', 0);
			$from = urlencode(request()->path());
			$this->redirect('login/index', ['from' => $from]);
		}
	}

	/**
	 * 媒体列表页
	 * @return mixed|string
	 * @author darkcloud.tan
	 */
	public function index(){
		$tpl_list = [
			'',
			'forum_resources',
			'microblog_resources',
			'wechat_resources',
			'pyq_resources',
			'blog_resources',
			'video_resources',
			'live_resources',
			'top_resources'
		];
		$type_id = intval(request()->param('type', 1));
		if($type_id<1 || $type_id > 8){
			exit;
		}
		$this->assign('type_id', $type_id);
		$this->assign('title',['','论坛资源','微博资源','微信资源','朋友圈资源','博客资源','视频资源','直播资源','头条资源'][$type_id]);
		return $this->fetch();
		return $this->fetch($tpl_list[$type_id]);
	}

	/**
	 * 派单
	 * @param unknown $type_id
	 * @throws \Exception
	 */
	public function order(){
		$this->checkUser();
		$post = request()->post();
		if($post){
			$type_id = FW::_POST('media_type_id', 1);
			$rs = VORsAjax::getInstance();
			try{
				db()->startTrans();
				$vo = VOOrder::getInstance();
				$vo->add_time = time();
				$vo->type_id = $type_id;
				$vo->user_id = $this->user->user_id;
				$vo->loadFromArray($_POST);
				$price_sum_list = db('channel_media')
				->whereIn('id',FW::_POST('media_id'))
				->field('sum(price_1) p1, sum(price_2) p2, sum(price_3) p3')
				->find();
				if($vo->days < 1){
					throw new \Exception('参数错误：执行天数', 9001);
				}
				switch ($vo->price_type){
					case 1:
						$vo->price_sum = $price_sum_list['p1'] * $vo->days;
						break;
					case 2:
						$vo->price_sum = $price_sum_list['p2'] * $vo->days;
						break;
					case 3:
						$vo->price_sum = ($price_sum_list['p1'] + $price_sum_list['p2']) * $vo->days;
						break;
					case 4:
						$vo->price_sum = $price_sum_list['p3'] * $vo->days;
						break;
					default:
						throw new \Exception('参数错误：无效的订单类型');
						break;
				}
				$vo->timeout = date("Y-m-d H:i:s", time() + 3600 * $vo->timeout);

//				if($vo->price_sum > $this->user->user_money){
//					throw new \Exception('当前用户金额不足。', 9011);
//				}
				db('channel_media_order')->insert($vo->toDBArray());
//				db('users')
//				->where('user_id', $this->user->user_id)
//				->setDec('user_money', $vo->price_sum);
				db()->commit();
				$rs->status = 1;
				$rs->msg = '派单成功';
				$rs->data = [
					'url' => url('media/index',['type'=>$type_id])
				];
				// 				\anywhere\FW::debug($vo);exit;
			}catch(\Exception $e){
				db()->rollback();
				$rs->err = $e->getCode();
				$rs->msg = $e->getMessage();// . db()->getLastSql();
			}
			$rs->outputJSON();
		}else{
			try{
				$type_id = request()->param('type');
				//获取订单类型
				$order_type_list = db('channel_media_order_type')
					->where('media_type_id', $type_id)
					->order('sort_order')
					->select();
				if(!$order_type_list){
					$order_type_list = [];
				}
				$this->assign('order_type_list', $order_type_list);
				$mod_cat = MMediaCat::getInstance();
				$media_cat = $mod_cat->getOneByPrimaryKey($type_id);
				$this->assign('type_id', $type_id);
				$this->assign('media_cat', $media_cat->toArray());
				$id = request()->param('id', 0);
				$list = [];
				$media_ids = [];
				$model = MMedia::getInstance();
				$media = $model->getOneById($id);
				$list[] = $media->toArray();
				$media_ids[] = $media->id;
				$this->assign('list', $list);
				$this->assign('media_id', implode(',', $media_ids));

				$price_1_total = $price_2_total = $price_3_total = 0;
				foreach($list as $k => $v){
					$price_1_total += $v['price_1'];
					$price_2_total += $v['price_2'];
					$price_3_total += $v['price_3'];
				}
				$this->assign([
					'price_1_total' => $price_1_total,
					'price_2_total' => $price_2_total,
					'price_3_total' => $price_3_total,
				]);
			}catch(\Exception $e){
				\anywhere\FW::debug($e);
			}
			$page_list = [
				'',
				'forum_order',
				'microblog_order',
				'wechat_order',
				'pyq_order',
				'blog_order',
				'video_order',
				'live_order',
				'top_order',
			];
			return $this->fetch();
		}
	}

	/**
	 * 媒体详情页
	 * @return mixed|string
	 * @author darkcloud.tan
	 */
	public function detail(){
		$id = request()->param('id',0);
		$mod = MMedia::getInstance();
		$media = $mod->getOneById($id);
		if(!$media){
			echo '<script>history.go(-1);</script>';
			exit;
		}
		$mod_cat = MMediaCat::getInstance();
		$type = $mod_cat->getOneByPrimaryKey($media->type_id);
		$this->assign('type', $type);
//        print_r($type);die;
// 		FW::debug($type);exit;
		$this->assign('media', $media->toArray());
		$this->assign('id', request()->param('id',0));
		return $this->fetch();
	}

    /**
     * 媒体收藏  1取消收藏，0收藏
     */
    public function media_collect(){
        $media_id = input('media_id',0);
        $status = input('status',0);//1取消收藏，0收藏
        if(empty($this->user->user_id)){
            $this->returnJson(null,0,'请先登录');
        }
        $mediaInfo = db('channel_media')->where(['id'=>$media_id])->field('id')->find();
        if($media_id==0 || empty($mediaInfo)){
            $this->returnJson(null,0,'不存在的媒体ID');
        }
        if(!in_array($status,[0,1])){
            $this->returnJson(null,0,'错误的操作');
        }
        $mediaCollect = db('channel_media_collect')->where(['media_id'=>$media_id,'user_id'=>$this->user->user_id])->find();
        if($status==0){
            if(!empty($mediaCollect)){
                $this->returnJson(null,0,'已经收藏了');
            }else{
                $res = db('channel_media_collect')->insert([
                    'media_id'=>$media_id,
                    'user_id'=>$this->user->user_id,
                    'add_time'=>time()
                ]);
                if($res){
                    $this->returnJson(null,1,'收藏成功');
                }else{
                    $this->returnJson(null,0,'收藏失败');
                }
            }
        }else if($status==1){
            if(!empty($mediaCollect)){
                $res = db('channel_media_collect')->where([
                    'media_id'=>$media_id,
                    'user_id'=>$this->user->user_id
                ])->delete();
                if($res){
                    $this->returnJson(null,1,'取消收藏成功');
                }else{
                    $this->returnJson(null,0,'取消收藏失败');
                }
            }else{
                $this->returnJson(null,0,'还未收藏');
            }
        }
    }

    /**
     * 资源入驻（渠道+媒体）
     */
    public function settled(){
        $this->checkUser();
        $post = request()->post();
        if($post){
            $data = [
                'name'=>$post['name'],
                'channel_type'=>$post['channel_type'],
                'category_id'=>$post['type_id'],
                'province_id'=>$post['province'],
                'city_id'=>isset($post['city'])?$post['city']:0,
                'district_id'=>isset($post['district'])?$post['district']:0,
                'company_id'=>!empty($this->company->id)?$this->company->id:0,
                'user_id'=>$this->user->user_id,
                'contact_name'=>$post['contact_name'],
                'contact_phone'=>$post['contact_phone'],
                'content'=>format_bl2br(trim($post['content'])),
                'apply_time'=>time()
            ];
            $res = db('channel_apply')->insertGetId($data);
            if($res){
                $this->success('提交成功,请耐心等待审核！',url('index/index'));
            }else{
                $this->error('提交失败');
            }
        }else{
            $channelCategoryList = db('channel_company_category')->where(['is_show'=>1])->select();
            $mediaList = [
                ['id'=>'1','category_name'=>'论坛资源'],
                ['id'=>'2','category_name'=>'微博资源'],
                ['id'=>'3','category_name'=>'微信资源'],
                ['id'=>'4','category_name'=>'朋友圈资源'],
                ['id'=>'5','category_name'=>'博客资源'],
                ['id'=>'6','category_name'=>'视频资源'],
                ['id'=>'7','category_name'=>'直播资源'],
                ['id'=>'8','category_name'=>'头条资源']
            ];
            $this->assign([
                'channelCategoryList'=>$channelCategoryList,
                'mediaCategoryList'=>$mediaList
            ]);

            return $this->fetch();
        }
    }

    /**
     * 获取任务分类JS
     *
     * @author 谭武云
     *         @date 2017年9月20日
     */
    public function getRegionDataJs() {
        $province= db ( 'region' )->where ('parent_id <=1')->field('region_id,region_name')->select ();
        foreach ( $province as $k=>$v ) {
            if($v['region_id']==1) continue;
            $citys = db('region')->where(['parent_id'=>$v['region_id']])->field('region_id,region_name')->select();
            foreach($citys as $key=>$value){
                $districts = db('region')->where(['parent_id'=>$value['region_id']])->field('region_id,region_name')->select();
                $district[$value['region_id']] = array_column($districts,'region_name','region_id');
            }
            $city[$v['region_id']] = array_column($citys,'region_name','region_id');
        }

        $data = [
           'provinces' => array_column($province,'region_name','region_id'),
            'citys'=>$city,
            'districts'=>$district
        ];
        $js = '(function (factory) {
    if (typeof define === \'function\' && define.amd) {
        // AMD. Register as anonymous module.
        define(\'regions\', [], factory);
    } else {
        // Browser globals.
        factory();
    }
})(function () {

    var regions = ';
        $js .= json_encode ( $data );
        $js .= ';

    if (typeof window !== \'undefined\') {
        window.regions = regions;
    }

    return regions;

});
';
        die ( $js );
    }

}
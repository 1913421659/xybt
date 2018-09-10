<?php

namespace app\index\controller;

use app\common\model\MRegion;
use think\Controller;
use anywhere\FW;
use anywhere\VOParams;
use app\common\channel\company\Model as MChannelCompany;
use app\common\channel\company\order\Model as MChannelCompanyOrder;
use app\common\media\attr\Model as MMediaAttr;
use app\common\media\category\Model as MMediaCat;
use app\common\media\Model as MMedia;
use app\common\media\order\Model as MMediaOrder;
use app\common\user\company\Model as MUserCompany;
use app\common\user\bill\Model as MUserBill;
use app\common\VORsAjax;
use app\common\user\MUser;
use anywhere\VOPageInfo;

class Data extends Common{
	
	/**
	 * 获取营销渠道的分类
	 */
	public function channelCompanyCategoryList(){
		$rs = VORsAjax::getInstance();
		try{
			$list = db('channel_company_category')
			->where('is_show',1)
			->order('sort_order desc, id')->select();
			$rs->status = 1;
			$rs->data = $list;
		}catch(\Exception $e){
			$rs->err = $e->getCode();
			$rs->msg = $e->getMessage();
		}
		$rs->outputJSON();
	}

    //获取地区省级列表
    public function getRegion(){
        $rs = VORsAjax::getInstance();
        try{
            $data = (new MRegion())->getTree(1);;
            $rs->status = 1;
            $rs->data = $data;
        }catch(\Exception $e){
            $rs->err = $e->getCode();
            $rs->msg = $e->getMessage();
        }
        $rs->outputJSON();
    }
	
	/**
	 * 获取营销渠道列表
	 */
	public function channelCompanyList(){
		$rs = VORsAjax::getInstance();
		try{
			$query = db('channel_company')->alias('cc')
			->join('user_company uc','uc.id=cc.company_id', 'left')
			->field('cc.*,uc.is_auth,uc.company_head_portrait')
			->order('cc.sort_order DESC,cc.price_total DESC,cc.order_total DESC,cc.id DESC');
			//分类
			$category_id = intval(FW::_REQUEST('category_id', 0));
			if($category_id){
				$query->where('cc.category_id', $category_id);
			}
			//地区
			$province_id = intval(FW::_REQUEST('province_id', 0));
			if($province_id){
				$query->where('cc.province_id', $province_id);
			}
			//接单数
			$order_total = trim(FW::_REQUEST('order_total', ''));
			if($order_total){
				$_tmp = explode(',', $order_total);
				if(isset($_tmp[0])){
					$query->where('cc.order_total','>=', $_tmp[0]);
				}
				if(isset($_tmp[1])){
					$query->where('cc.order_total','<=', $_tmp[1]);
				}
				//$query->where('cc.order_total', ['between',explode(',', $order_total)]);
			}
			//接单金额
			$price= trim(FW::_REQUEST('price', ''));
			if($price){
				$_tmp = explode(',', $price);
				if(isset($_tmp[0])){
					$query->where('cc.price_total','>=', $_tmp[0]);
				}
				if(isset($_tmp[1])){
					$query->where('cc.price_total','<=', $_tmp[1]);
				}
				//$query->where('cc.price', ['between',$price]);
			}
			//认证
			$is_auth = FW::_REQUEST('is_auth', null);
			if($is_auth!==null && in_array($is_auth,[0,1,'0','1'])){
				$query->where('uc.is_auth', intval($is_auth));
			}
			
			//评星
			$company_grade = FW::_REQUEST('grade', null);
			if($company_grade!==null && in_array($company_grade,[0,1,2,3,4,5,'0','1','2','3','4','5'])){
				$query->where('cc.company_grade', intval($company_grade));
			}
			$page_info = VOPageInfo::getInstance();
			$page_info->page_size = FW::_REQUEST('page_size', 10);
			$page_info->page= FW::_REQUEST('page', 1);
			$query->where(['cc.status'=>1]);
			$data = $query->paginate($page_info->page_size, false,['page'=>$page_info->page]);
			$list =$data->items();
            if(!empty($list)){
                foreach($list as $k=>$v){
                    $list[$k]['company_head_portrait'] = get_image_path($v['company_head_portrait']);
                }
            }
			$page_info->total = $data->total();
			$rs->data = [
				'list' => $list,
				'page_info' => $page_info
			];
// 		}catch(PDOException $e){
// 			die(db()->getLastSql());
		}catch(\Exception $e){
// 			throw $e;
			$rs->err = $e->getCode();
			$rs->msg = $e->getMessage();
		}
		$rs->outputJSON();
	}
	
	public function channelCompanyOrderInfo(){
		$rs = VORsAjax::getInstance();
		try{
			$id = FW::_REQUEST('id', 0);
			$row = MChannelCompanyOrder::getInstance()->getOneByPrimaryKey($id);
			if(!$row){
				throw new \Exception('数据错误', 9000);
			}
			$row = $row->toArray();
			$row['channel'] = MChannelCompany::getInstance()->getOneByPrimaryKey($row['channel_company_id']);
			$row['from_user'] = MUser::getInstance()->getOneById($row['user_id']);
            $row['from_user']->user_picture = get_image_path($row['from_user']->user_picture);
			$row['from_company'] = $row['channel']->company_id>0
				? MUserCompany::getInstance()->getOneByPrimaryKey($row['channel']->company_id)
				: null;
			if(!$row['channel']){
				throw new \Exception('数据错误', 9001);
			}
			$row['channel'] = $row['channel']->toArray();
			$rs->data = $row;
		}catch(\Exception $e){
			$rs->err = $e->getCode();
			$rs->msg = $e->getMessage();
		}
		$rs->outputJSON();
	}
	
	/**
	 * 获取媒体资源类型列表
	 * 
	 * @author darkcloud.tan
	 * @date 2017-12-07
	 */
	public function channelMediaTypeList(){
		$rs = VORsAjax::getInstance();
		try{
			$mod = MMediaCat::getInstance();
			$rs->data = $mod->getTypeList();
			$rs->status = 1;
		}catch(\Exception $e){
			$rs->err = $e->getCode();
			$rs->msg = $e->getMessage();
		}
		$rs->outputJSON();
	}
	
	/**
	 * 获取媒体资源分类
	 */
	public function channelMediaCategoryList(){
		$rs = VORsAjax::getInstance();
		try{
			$type_id = FW::_REQUEST('type_id', 1);
			$limit = FW::_REQUEST('limit', 99);
			$mod = MMediaAttr::getInstance();
			$rs->data = $mod->getCateListByTypeId($type_id, $limit);
			$rs->status = 1;
		}catch(\Exception $e){
			$rs->err = $e->getCode();
			$rs->msg = $e->getMessage();
		}
		$rs->outputJSON();
	}
	
	/**
	 * 获取媒体渠道资源列表
	 */
	public function channelMediaList(){
		$rs = new VORsAjax();
		try{
			$post = request()->post();
			$model = MMedia::getInstance();
			$vo = MMedia::newVO();
			$vo->loadFromArray($post);
			$params = VOParams::getInstance();
			$params->limit = FW::_POST('limit', 8);
			$params->where = $vo->toDBArray();
            $params->where['status'] = ['=',1];
			$params->order = FW::_POST('order_by', 'sort_order DESC,price_1 DESC,price_2 DESC,price_3 DESC,history DESC,power DESC,id DESC');
			$list = $model->getList($params);
            if(!empty($list)){
                foreach($list as $k=>$v){
                    $v->logo = get_image_path($v->logo);
                    $list[$k] = $v;
                }
            }
			$rs->data = [
				'list'=>$list,
				'page_info'=>$params->page_info
			];
			$rs->status = 1;
		}catch(\Exception $e){
			// 			throw $e;
			$rs->err = $e->getCode();
			$rs->msg = $e->getMessage();
		}
		$rs->outputJSON();
	}
	
	/**
	 * 获取媒体订单（派单）
	 * @throws \Exception
	 * @author darkcloud.tan
	 */
	public function channelMedaiOrderList(){
		$rs = new VORsAjax();
		try{
			if($this->company == null){
				throw new \Exception('您还没加入或创建企业',__LINE__);
			}
			$post = request()->post();
			$where = [];
			$page_size = FW::_REQUEST('page_size', 0);
			$page = FW::_REQUEST('page',1);
			$params = VOParams::getInstance()
			->setWhere($where)
			->setLimit(FW::_POST('limit', 99))
			->setOrder(FW::_POST('order_by', 'id desc'))
			->setPaging($page_size, $page)
			;
			$list = MMediaOrder::getInstance()->getList($params);
			$rs->data = [
				'list'=>$list,
				'page_info'=>$params->page_info
			];
			$rs->status = 1;
		}catch(\Exception $e){
			// 			throw $e;
			$rs->err = $e->getCode();
			$rs->msg = $e->getMessage();
		}
		$rs->outputJSON();
	}
	
	/**
	 * 获取某个类型下扩展属性组和值
	 * 
	 * @author darkcloud.tan
	 */
	public function channelMediaAttrGroups(){
		$rs = VORsAjax::getInstance();
		try{
			$type_id = FW::_REQUEST('type_id', 1);
			$mod = MMedia::getInstance();
			$rs->data = $mod->getAllAttrByGroup($type_id);
			$rs->status = 1;
		}catch(\Exception $e){
			$rs->err = $e->getCode();
			$rs->msg = $e->getMessage();
		}
		$rs->outputJSON();
	}
	
	/**
	 * 获取某个媒体资源的数据
	 * 
	 * @author darkcloud.tan
	 */
	public function channelMediaDetail(){
		$rs = VORsAjax::getInstance();
		try{
			$media_id = FW::_REQUEST('id', 1);
			$mod = MMedia::getInstance();
			$rs->data = $mod->getDetail($media_id);
			$rs->status = 1;
		}catch(\Exception $e){
			$rs->err = $e->getCode();
			$rs->msg = $e->getMessage();
		}
		$rs->outputJSON();
	}
	
	/**
	 * 获取企业流水列表
	 * 支持POST提交的参数：
	 * company_id：企业ID号；bill_type：帐单类型1充值2收入3支出
	 * 
	 * @author darkcloud.tan
	 */
	public function companyBillList(){
		$rs = VORsAjax::getInstance();
		try{
			$company_id = FW::_REQUEST('company_id',$this->company['id']);
			$bill_type = FW::_REQUEST('bill_type', 1);//默认为1
            $month = FW::_REQUEST('month',date('Y-m'));
            if(empty($month)){
                $month = date('Y-m');
            }
			$mod = MUserBill::getInstance();
			$vo = MUserBill::newVO();
			$vo->company_id = $company_id;
			$vo->bill_type = $bill_type;
			$page_size = FW::_REQUEST('page_size', 10);
			$page = FW::_REQUEST('page', 1);
			$params = VOParams::getInstance();
			$params->setWhere($vo->toDBArray())
			->setOrder('add_time desc')
			->setPaging($page_size, $page);
            $params->where['add_time'] = [['<', strtotime('+1 month',strtotime($month))],['>=', strtotime($month)]];
            $params->where['status'] = 1;
			$list = $mod->getList($params);
			$rs->data = [
				'list' => $list,
				'page_info' => $params->page_info,
                'month'=>$month
			];
// 			$rs->data = db('user_bill_record')
// 			->where('company_id', $company_id)
// 			->order('add_time', 'desc')
// 			->select();
			$rs->status = 1;
		}catch(\Exception $e){
			$rs->err = $e->getCode();
			$rs->msg = $e->getMessage();
		}
		$rs->outputJSON();
	}
	
	public function userBillList(){
		$mod = new MUserBill();
		$params = VOParams::getInstance();
		
		
	}
	
	/**
	 * 获取企业信息
	 * @throws \Exception
	 * @author darkcloud.tan
	 */
	public function companyInfo(){
		$rs = VORsAjax::getInstance();
		try{
			$id = FW::_REQUEST('id', 0);
			$row = MUserCompany::getInstance()->getOneByPrimaryKey($id);
			if(!$row){
				throw new \Exception('数据错误', 9000);
			}
			$row = $row->toArray();
			
			$rs->data = $row;
		}catch(\Exception $e){
			$rs->err = $e->getCode();
			$rs->msg = $e->getMessage();
		}
		$rs->outputJSON();
	}

	public function test(){
	}
	
}
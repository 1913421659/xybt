<?php
namespace app\index\controller;

use app\common\VORsAjax;
use app\common\vo\VOCompanyUser;
use think\Exception;
use think\exception\ErrorException;
use think\exception\DbException;
use OSS\Core\OssException;
use app\common\model\MUserCompany;
use anywhere\FW;
use app\common\channel\company\category\Model as MChannelCompanyCategory;
use app\common\channel\Company\Model as MChannelCompany;
use app\common\channel\company\order\Model as MChannelCompanyOrder;
use anywhere\VOParams;
use app\common\user\MUser;

class Company extends Common{
	
	
	
	public function __construct(\think\Request $request = null){
		parent::__construct($request);
		if(!$this->user){
			$this->redirect('login/index', ['from' => urlencode(request()->path())]);
		}
// 		$this->assign('is_manage', false);
	}
	
	//企业首页/选择页
	public function index(){
		if($this->user->company_id > 0){
			$company = &$this->company;
			//用户数量
			$res = db('users')->where('company_id', $company['id'])->field('count(*) as total')->find();
			$res2 = db('user_company_apply')
				->where('company_id', $company['id'])
				->where('exam_status',0)
				->field('count(*) as total')
				->find();
			$this->assign([
				'company'=> $company,
				'user_number'=>$res['total'],
				'apply_number' =>$res2['total']
			]);
			$this->que_menu(['','企业首页', '']);
			if($company['status'] == 1){
				return $this->fetch('home');
			}else{
				return $this->fetch('moderated_create');
			}
		}else{
			$apply = db('user_company_apply')
				->where('user_id', $this->user->user_id)
				->where('exam_status', 0)
				->find();
			if($apply){
				$company = db('user_company')
				->where('id', $apply['company_id'])
				->find();
				$this->assign('apply_company', $company);
				return $this->fetch('moderated');
			}else{
				return $this->fetch('com_index');
			}
		}
	}

    //营销管理首页
    public function com_index(){
        if($this->user->company_id > 0){
            $companyInfo = db('user_company')
                ->where('id', $this->user->company_id)
                ->find();
            if($companyInfo['founder_user_id'] == $this->user->user_id){
                //商家需求
                $demandList = db('demand')->alias('d')
                    ->join('task t', "t.demand_id = d.id", 'left')
                    ->join('task_promotion tp', "tp.id=t.parent_promotion_id", 'left')
                    ->join('task_exam_time_type tett', "tett.id=t.exam_time_type_id", 'left')
                    ->join('users u','u.user_id=d.user_id','left')
                    ->where([
                        't.company_id'=> $this->user->company_id
                    ])
                    ->field('d.*,u.nick_name,t.collect_total,tp.promotion_name, t.demand_id,t.id as task_id,t.name as task_name,t.logo_img,t.brought_total,t.qty,t.cost_price,t.cost_unit_price,t.up_time')
                    ->order('add_time', 'desc')
                    ->limit(3)->select();
                //渠道派单
                $channelOrderList = db('channel_company_order')->alias('cco')
                        ->join('channel_company cc','cc.id = cco.channel_company_id','left')
                        ->join('users u','cco.user_id=u.user_id','left')
                        ->join('user_company uc','cc.company_id=uc.id','left')
                        ->join('channel_company_category ccc','ccc.id=cc.category_id','left')
                        ->where([
                            'cco.company_id'=>$this->user->company_id,
                        ])
                        ->field('cco.id,cco.add_time,cco.receiving_status,cco.receiving_channel_status,cco.promotion_budget,u.nick_name,uc.company_head_portrait,cc.name,ccc.category_name')
                        ->limit(3)->select();
//                print_r($channelOrderList);die;
                //优先待接单选项
                $channelGetOrderList = db('channel_company_order')->alias('cco')
                    ->join('channel_company cc','cc.id = cco.channel_company_id','left')
                    ->join('users u','cco.user_id=u.user_id','left')
                    ->join('user_company uc','cc.company_id=uc.id','left')
                    ->join('channel_company_category ccc','ccc.id=cc.category_id','left')
                    ->where([
                        'cco.company_id'=>$this->user->company_id,
                        'cco.receiving_status'=>1
                    ])
                    ->field('cco.id,cco.add_time,cco.receiving_status,cco.receiving_channel_status,cco.promotion_budget,u.nick_name,uc.company_head_portrait,cc.name,ccc.category_name')
                    ->order('cco.receiving_channel_status')
                    ->limit(3)->select();
            }else{
                //商家需求
                $demandList = db('demand')->alias('d')
                    ->join('task t', "t.demand_id = d.id", 'left')
                    ->join('task_promotion tp', "tp.id=t.parent_promotion_id", 'left')
                    ->join('task_exam_time_type tett', "tett.id=t.exam_time_type_id", 'left')
                    ->join('users u','u.user_id=d.user_id','left')
                    ->where([
                        'd.user_id'=> $this->user->user_id,
                    ])
                    ->field('d.*,u.nick_name,t.collect_total,tp.promotion_name, t.demand_id,t.id as task_id,t.name as task_name,t.logo_img,t.brought_total,t.qty,t.cost_price,t.cost_unit_price,t.up_time')
                    ->order('add_time', 'desc')
                    ->limit(3)->select();
                //渠道派单
                $channelOrderList = db('channel_company_order')->alias('cco')
                    ->join('channel_company cc','cc.id = cco.channel_company_id','left')
                    ->join('users u','cco.user_id=u.user_id','left')
                    ->join('user_company uc','cc.company_id=uc.id','left')
                    ->join('channel_company_category ccc','ccc.id=cc.category_id','left')
                    ->where(['cco.user_id'=>$this->user->user_id])
                    ->field('cco.id,cco.add_time,cco.receiving_status,cco.receiving_channel_status,cco.promotion_budget,u.nick_name,uc.company_head_portrait,cc.name,ccc.category_name')
                    ->limit(3)->select();

                //优先待接单选项
                $channelGetOrderList = db('channel_company_order')->alias('cco')
                    ->join('channel_company cc','cc.id = cco.channel_company_id','left')
                    ->join('users u','cco.user_id=u.user_id','left')
                    ->join('user_company uc','cc.company_id=uc.id','left')
                    ->join('channel_company_category ccc','ccc.id=cc.category_id','left')
                    ->where([
                        'cco.user_id'=>$this->user->user_id,
                        'cco.receiving_status'=>1
                    ])
                    ->field('cco.id,cco.add_time,cco.receiving_status,cco.receiving_channel_status,cco.promotion_budget,u.nick_name,uc.company_head_portrait,cc.name,ccc.category_name')
                    ->order('cco.receiving_channel_status')
                    ->limit(3)->select();
            }

        }else{
            //商家需求
            $demandList = db('demand')->alias('d')
                ->join('task t', "t.demand_id = d.id", 'left')
                ->join('task_promotion tp', "tp.id=t.parent_promotion_id", 'left')
                ->join('task_exam_time_type tett', "tett.id=t.exam_time_type_id", 'left')
                ->join('users u','u.user_id=d.user_id','left')
                ->where([
                    'd.user_id'=> $this->user->user_id,
                ])
                ->field('d.*,u.nick_name,t.collect_total,tp.promotion_name, t.demand_id,t.id as task_id,t.name as task_name,t.logo_img,t.brought_total,t.qty,t.cost_price,t.cost_unit_price,t.up_time')
                ->order('add_time', 'desc')
                ->limit(3)->select();
            //渠道派单
            $channelOrderList = db('channel_company_order')->alias('cco')
                ->join('channel_company cc','cc.id = cco.channel_company_id','left')
                ->join('users u','cco.user_id=u.user_id','left')
                ->join('user_company uc','cc.company_id=uc.id','left')
                ->join('channel_company_category ccc','ccc.id=cc.category_id','left')
                ->where(['cco.user_id'=>$this->user->user_id])
                ->field('cco.id,cco.add_time,cco.receiving_status,cco.receiving_channel_status,cco.promotion_budget,u.nick_name,uc.company_head_portrait,cc.name,ccc.category_name')
                ->limit(3)->select();

            //优先待接单选项
            $channelGetOrderList = db('channel_company_order')->alias('cco')
                ->join('channel_company cc','cc.id = cco.channel_company_id','left')
                ->join('users u','cco.user_id=u.user_id','left')
                ->join('user_company uc','cc.company_id=uc.id','left')
                ->join('channel_company_category ccc','ccc.id=cc.category_id','left')
                ->where([
                    'cco.user_id'=>$this->user->user_id,
                    'cco.receiving_status'=>1
                ])
                ->field('cco.id,cco.add_time,cco.receiving_status,cco.receiving_channel_status,cco.promotion_budget,u.nick_name,uc.company_head_portrait,cc.name,ccc.category_name')
                ->order('cco.receiving_channel_status')
                ->limit(3)->select();

        }
        $this->assign([
            'demandList'=>$demandList,
            'channelOrderList'=>$channelOrderList,
            'channelGetOrderList'=>$channelGetOrderList
        ]);
        return $this->fetch('com_index');
    }
	
	/**
	 *
	 */
	public function ajaxUserList(){
		$rs = new VORsAjax();
		try{
			$company = &$this->company;
			$post = request()->post();
			$page = isset($post['page']) ? intval($post['page']) : 1;
			$page_size = isset($post['page_size']) ? intval($post['page_size']) : 1;
			$list = db('users')->alias('u')
				->join('user_rank ur', 'ur.rank_id = u.user_rank', 'left')
				->where('u.company_id', $this->user->company_id)
				->field('u.*,ur.sort_order user_rank_level, ur.rank_name')
				->limit($page_size * ($page -1), $page_size)
				->select();
			if(!is_array($list)){
				throw new \Exception('获取失败', 9001);
			}
			$me_id = $this->user->user_id;
			array_walk($list, function(&$item)use($me_id,$company){
				$item['user_picture'] = get_image_path($item['user_picture']);
				$obj = new VOCompanyUser();
				$obj->loadFromArray($item);
				$obj->is_me = $item['user_id'] == $me_id;
				$obj->is_manage = $item['user_id'] == $company['founder_user_id'];
				$item = $obj->toArray();
			});
			$rs->data = $list;
			$rs->status = 1;
		}catch(\Exception $e){
			$rs->err = $e->getCode();
			$rs->msg = $e->getMessage();
			$rs->status = 0;
		}
		$rs->outputJSON();
	}
	
	/**
	 * ajax方式移除用户
	 * @throws \Exception
	 */
	public function ajaxUserRemove(){
		$rs = new VORsAjax();
		try{
			$company = &$this->company;
			if(!$company){
				throw new \Exception('企业不存在！', 9001);
			}
			if($company['founder_user_id'] != $this->user->user_id){
				throw new \Exception('没有权限', 9901);
			}
			$post = request()->post();
			$user_id = intval($post['user_id']);
			if($user_id <= 0){
				throw new \Exception('用户ID错误', 9002);
			}
			if($user_id == $company['founder_user_id']){
				throw new \Exception('不能移除管理员', 9902);
			}
			$user = db('users')->where('user_id', $user_id)->find();
			if(!$user){
				throw new \Exception('用户不存在', 9003);
			}
			if($user['company_id'] != $company['id']){
				throw new \Exception('用户不属性当前企业', 9004);
			}
			$res = db('task_user')->where('user_id', $user_id)->where('company_id', $company['id'])
				->field('count(*) as total')->find();
			if($res['total'] > 0){
				throw new \Exception('此员工需要转移任务才能移除', 9005);
			}
			db('users')->where('user_id', $user_id)->update(['company_id'=>0]);
			//（通过融云）发送消息通知用户。
			$rs->status = 1;
			
		}catch(ErrorException $e){
			$rs->status = 0;
			$rs->msg = '数据错误';
		}catch(\Exception $e){
			$rs->err = $e->getCode();
			$rs->msg = $e->getMessage();
			$rs->status = 0;
		}
		$rs->outputJSON();
	}
	
	//创建企业页
	public function create(){
		$post = request()->post();
		if($post){
			$rs = new VORsAjax();
			try{
				$mod = db('user_company');
				$post['founder_user_id'] = $this->user->user_id;
				$post['company_short_name'] = '';
				$post['remarks'] = '';
				$mod->insert($post);
				$id = $mod->getLastInsID();
				//生成二维码
				$qr = MUserCompany::createQRcode($id);
				if($qr){
					db('user_company')->where('id', $id)->update(['qrcode_path'=>$qr]);
				}
				$this->user->company_id = $id;
				db('users')->where('user_id', $this->user->user_id)->update(['company_id'=>$id]);
				session('user', $this->user);
				db('user_company_apply')->where('user_id', $this->user->user_id)->update(['exam_status'=>3]);
				$rs->status = 1;
			}catch(DbException $e){
				$rs->msg='数据错误';// . db()->getLastSql();
				$rs->status = 0;
			}catch(\think\Exception $e){
// 				$rs->data = db()->getLastSql();
				$rs->msg = '保存失败';
				$rs->status = 0;
			}catch(\Exception $e){
				$rs->data = var_export($e, 1);
				$rs->msg = $e->getMessage();
				$rs->status = 0;
			}
			$rs->outputJSON();
		}else{
			$this->que_menu();
			return $this->fetch();
		}
	}
	
	/**
	 * apply别名
	 * @return mixed|string
	 * @author darkcloud.tan
	 */
	public function apply_list(){
		return $this->apply();
	}
	
	/**
	 * 申请审核
	 * @return mixed|string
	 */
	public function apply(){
		$post = request()->post();
		if($post){
			$rs = new VORsAjax();
			try{
				$id = intval($post['id']);
				$status = intval($post['status']);
				if($id <= 0){
					throw new \Exception('数据错误', 9001);
				}
				$data = db('user_company_apply')->where('id', $id)->find();
				if(!$data){
					throw new \Exception('数据不存在', 9002);
				}
				if($data['exam_status'] != 0){
					throw new \Exception('状态异常', 9003);
				}
				if($data['company_id'] != $this->company['id']){
					throw new \Exception('无法审核本企业外的用户', 9004);
				}
				if($this->company['founder_user_id'] != $this->user->user_id){
					throw new \Exception('无权操作', 9005);
				}
				if(!in_array($status, [1,2])){
					throw new \Exception('状态异常', 9006);
				}
				db('user_company_apply')->where('id', $id)->update(['exam_status' => $status]);
				if($status == 1){
					db('users')->where('user_id', $data['user_id'])->update(['company_id' => $this->company['id']]);
				}
				$rs->status = 1;
			}catch(\Exception $e){
				$rs->msg = $e->getMessage();
				$rs->err = $e->getCode();
			}
			$rs->outputJSON();
		}else{
			//获取审核列表
			$res = db('user_company_apply')->alias('uca')
			->join('users u', 'u.user_id = uca.user_id', 'left')
			->join('user_rank ur', 'ur.rank_id = u.user_rank', 'left')
			->where('uca.company_id', $this->company['id'])
			->where('uca.exam_status', 'in', [0,1])
			->field('uca.id,uca.content,uca.exam_status,'.
					'u.user_id,u.nick_name,u.user_name,u.mobile_phone,u.user_picture,'.
					'ur.sort_order rank_level,ur.rank_name')
			->select();
			$list = [[],[]];
			foreach ($res as $k => $v){
				$res[$k]['user_picture'] = get_image_path($res[$k]['user_picture']);
				$list[$v['exam_status']][] = &$res[$k];
			}
			$this->assign('list', $list);
			$this->que_menu();
			return $this->fetch('apply_list');
		}
	}
	
	//绑定支付宝等
	public function bind(){
		$post = request()->post();
		if($post){
			$rs = new VORsAjax();
			try{
				$type = $post['type'];
				switch ($type){
					case 1 :
						$data = [
							'personal_alipay_bind' => 1,
							'personal_alipay_name' => $post['value']
						];
						break;
					case 2 :
						$data = [
							'personal_wechat_bind' => 1,
							'personal_wechat_name' => $post['value']
						];
						break;
					case 3 :
						$data = [
							'personal_bank_bind' => 1,
							'personal_bank_user' => $post['bank_user'],
							'personal_bank_name' => $post['bank_name'],
							'personal_bank_number' => $post['bank_number']
						];
						break;
					default :
						throw new \Exception('参数错误', 9000);
						break;
				}
				db('user_company')->where('id',$this->company['id'])->update($data);
				$rs->status = 1;
				
				
			}catch(\Exception $e){
				$rs->err = $e->getCode();
				$rs->msg = $e->getMessage();
				$rs->status = 0;
			}
			$rs->outputJSON();
		}else{
			$this->que_menu();
			return $this->fetch('bind');
		}
	}
	
	//我加入的企业 
	public function myCompany(){
		return $this->fetch('my_company');
	}
	
	//企业信息页
	public function information(){
		$company = &$this->company;
		if(!$company){
			$this->error('企业不存在');
			exit;
		}
		$company['license_path'] = get_image_path($company['license_path']);//oss路径
		$this->assign('company', $company);
		$this->que_menu();
		return $this->fetch();
	}
	
	//选择加入企业
	public function join(){
        if($this->company['id']){
            $this->redirect('company/index');
        }
        $applyInfo = db('user_company_apply')->where(['user_id'=>$this->user->user_id,'exam_status'=>0])->find();
        if($applyInfo){
            $this->redirect('company/index');
        }
		$this->que_menu();
		return $this->fetch('');
	}
	
	//员工管理
	public function users(){
		$this->que_menu();
		return $this->fetch('');
	}
	
	/**
	 * 查找企业
	 */
	public function ajaxSearch(){
		$rs = new VORsAjax();
		try{
			$key = request()->post('key');
			$query = db('user_company');
			if($key != ''){
				$query->where('company_name', 'like', "%$key%");
				$query->whereOr('company_short_name', 'like', "%$key%");
				$query->whereOr('id', 'like', "%$key%");
			}
			$list = $query->limit(100)->order("id", 'desc')
			->field('id, company_name, company_short_name, company_head_portrait')
			->select();
			if(is_array($list)){
				array_walk($list, function(&$value, $key){
					$value['company_head_portrait'] = get_image_path($value['company_head_portrait']);
				});
			}
			$rs->status = 1;
			$rs->data = $list;
		}catch(\Exception $e){
			$rs->status = 0;
			$rs->msg = $e->getMessage();
			$rs->err = $e->getCode();
		}
		$rs->outputJSON();
	}
	
	public function ajaxApply(){
		$rs = new VORsAjax();
		try{
			$post = request()->post();
			$company_id = intval($post['company_id']);
			if($company_id <= 0){
				throw new \Exception('企业不存在', 9001);
			}
			$content = $post['content'];
            $applyInfo = db('user_company_apply')->where(['user_id'=>$this->user->user_id,'exam_status'=>0])->find();
            if($applyInfo){
                throw new \Exception('只能申请一个企业', 9001);
            }
			$data = [
				'company_id'=>$company_id,
				'user_id' => $this->user->user_id,
				'content' => $content,
				'exam_status' => 0,
				'add_time' => time()
			];
			db('user_company_apply')->insert($data);
			$rs->status = 1;
            $rs->msg ='申请成功';
		}catch(\Exception $e){
			$rs->status = 0;
			$rs->msg = $e->getMessage();
			$rs->err = $e->getCode();
		}
		$rs->outputJSON();
	}

    //取消申请
    public function ajaxDeleApply(){
        $rs = new VORsAjax();
        try{
            $applyInfo = db('user_company_apply')->where(['user_id'=>$this->user->user_id,'exam_status'=>0])->find();
            if(!$applyInfo){
                throw new \Exception('申请不存在', 9001);
            }else if($applyInfo['exam_status']==1){
                throw new \Exception('该申请已经被同意', 9002);
            }else if($applyInfo['exam_status']==2){
                throw new \Exception('该申请已经被拒绝', 9003);
            }else if($applyInfo['exam_status']==3){
                throw new \Exception('该申请已经被取消', 9004);
            }
            $res = db('user_company_apply')->where(['id'=>$applyInfo['id']])->update(['exam_status'=>3]);
            $rs->status = $res;
            $rs->msg = '取消成功';
        }catch(\Exception $e){
            $rs->status = 0;
            $rs->msg = $e->getMessage();
            $rs->err = $e->getCode();
        }
        $rs->outputJSON();
    }
	
	public function ajaxSaveRemask(){
		$rs = new VORsAjax();
		try{
			$post = request()->post();
			$remarks = format_bl2br(trim($post['remarks']));
			db('user_company')->where('id', $this->company['id'])->update(['remarks'=>$remarks]);
			$rs->status = 1;
			$rs->msg = '保存成功';
			$rs->data = $remarks;
		}catch(\Exception $e){
			$rs->status = 0;
			$rs->msg = $e->getMessage();
			$rs->err = $e->getCode();
		}
		$rs->outputJSON();
	}
	/**
	 * 上传头像
	 */
	public function uploadhead(){
		$rs = new VORsAjax();
		try{
			$file = request()->file('file');
			$path = 'static/company_picture';
			$file_name = 'u_' . $this->company['id'] . date("_Ymd_") . rand(100,999);
			$info = $file->move(ROOT_PATH . 'public' . DS . $path, $file_name);
			if(!$info){
				throw new \Exception(__LINE__);
			}
			// 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
			$file_name = $info->getSaveName();
			$this->company['company_head_portrait'] = $path . '/' . $file_name;
			db('user_company')->where('id', $this->company['id'])
			->update(['company_head_portrait'=>$this->company['company_head_portrait']]);
			try{
				$file_tmp_path = ROOT_PATH . 'public/' .$this->company['company_head_portrait'];
				\FileUpload::upload($file_tmp_path, $this->company['company_head_portrait']);
			}catch (OssException $e) {
				// 					\anywhere\FW::debug($e);
				// 					return json(array('status'=>0,'errmsg'=>'上传失败' . $e->getMessage()));
			}
			$rs->status = 1;
			$rs->data = [
				'path' => $path
			];
		}catch(\Exception $e){
			$rs->status = 0;
			$rs->msg = '上传失败' . $e->getMessage();
		}
		$rs->outputJSON();
	}
	/**
	 * 我的任务
	 * @author 谭武云
	 * @date 2017年9月15日
	 * @return mixed|string
	 */
	public function myTask(){
		$t_id = input('key', 1, 'int');//顶级营销方式
		$top_prom_list = db('task_promotion')->where('parent_id', 0)->order('sort_order')
		->column('id,promotion_name,sort_order');
		if(isset($top_prom_list[$t_id])){
			$this_prom = $top_prom_list[$t_id];
		}else{
			$this_prom = current($top_prom_list);
		}
		$this->assign('this_prom', $this_prom);
		$this->assign('top_pro_list', $top_prom_list);
		$this->assign('key',$t_id);
		//收藏的任务列表
		$list = db("task_user")->alias('tu')->join("task t", "t.id=tu.task_id", 'left')
		->join("task_adv_packet tap", "tap.task_user_id = tu.id", 'left')
		->field('tu.*,'
				. 't.id,t.name,t.logo_img,t.parent_promotion_id,t.promotion_id,t.task_category_id,t.cost_price,'
				. 'tap.id as tap_id,tap.status as tap_status, tap.task_packet_id')
		->where([
			"tu.user_id"=>$this->user->user_id,
			"t.parent_promotion_id"=>$this_prom['id'],
			't.status'=>1
		])
		->group('tu.id')
		->select();
		$this->assign('list', $list);
		$this->assign('status_names',[
			'','已领取','完成','已返利','已取消'
		]);
		$this->getPromShortName();
		return $this->fetch();
	}	
//	我的需求
	public function myNeed(){
		$t_id = input('key', 1, 'int');
		//顶级营销方式
		$top_prom_list = db('task_promotion')->where('parent_id', 0)->order('sort_order')
		->column('id,promotion_name,sort_order');
		if(isset($top_prom_list[$t_id])){
			$this_prom = $top_prom_list[$t_id];
		}else{
			$this_prom = current($top_prom_list);
		}
		//获取需求
		$pages = db('demand')->alias('d')
		->join('task t', "t.demand_id = d.id", 'left')
		->join('task_promotion tp', "tp.id=t.parent_promotion_id", 'left')
		->join('task_exam_time_type tett', "tett.id=t.exam_time_type_id", 'left')
		->where([
			'd.user_id'=> $this->user->user_id,
			't.parent_promotion_id'=>$this_prom['id']
		])
		->field('d.*,tp.promotion_name, t.demand_id,t.id as task_id,t.name as task_name,t.logo_img,t.brought_total,t.qty,t.cost_price,t.cost_unit_price,t.exam_time_type_id, tett.name as exam_time_type_name')
		->order('add_time', 'desc')
		->paginate(10, null, ['page'=>input('page',1,'int')])->toArray();
		;
		$demand_list = $pages['data'];
		unset($pages['data']);
		$this->assign([
			'this_prom' => $this_prom,
			'top_pro_list' => $top_prom_list,
			'demand_list' => $demand_list,
			'pages' => $pages,
			'page_url_pre' => preg_replace("/\\.html$/i",'',Url('')) . '/page/',
		]);
		return $this->fetch();
	}
	
    //我的需求-》查看结果
    public function myNeedResults(){

        $id = input('id', 0, 'int');
        $page = input('page',0,'int');
        $pageSize = 20;
        //今天的数据
        $stat_date = input('stat_date',date('Y-m-d'));
//        $status = input('status',-1,'int');
        //获取需求
        $field = [
            'id',
            'name',
            'banner_img',
            'parent_promotion_id',
            'advertiser_id',
            'share_type',
            'budget',
            'qty',
            'cost_unit_price',
            'exam_type',
            'exam_time_type_id',
            'task_step_1_id',
            'task_step_2_id',
            'task_step_3_id',
            'add_time as release_time',
            'demand_id'
        ];
        $taskInfo = db('task')->where(['demand_id'=>$id])->field($field)->find();

        if(empty($id) || empty($taskInfo) || empty($taskInfo['parent_promotion_id'])){
            $this->error('任务不存在',url('/index/user/index'));
        }

//        //判断是否拥有权限
        if($taskInfo['advertiser_id'] !== $this->user->user_id){
            $user_company = db('user_company')->where(['founder_user_id'=>$this->user->user_id])->find();
            if(!empty($user_company)){
                $userC =db('users')->where(['user_id'=>$taskInfo['advertiser_id'],'company_id'=>$user_company['id']])->find();
                if(empty($userC)){
                    $this->error('没有权限',url('/index/user/index'));
                }
            }else{
                $this->error('没有权限',url('/index/user/index'));
            }
        }
        //广告图
        if(!empty($task_info['banner_img'])){
            $taskInfo['banner_img'] = get_image_path($taskInfo['banner_img']);
        }
        //发布人
        $advertiser = db('users')->where(['user_id'=>$taskInfo['advertiser_id']])->field('nick_name,company_nick_name')->find();
        $taskInfo['advertiser_name'] = empty($advertiser['company_nick_name'])?$advertiser['nick_name']:$advertiser['company_nick_name'];

        //审核时间
        if(!empty($taskInfo['exam_time_type_id'])){
            $exam_time = db('task_exam_time_type')->where(['id'=>$taskInfo['exam_time_type_id']])->field('name,time_total')->find();
            $taskInfo['exam_time'] = $exam_time['name'];
        }

        //完成数量
        $finish_count = db('task_user')->field('sum(qty) as sum')->where(['task_id'=>$taskInfo['id']])->where('status',['=',2],['=',3],'or')->find();
        $taskInfo['finish_qty'] = !empty($finish_count['sum'])?$finish_count['sum']:0;

        //剩余预算
        $taskInfo['surplus_budget'] = bcsub($taskInfo['budget'],$taskInfo['cost_unit_price'] * $taskInfo['finish_qty']);

        //xybt_task_adv_packet_stat_day
        $adv_stat_day = db('task_adv_packet_data')->alias('tapd')
                ->join('task_adv_packet tap','tapd.adv_packet_id = tap.id','left')
                ->join('task_packet tp','tap.task_packet_id=tp.id','left')
                ->join('task t','tp.task_id=t.id','left')
                ->where(['tp.advertiser_id'=>$this->user->user_id,'tapd.stat_date'=>$stat_date,'t.parent_promotion_id'=>$taskInfo['parent_promotion_id']])
                ->field('tapd.id,tapd.stat_date,tp.packet_number,t.init_price,tapd.checkout_qty,tapd.checkout_money,tapd.data_lock')
                ->select();

        $url = url('index/user/myneedresults',['id'=>$id]);
        $this->assign([
            'taskInfo' => $taskInfo,
            'adv_stat_day' => $adv_stat_day ,
            'stat_date'=>$stat_date,
            'url'=>$url,
            'id'=>$id
        ]);

//        return $this->fetch('myneed_results');
        return $this->fetch('netword_results');
    }
    
    /**
     * 6个顶级营销方式的缩短名称
     * @author 谭武云
     * @date 2017年9月15日
     * @return string[]
     */
    private function getPromShortName(){
    	$short = [
    		'1'=>'福利',
    		'2'=>'体验',
    		'3'=>'活动',
    		'4'=>'营销',
    		'5'=>'分销',
    		'6'=>'广告',
    	];
    	$this->assign('prom_short_name', $short);
    	return $short;
    }

    //付款项目
    public function pay_project(){

        return $this->fetch();
    }

    //付款项目详情
    public function pay_details(){

        return $this->fetch();
    }

    //收支记录
    public function income_record(){
        $page = input('pno',1,'int');
        $dateSearch = input('dateSearch',date('Y-m-d'));
        if(!strtotime($dateSearch)){
            $this->error('查询时间格式错误');
        }else{
            $dateSearch = date('Y-m-d',strtotime($dateSearch));
        }
        $startDate = strtotime($dateSearch);//当天开始时间
        $endDate = strtotime($dateSearch)+24*60*60;//当天结束时间
        $pageSize = 15;
        $company_info = db('user_company')->where(['founder_user_id'=>$this->user->user_id])->find();
        $arr = db('user_bill_record')->field([
            'id','ordersn','bill_type','recharge_type',
            'company_id','add_type','money','status','add_time'
        ])->where([
            'company_id'=>$company_info['id'],
            'status'=>1
        ])->where('add_time',['>=',$startDate],['<',$endDate])->paginate($pageSize, null, ['page'=>$page])->toArray();

        $this->assign([
            'list'=>$arr,
            'dateSearch'=>$dateSearch
        ]);
        return $this->fetch();
    }

    //收支详情
    public function income_details(){
        $bill_id = input('bill_id',0);
        $company_info = db('user_company')->where(['founder_user_id'=>$this->user->user_id])->find();
        $info = db('user_bill_record')->alias('b')->field([
            'b.id','b.ordersn','b.bill_type','b.recharge_type',
            'b.company_id','b.add_type','b.money','b.status','b.add_time',
            'u.nick_name'
        ])->join('users u','b.user_id = u.user_id','left')
        ->where([
            'b.company_id'=>$company_info['id'],'b.id'=>$bill_id
        ])->find();
        $this->assign('info',$info);
        return $this->fetch();
    }

    //支付管理
    public function payment(){
        $page = input('pno',1,'int');
        $pageSize = 15;
        $startDate = input('startDate',date('Y-m-d',strtotime('-7day')));
        $endDate = input('endDate',date('Y-m-d'));
        $status = input('status',-1);
        if(!strtotime($startDate) || !strtotime($startDate)){
            $this->error('查询时间格式错误');
        }else if(strtotime($startDate)>strtotime($endDate)){
            $this->error('开始时间不能大于结束时间');
        }else{
            $start = strtotime(date('Y-m-d',strtotime($startDate)));
            $end = strtotime($endDate)+24*60*60;
        }

        if($status>-1){
            switch($status){
                case 0:
                case 1:
                case 2: $pay_status=$status;break;
                case 3:
                case 4:
                case 5: $order_status = $status-3;break;
            }
        }

        $company_info = db('user_company')->where(['founder_user_id'=>$this->user->user_id])->find();
        $list = [
            'total'=>0,
            'per_page'=>$pageSize,
            'current_page'=>$page,
            'last_page'=>1,
            'data'=>[]
        ];
        if(!empty($company_info)){
            $listDb = db('channel_media_order')->alias('mo')->field([
                'u.user_picture','u.nick_name','u.user_id','mo.price_sum','mo.add_time','mo.status','mo.id,mo.pay_status'
            ])->join('channel_media m','mo.media_id = m.id','left')
            ->join('users u','u.user_id = mo.user_id','left')
            ->where([
                'u.company_id'=>$company_info['id']
            ])->where('mo.add_time',['>=',$start],['<',$end]);
            if(isset($pay_status)){
                $listDb = $listDb->where(['mo.pay_status'=>$pay_status]);
            }
            if(isset($order_status)){
                $listDb = $listDb->where(['mo.status'=>$order_status,'mo.pay_status'=>1]);
            }
            $list = $listDb->order('mo.pay_status ASC')->paginate($pageSize, null, ['page'=>$page])->toArray();
            foreach($list['data'] as $k=>$v){
                //0待支付，1支付，2拒绝支付，3派单中，4接受派单，5拒绝接单，6超时失效
                if($v['pay_status']==1){
                    $list['data'][$k]['status'] = $v['status']+3;
                }else{
                    $list['data'][$k]['status'] = $v['pay_status'];
                }
                $list['data'][$k]['status_name'] = $this->getMediaOrderStatusName($list['data'][$k]['status']);
            }
        }

        $this->assign([
            'list'=>$list,
            'startDate'=>$startDate,
            'endDate'=>$endDate
        ]);
        return $this->fetch();
    }

    //支付订单详情
    public function payment_details(){
        $order_id = input('order_id',0);
        $company_info = db('user_company')->where(['founder_user_id'=>$this->user->user_id])->find();
        $info = db('channel_media_order')->alias('mo')->field([
            'u.user_picture','u.nick_name','u.user_id','mo.price_sum','mo.add_time','mo.status','mo.id',
            'mo.title as order_title','mo.remarks as order_remarks','mo.media_id','m.title as media_title',
            'mo.order_type','mo.link','m.type_id','mot.name as order_type_name,mo.pay_status'
        ])->join('channel_media m','mo.media_id = m.id','left')
            ->join('users u','u.user_id = mo.user_id','left')
            ->join('channel_media_order_type mot','mo.order_type=mot.order_type_id AND mot.media_type_id=mo.media_type_id')
            ->where([
                'u.company_id'=>$company_info['id'],
                'mo.id'=>$order_id
            ])->find();
        if(empty($order_id) || empty($info)) {
            $this->error('该派单不存在',url('/index/company/payment'));
        }
        $info['media_type_name'] = $this->getMediaTypeName($info['type_id']);
        //0待支付，1支付，2拒绝支付，3派单中，4接受派单，5拒绝接单，6超时失效
        if($info['pay_status']==1){
            $info['status'] = $info['status']+3;
        }else{
            $info['status'] = $info['pay_status'];
        }
        $info['status_name'] = $this->getMediaOrderStatusName($info['status']);
        $this->assign([
            'info'=>$info
        ]);
        return $this->fetch();
    }

    /**
     * 媒体派单支付审核
     */
    public function media_order_pay_exam(){
        $order_id = input('order_id',0);
        $pay_status = input('status',0);
        $company_info = db('user_company')->where(['founder_user_id'=>$this->user->user_id])->find();
        if(empty($company_info)){
            $this->returnJson(null,0,'无审核权限');
        }
        $info = db('channel_media_order')->alias('mo')->field([
            'mo.*'
        ])->join('channel_media m','mo.media_id = m.id','left')
            ->join('users u','u.user_id = mo.user_id','left')
            ->where([
                'u.company_id'=>$company_info['id'],
                'mo.id'=>$order_id
            ])->find();
        if($order_id==0 || empty($info)){
            $this->returnJson(null,0,'派单不存在');
        }
        if($info['pay_status']!=0){
            $this->returnJson(null,0,'派单已被审核');
        }
        $res = $this->updateMediaOrderPay($info,$pay_status);
        if($res){
            $this->returnJson(null,1,'操作成功');
        }else{
            $this->returnJson(null,0,'操作失败');
        }
    }

    /**
     * 支付审核
     */
    private function updateMediaOrderPay($orderInfo,$status){
        if($status==2){
            return db('channel_media_order')
                ->where(['id'=>$orderInfo['id']])
                ->update(['pay_status'=>$status]);
        }else if($status==1){
            $orderDB = db('channel_media_order');
            $orderDB->startTrans();
            $res = $orderDB->where(['id'=>$orderInfo['id']])
                    ->update(['pay_status'=>$status]);
            if($res){
                if($orderInfo['company_id']==0){
                    $tab = 'users';
                    $c = 'user_money';
                    $condition = ['user_id'=>$orderInfo['user_id']];
                }else{
                    $tab = 'user_company';
                    $c ='company_money';
                    $condition = ['id'=>$orderInfo['company_id']];
                }
                $r = db($tab)->where($condition)->setDec($c,$orderInfo['price_sum']);//扣款操作
                $this->insertUserBillRecord([
                    'ordersn'=>$this->guid(),
                    'bill_type'=>3,//支出
                    'company_id'=>$orderInfo['company_id'],
                    'user_id'=>$orderInfo['user_id'],
                    'add_type'=>2,
                    'money'=>$orderInfo['price_sum'],
                    'add_time'=>time()
                ]);
                if($r){
                    $orderDB->commit();
                    return true;
                }else{
                    $orderDB->rollback();
                    return false;
                }
            }else{
                $orderDB->rollback();
                return false;
            }
        }
    }

    //返回媒体八大类型对应id的名称
    private function getMediaTypeName($type_id){
        if(!in_array($type_id,[1,2,3,4,5,6,7,8])) return '';
        $arr = ['','论坛资源','微博资源','微信资源','朋友圈资源','博客资源','视频资源','直播资源','头条资源'];
        return $arr[$type_id];
    }

    //返回订单状态所对应的名称 1派单状态对应名称；2接单状态对应名称
    private function getMediaOrderStatusName($status,$type=1){
        if($type==1){
            $arr = ['待支付','支付成功','拒绝支付','派单中','接收派单','拒绝派单','超时失效'];
            return $arr[$status];
        }else if($type==2){
            $arr = ['待接单','已接单','已拒单'];
            return $arr[$status];
        }
    }

    private function insertUserBillRecord($data){
        if(empty($data)) return false;
        return db('user_bill_record')->insert($data);
    }

    /**
     * 媒体派单列表页
     */
    public function media_dispatch(){
        $page = input('pno',1,'int');
        $pageSize = 15;
        $startDate = input('startDate',date('Y-m-d',strtotime('-7day')));
        $endDate = input('endDate',date('Y-m-d'));
        $status = input('status',-1);
        $type_id = input('type_id',1);
        if(!in_array($type_id,[1,2,3,4,5,6,7,8])){
            $this->error('错误的分类');
        }
        if(!strtotime($startDate) || !strtotime($startDate)){
            $this->error('查询时间格式错误');
        }else if(strtotime($startDate)>strtotime($endDate)){
            $this->error('开始时间不能大于结束时间');
        }else{
            $start = strtotime(date('Y-m-d',strtotime($startDate)));
            $end = strtotime($endDate)+24*60*60;
        }
        if($status>-1){
            switch($status){
                case 0:
                case 1:
                case 2: $pay_status=$status;break;
                case 3:
                case 4:
                case 5: $order_status = $status-3;break;
            }
        }
        $company_info = db('user_company')->where(['founder_user_id'=>$this->user->user_id])->find();
        if(!empty($company_info) && !empty($company_info['id'])){
            $listDb = db('channel_media_order')->alias('mo')->field([
                'u.user_picture','u.nick_name','u.user_id','mo.price_sum','mo.add_time','mo.status','mo.id,mo.pay_status'
            ])->join('channel_media m','mo.media_id = m.id','left')
                ->join('users u','u.user_id = mo.user_id','left')
                ->where('mo.add_time',['>=',$start],['<',$end])
                ->where(['m.type_id'=>$type_id,'u.company_id'=>$company_info['id']]);
        }else{
            $listDb = db('channel_media_order')->alias('mo')->field([
                'u.user_picture','u.nick_name','u.user_id','mo.price_sum','mo.add_time','mo.status','mo.id,mo.pay_status'
            ])->join('channel_media m','mo.media_id = m.id','left')
                ->join('users u','u.user_id = mo.user_id','left')
                ->where('mo.add_time',['>=',$start],['<',$end])
                ->where('u.user_id='.$this->user->user_id)
                ->where(['m.type_id'=>$type_id]);
        }

        if(isset($pay_status)){
            $listDb = $listDb->where(['mo.pay_status'=>$pay_status]);
        }
        if(isset($order_status)){
            $listDb = $listDb->where(['mo.status'=>$order_status,'mo.pay_status'=>1]);
        }

        $list = $listDb->paginate($pageSize, null, ['page'=>$page])->toArray();
        foreach($list['data'] as $k=>$v){
            //0待支付，1支付，2拒绝支付，3派单中，4接受派单，5拒绝接单，6超时失效
            if($v['pay_status']==1){
                $list['data'][$k]['status'] = $v['status']+3;
            }else{
                $list['data'][$k]['status'] = $v['pay_status'];
            }
            $list['data'][$k]['status_name'] = $this->getMediaOrderStatusName($list['data'][$k]['status']);
        }


        $this->assign([
            'list'=>$list,
            'startDate'=>$startDate,
            'endDate'=>$endDate,
            'status'=>$status,
            'type_id'=>$type_id
        ]);
        return $this->fetch();
    }

    /**
     * 媒体派单详情
     */
    public function media_dispatch_details(){
        $id = intval(input('id',0));
        $company_info = db('user_company')->where(['founder_user_id'=>$this->user->user_id])->find();
        $companyId = !empty($company_info)?$company_info['id']:0;
        $mediaInfo = db('channel_media_order')->alias('mo')->field([
            'u.user_picture','u.nick_name','u.user_id','mo.price_sum','mo.add_time','mo.status',
            'mo.id','mo.pay_status','m.title as media_title','m.title_sub as media_title_sub','m.id as media_id',
            'mo.title as order_title','mot.name as order_type_name','mo.link','mo.remarks'
        ])->join('channel_media m','mo.media_id = m.id','left')
            ->join('users u','u.user_id = mo.user_id','left')
            ->join('channel_media_order_type mot','mo.order_type=mot.order_type_id AND mot.media_type_id = mo.media_type_id','left')
            ->where(['mo.id'=>$id])
            ->find();
        //0待支付，1支付，2拒绝支付，3派单中，4接受派单，5拒绝接单，6超时失效
        if($mediaInfo['pay_status']==1){
            $mediaInfo['status'] = $mediaInfo['status']+3;
        }else{
            $mediaInfo['status'] = $mediaInfo['pay_status'];
        }
        $mediaInfo['status_name'] = $this->getMediaOrderStatusName($mediaInfo['status']);
        $this->assign([
            'info'=>$mediaInfo
        ]);
        return $this->fetch();
    }

    /**
     * 渠道派单列表页
     * @return mixed|string
     * @author darkcloud.tan
     */
    public function channel_dispatch(){
    	//分类
        $startDate = input('startDate',date('Y-m-d',strtotime('-7 day')));
        $endDate = input('endDate',date('Y-m-d'));
        $status = input('status',-1);
        $this->assign([
            'startDate'=>$startDate,
            'endDate'=>$endDate,
            'status'=>$status
        ]);

        if(!strtotime($startDate) || !strtotime($startDate)){
            $this->error('查询时间格式错误');
        }else if(strtotime($startDate)>strtotime($endDate)){
            $this->error('开始时间不能大于结束时间');
        }else{
            $start = strtotime(date('Y-m-d',strtotime($startDate)));
            $end = strtotime($endDate)+24*60*60;
        }

    	$mod_cat = MChannelCompanyCategory::getInstance();
    	$params = VOParams::getInstance();
    	$params->order('sort_order desc, id');
    	$cat_list = $mod_cat->getList($params, 'array');
    	$this->assign('cat_list', $cat_list);
    	
    	$category_id = request()->param('category_id', 1);
    	$this->assign('category_id', $category_id);

        $page = input('pno',1,'int');
        $pageSize = 15;

        $listDB = db('channel_company_order')->alias('o')
            ->join('channel_company c','o.channel_company_id = c.id','left')
            ->where([
    		    'c.category_id' => $category_id
            ])->where('o.add_time',['>=',$start],['<',$end]);
        if(!empty($this->company['id'])){
            $listDB->where(['o.company_id'=>$this->company['id']]);
        }else{
            $listDB->where(['o.user_id'=>$this->user->user_id]);
        }

        if(!in_array($status,[-1,0,1,2,3])){
            $this->error('该检索状态码不存在');
        }else{
            if($status>-1){
                $listDB->where(['o.status'=>$status]);
            }
        }
        $list = $listDB->field('o.*')
            ->paginate($pageSize, null, ['page'=>$page])
            ->toArray();
    	if(!empty($list['data'])){
    		foreach($list['data'] as $k=>$v){
                $userInfo = db('users')->where(['user_id'=>$v['user_id']])->field('user_id,user_name,nick_name,email,sex,birthday,user_picture')->find();
                $list['data'][$k]['user_info'] = !empty($userInfo) ? $userInfo : null;
                switch($v['status']){
                    case 0:
                        $list['data'][$k]['status_name'] = '待处理';break;
                    case 1:
                        $list['data'][$k]['status_name'] = '进行中';break;
                    case 2:
                        $list['data'][$k]['status_name'] = '已完成';break;
                    case 3:
                        $list['data'][$k]['status_name'] = '已取消';break;
                }
    		}
    	}

    	$this->assign('list', $list);
    	return $this->fetch();
    }
	
    /**
     * 渠道派单详情页
     * 
     * @author darkcloud.tan
     */
    public function channel_dispatch_details(){
    	$this->assign('id', request()->param('id', 0));
    	return $this->fetch();
    }

    /**
     * 渠道接单列表
     * @return mixed
     */
    public function channel_receipt(){
        //分类
        $startDate = input('startDate',date('Y-m-d',strtotime('-7 day')));
        $endDate = input('endDate',date('Y-m-d'));
        $status = input('status',-1);
        $this->assign([
            'startDate'=>$startDate,
            'endDate'=>$endDate,
            'status'=>$status
        ]);

        if(!strtotime($startDate) || !strtotime($startDate)){
            $this->error('查询时间格式错误');
        }else if(strtotime($startDate)>strtotime($endDate)){
            $this->error('开始时间不能大于结束时间');
        }else{
            $start = strtotime(date('Y-m-d',strtotime($startDate)));
            $end = strtotime($endDate)+24*60*60;
        }

        $mod_cat = MChannelCompanyCategory::getInstance();
        $params = VOParams::getInstance();
        $params->order('sort_order desc, id');
        $cat_list = $mod_cat->getList($params, 'array');
        $this->assign('cat_list', $cat_list);

        $category_id = request()->param('category_id', 1);
        $this->assign('category_id', $category_id);

        $page = input('pno',1,'int');
        $pageSize = 15;

        $listDB = db('channel_company_order')->alias('o')
            ->join('channel_company cc','o.channel_company_id = cc.id','')
            ->join('user_company uc','uc.id=cc.company_id','left')
            ->where([
                'cc.company_id'=>$this->company['id'],
                'cc.category_id' => $category_id,
                'uc.founder_user_id'=>$this->user->user_id,
                'o.receiving_status'=>1
            ])->where('o.add_time',['>=',$start],['<',$end]);

        if(!in_array($status,[-1,0,1,2,3])){
            $this->error('该检索状态码不存在');
        }else{
            if($status>-1){
                $listDB->where(['o.status'=>$status]);
            }
        }
        $list = $listDB->field('o.*')
            ->paginate($pageSize, null, ['page'=>$page])
            ->toArray();
        if(!empty($list['data'])){
            foreach($list['data'] as $k=>$v){
                $userInfo = db('users')->where(['user_id'=>$v['user_id']])->field('user_id,user_name,nick_name,email,sex,birthday,user_picture')->find();
                $list['data'][$k]['user_info'] = !empty($userInfo) ? $userInfo : null;
                switch($v['status']){
                    case 0:
                        $list['data'][$k]['status_name'] = '待处理';break;
                    case 1:
                        $list['data'][$k]['status_name'] = '进行中';break;
                    case 2:
                        $list['data'][$k]['status_name'] = '已完成';break;
                    case 3:
                        $list['data'][$k]['status_name'] = '已取消';break;
                }
            }
        }
        $this->assign('list', $list);
        return $this->fetch();
    }

    /**
     * 渠道接单详情
     * @return mixed
     */
    public function channelreceipt_details(){
        $id = intval(input('id', 0));
        $this->assign('id', request()->param('id', 0));
        $info = db('channel_company_order')->alias('o')
            ->join('channel_company cc','o.channel_company_id = cc.id','')
            ->join('user_company uc','uc.id=cc.company_id','left')
            ->join('channel_company_category ccc','ccc.id=cc.category_id','left')
            ->where([
                'cc.company_id'=>$this->company['id'],
                'o.id'=>$id,
                'o.receiving_status'=>1
            ])->field('o.*,ccc.category_name,cc.province_id,cc.city_id,cc.district_id')->find();
        $userInfo = db('users')->where(['user_id'=>$info['user_id']])->field('user_id,user_name,nick_name,email,sex,birthday,user_picture')->find();
        $province = db('region')->where(['region_id'=>$info['province_id']])->value('region_name');
        $city = db('region')->where(['region_id'=>$info['city_id']])->value('region_name');
        $district = db('region')->where(['region_id'=>$info['district_id']])->value('region_name');
        $areaInfo = !empty($province)?$province:'';
        $areaInfo .= !empty($city)?'-'.$city:'';
        $areaInfo .= !empty($district)?'-'.$district:'';

        $this->assign([
            'info'=>$info,
            'userInfo'=>$userInfo,
            'areaInfo'=>!empty($areaInfo)?$areaInfo:'全国'
        ]);
        return $this->fetch();
    }

    /**
     * 媒体接单列表
     */
    public function media_receipt(){
        $page = input('pno',1,'int');
        $pageSize = 15;
        $startDate = input('startDate',date('Y-m-d',strtotime('-7day')));
        $endDate = input('endDate',date('Y-m-d'));
        $status = input('status',-1);
        $type_id = input('type_id',1);
        if(!in_array($type_id,[1,2,3,4,5,6,7,8])){
            $this->error('错误的分类');
        }
        if(!strtotime($startDate) || !strtotime($startDate)){
            $this->error('查询时间格式错误');
        }else if(strtotime($startDate)>strtotime($endDate)){
            $this->error('开始时间不能大于结束时间');
        }else{
            $start = strtotime(date('Y-m-d',strtotime($startDate)));
            $end = strtotime($endDate)+24*60*60;
        }

        $company_info = db('user_company')->where(['founder_user_id'=>$this->user->user_id])->find();
        if(!empty($company_info) && !empty($company_info['id'])){
            $listDb = db('channel_media_order')->alias('mo')->field([
                'u.user_picture','u.nick_name','u.user_id','mo.price_sum','mo.add_time','mo.status','mo.id,mo.pay_status'
            ])->join('channel_media m','mo.media_id = m.id','left')
                ->join('users u','u.user_id = mo.user_id','left')
                ->where('mo.add_time',['>=',$start],['<',$end])
                ->where(['m.type_id'=>$type_id,'m.company_id'=>$company_info['id']]);
        }else{
            $this->error('您的名下没有媒体资源');
        }
        if(in_array($status,[0,1,2,3])){
            $listDb = $listDb->where(['mo.status'=>$status,'mo.pay_status'=>1]);
        }
        $list = $listDb->paginate($pageSize, null, ['page'=>$page])->toArray();
        foreach($list['data'] as $k=>$v){
            //0派单中，1接受派单，2拒绝接单，3超时失效
            $list['data'][$k]['status_name'] = $this->getMediaOrderStatusName($list['data'][$k]['status'],2);
        }
        $this->assign([
            'list'=>$list,
            'startDate'=>$startDate,
            'endDate'=>$endDate,
            'status'=>$status,
            'type_id'=>$type_id
        ]);
        return $this->fetch();
    }

    /**
     * 媒体接单详情
     */
    public function receipt_details(){
        $id = intval(input('id',0));
        $company_info = db('user_company')->where(['founder_user_id'=>$this->user->user_id])->find();
        $companyId = !empty($company_info)?$company_info['id']:0;
        $mediaInfo = db('channel_media_order')->alias('mo')->field([
            'u.user_picture','u.nick_name','u.user_id','mo.price_sum','mo.add_time','mo.status',
            'mo.id','mo.pay_status','m.title as media_title','m.title_sub as media_title_sub','m.id as media_id,mo.receiving_channel_status,mo.receiving_channel_user_id',
            'mo.title as order_title','mot.name as order_type_name','mo.link','mo.remarks'
        ])->join('channel_media m','mo.media_id = m.id','left')
            ->join('users u','u.user_id = mo.user_id','left')
            ->join('channel_media_order_type mot','mo.order_type=mot.order_type_id AND mot.media_type_id = mo.media_type_id','left')
            ->where(['mo.id'=>$id,'m.company_id'=>$companyId])
            ->find();
        //0待支付，1支付，2拒绝支付，3派单中，4接受派单，5拒绝接单，6超时失效
        if($mediaInfo['pay_status']==1){
            $mediaInfo['status'] = $mediaInfo['status']+3;
        }else{
            $mediaInfo['status'] = $mediaInfo['pay_status'];
        }
        $mediaInfo['status_name'] = $this->getMediaOrderStatusName($mediaInfo['status']);
        $this->assign([
            'info'=>$mediaInfo
        ]);
        return $this->fetch();
    }

    /**
     * 获取公司员工
     */
    public function getCompanyUsers(){
        if($this->user->user_id){
            $companyInfo = db('user_company')->where(['founder_user_id'=>$this->user->user_id])->find();
            if(!empty($companyInfo)){
                $users = db('users')->where(['company_id'=>$companyInfo['id']])->where('user_id','<>',$this->user->user_id)->field('user_id,nick_name')->select();
                $this->returnJson($users,1,'获取成功');
            }else{
                $this->returnJson([],0,'请先申请注册企业');
            }
        }else{
            $this->returnJson([],0,'请先登录');
        }
    }

    /**
     * 媒体接单|拒单审核
     */
    public function mediaOrderExam(){
        $user_id = intval(input('user_id',0));//派单给哪个员工id
        $status = intval(input('status',0));//1接单，2拒单
        $order_id = intval(input('order_id',0));//派单id
        //判断操作码是否正确
        if(!in_array($status,[1,2])){
            $this->returnJson([],0,'错误的操作码');
        }
        //判断订单是否存在
        $orderInfo = db('channel_media_order')->where(['id'=>$order_id])->field('*')->find();
        if(!$order_id || empty($orderInfo)){
            $this->returnJson([],0,'不存在的派单');
        }

        //判断当前用户是否有权限处理单子
        $userCompany = db('channel_media')->alias('m')
                ->join('user_company uc','m.company_id = uc.id','left')
                ->field('uc.founder_user_id')
                ->where(['m.id'=>$orderInfo['media_id']])
                ->find();
        if(empty($userCompany) || $userCompany['founder_user_id']!=$this->user->user_id){
            $this->returnJson([],0,'权限不足');
        }

        if($orderInfo['receiving_channel_status']!=0){
            $this->returnJson([],0,'该派单已被处理');
        }
        //判断是接单还是拒单
        if($status==1){
            //判断接单的用户是否存在
            $userInfo = db('users')->where(['user_id'=>$user_id])->find();
            if(!$user_id || empty($userInfo)){
                $this->returnJson([],0,'不存在的公司员工');
            }
            //接单    更新派单状态
            $res = db('channel_media_order')->where(['id'=>$order_id])->update([
                'receiving_channel_status'=>$status,
                'receiving_channel_user_id'=>$user_id,
                'status'=>1
            ]);
            if($res){
                $this->returnJson([],1,'操作成功');
            }else{
                $this->returnJson([],1,'操作失败');
            }
        }else if($status==2){
            //拒单
            $res = db('channel_media_order')->where(['id'=>$order_id])->update([
                'receiving_channel_status'=>$status
            ]);
            //退款
            if($orderInfo['company_id']==0){
                $tab = 'users';
                $c = 'user_money';
                $condition = ['user_id'=>$orderInfo['user_id']];
            }else{
                $tab = 'user_company';
                $c ='company_money';
                $condition = ['id'=>$orderInfo['company_id']];
            }
            $res = db($tab)->where($condition)->setInc($c,$orderInfo['price_sum']);
            if($res){
                $this->returnJson([],1,'操作成功');
            }else{
                $this->returnJson([],1,'操作失败');
            }
        }
    }


    /**
     * 渠道接单|拒单审核
     */
    public function channelOrderExam(){
        $user_id = intval(input('user_id',0));//派单给哪个员工id
        $status = intval(input('status',0));//1接单，2拒单
        $order_id = intval(input('order_id',0));//派单id
        //判断操作码是否正确
        if(!in_array($status,[1,2])){
            $this->returnJson([],0,'错误的操作码');
        }
        //判断订单是否存在
        $orderInfo = db('channel_company_order')->where(['id'=>$order_id])->field('*')->find();
        if(!$order_id || empty($orderInfo)){
            $this->returnJson([],0,'不存在的派单');
        }
        //判断当前用户是否有权限处理单子
        $userCompany = db('channel_company')->alias('c')
            ->join('user_company uc','c.company_id = uc.id','left')
            ->field('uc.founder_user_id')
            ->where(['c.id'=>$orderInfo['channel_company_id']])
            ->find();
        if(empty($userCompany) || $userCompany['founder_user_id']!=$this->user->user_id){
            $this->returnJson([],0,'权限不足');
        }
        //判断派单状态是否已被处理
        if($orderInfo['receiving_channel_status']!=0){
            $this->returnJson([],0,'该派单已被处理');
        }
        //判断是接单还是拒单
        if($status==1){
            //判断接单的用户是否存在
            $userInfo = db('users')->where(['user_id'=>$user_id])->find();
            if(!$user_id || empty($userInfo)){
                $this->returnJson([],0,'不存在的公司员工');
            }
            //接单
            $res = db('channel_company_order')->where(['id'=>$order_id])->update([
                'receiving_channel_status'=>$status,
                'receiving_channel_user_id'=>$user_id,
                'status'=>1
            ]);
            if($res){
                $this->returnJson([],1,'操作成功');
            }else{
                $this->returnJson([],1,'操作失败');
            }
        }else if($status==2){
            //拒单
            $res = db('channel_company_order')->where(['id'=>$order_id])->update([
                'receiving_channel_status'=>$status
            ]);
            if($res){
                $this->returnJson([],1,'操作成功');
            }else{
                $this->returnJson([],1,'操作失败');
            }
        }
    }

    /**
     * 充值到企业余额
     */
    public function com_recharge(){
        return $this->fetch();
    }

    /**
     * pc充值调用支付宝生成订单
     * @param  int $money  描述：金额，单位为元，必填
     * @param int $type   描述：充值类型：1-【个人充值】，2-【企业充值】,必填，默认是2
     */
    public function ajax_recharge(){
        $total_money = floatval(input('money',0));
        $type = intval(input('type',2));
        if(!$total_money){
            $this->error('充值金额必须大于0',url('company/com_recharge'));
        }
        if(!in_array($type,[1,2])){
            $this->error('错误的充值类型',url('company/com_recharge'));
        }
        $out_trade_no = $this->guid();
        $subject = '企业充值';
        $param = [
            'subject'=>$subject,
            'total_amount'=>number_format($total_money,2),
            'out_trade_no'=>$out_trade_no
        ];
        $bill_type = 1;
        $recharge_type = 1;
        $add_type = 1;
        $companyInfo = db('user_company')->where(['founder_user_id'=>$this->user->user_id])->find();
        if($type==2 && empty($companyInfo)){
            $this->error('该用户无法进行企业充值',url('company/com_recharge'));
        }
        $companyId = $type==2?$companyInfo['id']:0;
        $res = db('user_bill_record')->insert([
            'ordersn'=>$out_trade_no,
            'bill_type'=>$bill_type,
            'recharge_type'=>$recharge_type,
            'add_type'=>$add_type,
            'company_id'=>$companyId,
            'user_id'=>$this->user->user_id,
            'money'=>$total_money,
            'status'=>0,
            'add_time'=>time(),
            'finish_time'=>0
        ]);

        if(!$res){
            $this->error('生成订单失败',url('company/com_recharge'));
        }
        \alipay\Pagepay::pay($param);
    }

}
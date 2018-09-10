<?php
namespace app\index\controller;
use think\Controller;
use rongCloud\RongCloud;
use think\Exception;
use app\common\user\MUser;
use app\common\user\MUserRank;
use app\common\user\company\Model as MUserCompany;
use phpqrcode\QRcode;
use OSS\Core\OssException;

class Common extends Controller
{
    /**
     * 营销模式
     * @var unknown
     */
    protected $promotion_tree = null;
    protected $promotion_tree_key = null;
    
    /**
     * 六大营销模式logo图片
     * @var array
     */
    protected $logoArr = array(
        1 => '__RESOURCES__images/fuli.png',
        2 => '__RESOURCES__images/tiyan.png',
        3 => '__RESOURCES__images/chuxiao.png',
        4 => '__RESOURCES__images/wangluo.png',
        5 => '__RESOURCES__images/fencheng.png',
        6 => '__RESOURCES__images/guanggao.png',
    );
    
    /**
     * 储存已登录的用户信息，各方法重用
     * @var \app\common\user\VOUser
     */
    protected $user = null;
    protected $company = null;

    protected function _initialize(){
    	$mod_user = Muser::getInstance();
    	$this->user = &$mod_user->getLogined();
    	if($this->user){
    		$mod_user->flushLogined();
            //判断用户二维码是否生成，没有生成则生成
            $this->getMyQrcode();
            $this->assign('me', $this->user->toArray());
            try{
            	$this->company = MUserCompany::getOneById($this->user->company_id);
            }catch (\Exception $e){
            	$this->company = null;
            }
            if($this->company){
            	$this->company['company_head_portrait'] = get_image_path($this->company['company_head_portrait']);
            	$this->company['qrcode_path'] = get_image_path($this->company['qrcode_path']);
            	$this->company['remarks'] = format_bl2br($this->company['remarks']);
            	$this->assign('company', $this->company);
            	$this->assign('is_manage', $this->company['founder_user_id'] == $this->user->user_id);
            }else{
            	$this->assign('company', null);
            	$this->assign('is_manage', 0);
            }
    	}else{
    		$this->assign('me', null);
    	}
    	if(!$this->user){
    		//未登录用户判断是否带参数进入
    		$request = request();
    		$parent_id = $request->param('parent_id', 0);
    		if($parent_id > 0){
    			//设置推广ID;
    			cookie('reg_parent_id', $parent_id);
    		}
    	}
    	//基础数据
    	$m_user_rank = MUserRank::getInstance();
    	$list_rank= $m_user_rank->getAll();
    	$this->assign('list_rank', $list_rank);
    	//end 基础数据
	    
		# 营销分类
	    $this->promotion_tree = db('task_promotion')->order('parent_id')->order('sort_order')->select();
	    array2tree($this->promotion_tree, 'id', 'parent_id', 'child');
	    foreach ($this->promotion_tree as $k => $v){
	        $this->promotion_tree_key[$v['id']] = &$this->promotion_tree[$k];
	    }
	    $this->assign('promotion', $this->promotion_tree);
	    $this->assign('promotion_tree_key', $this->promotion_tree_key);
    	# 获取task_promotion一级分类图标
		$this->assign('promotionLogo', $this->logoArr);
		# 友情链接
		$this->assign('friend_link', db('friend_link')->select());
		# userSession
		$this->userSession();
		// icp号
		$this->getConfig();

        //判断是否有新的聊天信息
        $this->getUser();
	}
	
	protected function getConfig(){
		$list = db('shop_config')->whereIn('code', [
			'icp_number', 'icp_file'
		])->column('value','code');
		$this->assign('_CFG', $list);
		return $list;
	}

	protected function que_menu($param = []){
		$Array = array(
			'marketing/index' => array(
				'万能营销' 			=> ker(0, $param, '') ,
				ker(1, $param, '')	=> '',
			),
			'marketing/info' => array(
				'万能营销' 			=> ker(0, $param, ''),
				ker(1, $param, '') 	=> ker(2, $param, ''),
				ker(3, $param, '')	=> '',
			),
			'task/create' => array(
				'发布需求' => ''
			),
			'index/aboutus' => array(
				'关于我们' => '',
			),
			'index/contactus' => array(
				'联系我们' => '',
			),
			'index/joinus' => array(
				'加入我们' => '',
			),
			'index/quention' => array(
				'常见问题' => '',
			),
            'index/quentioninfo' => array(
                '常见问题' => ''
            ),
		    'login/agreement' => array(
		        '小蚁兵团服务协议' => '',
		    ),
			'index/notice' => array(
				'小蚁公告' => ''
			),
			'company/create' => array(
				'企业管理' => '',
				'创建企业' => '',
			),
			'company/index' => array(
				'企业管理' => '',
				ker(1, $param, '') 	=> ker(2, $param, ''),
			),
			'company/users' => array(
				'企业管理' => '',
				'员工管理' => '',
			),
			'company/information' => array(
				'企业管理' => '',
				'企业资料' => '',
			),
			'company/bind' => array(
				'企业管理' => '',
				'结算绑定' => '',
			),
			'company/apply' => array(
				'企业管理' => '',
				'申请审核' => '',
			),
		);
		$ca = strtolower(request()->controller()) . '/' . strtolower(request()->action());
		$que_menu = isset($Array[$ca]) ? $Array[$ca] : [];
// 		\anywhere\FW::debug($que_menu);exit;
		$this->assign('que_menu', $que_menu);
	}

	/**
	 * 六大营销模式
	 * PC 1-6F 排序为3 5 2 1 4 6
	 */
	protected function MarketingArray($key = null){
		$arr = array(
			1 => '免费福利',
			2 => '产品体验',
			3 => '促销活动',
			4 => '网络营销',
			5 => '产品分销',
			6 => '广告投放',
		);
		return $key == null ? $arr : ker($key, $arr, '');
	}

	protected function userSession(){
		if($this->user){
			$this->assign('userSession', $this->user->toArray());
		}
	}
	
	/**
	 * 以json形式返回数据
	 * @author 谭武云
	 * @date 2017年9月18日
	 * @param unknown $data
	 * @param number $code
	 * @param string $msg
	 */
	public function returnJson($data=null, $code = 0, $msg=''){
		$result = array(
			'code' 	=> $code,
			'msg' 	=> $msg,
			'data' 	=> empty($data) ? [] : $data,
		);
		exit(json_encode($result, JSON_UNESCAPED_UNICODE));
		
	}

    //判断用户是否登录，如果已经登录就获取融云token
    private function getUser(){
        $user = $this->user ? $this->user->toArray() : null;
        if(!empty($user)){
//            $appKey = '8luwapkv8r49l';
//            $appSecret = '9xKUVAQqynY';

            $appKey = 'tdrvipkstqxz5';
            $appSecret = 'lo1NtAShrAQ7';

//            $appKey = 'lmxuhwagl0eqd';
//            $appSecret = 'JnxxkubA4wVaR6';
            try{
                $rongCloud = new RongCloud($appKey,$appSecret);
                $user_picture = empty($user['user_picture'])?'/resources/images/touxiang.png':get_image_path($user['user_picture']);
                $nick_name = empty($user['nick_name'])?$user['user_name']:$user['user_name'];//用户名称
                $res = json_decode($rongCloud->User()->getToken($user['user_id'],$nick_name,$user_picture),true);
                $this->assign('token',$res['token']);
                $this->assign('userId',$user['user_id']);
                $this->assign('appKey',$appKey);
            }catch (Exception $e){

            }
        }
    }
    
    function _empty($name){
    	try{
    		return $this->fetch($name);
    	}catch(\think\exception\TemplateNotFoundException $e){
    		return;
    	}
    }


    //我的二维码
    function getMyQrcode(){

        $userInfo = db('users')->where(['user_id'=>$this->user->user_id])->field('user_qrcode_path')->find();
        if(empty($userInfo['user_qrcode_path'])){
            //获取邀请码
            $code = $this->getInvitationCode();
            $path = ROOT_PATH . 'public/static/qrcode_img/user/';
            $return = mkdirs($path);
            if($return===false){
                return false;
            }
            //二维码跳转链接
            @$link = XYBT_MOBILE_HOST.'/index.php?m=user&c=invitation&a=index&operation=adduser&user_id='.$this->user->user_id.'&code='.$code;
            $logo = ROOT_PATH.'/public/static/qr_logo.jpg';
            $errorCorrectionLevel = '3';
            $matrixPointSize = 6;
            $qrcode = $path . 'user_' . $this->user->user_id . '.png';
            $qrcode_last = $path . 'user_' . $this->user->user_id. '.png';
            $db_save_path = 'static/qrcode_img/user/user_' . $this->user->user_id . '.png';//数据库保存路径，oss保存的名称
            QRcode::png($link, $qrcode, $errorCorrectionLevel, $matrixPointSize, 2);

            if(file_exists($logo)){
                $qrcode = imagecreatefromstring(file_get_contents($qrcode));
                $logo = imagecreatefromstring(file_get_contents($logo));
                $qrcode_width = imagesx($qrcode);
                $qrcode_height = imagesy($qrcode);
                $logo_width = imagesx($logo);
                $logo_height = imagesy($logo);
                $logo_qr_width = $qrcode_width / 5;
                $scale = $logo_width / $logo_qr_width;
                $logo_qr_height = $logo_height / $scale;
                $from_width = ($qrcode_width - $logo_qr_width) / 2;
                imagecopyresampled($qrcode, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);
                imagepng($qrcode,$qrcode_last);//生成最终的文件
            }

            //将二维码传到阿里服务器
            try{
                //保存二维码到数据库
                db('users')->where(['user_id'=>$this->user->user_id])->update(['user_qrcode_path'=>$db_save_path]);
                //上传二维码文件
                \FileUpload::upload($qrcode_last,$db_save_path);

            }catch (OssException $e){
                print $e->getMessage();
                return false;
            }
        }
    }

    //获取邀请码，并更新到邀请表中
    function getInvitationCode(){
        $invitation = db('user_invitation')->where(['user_id'=>$this->user->user_id])->find();
        if(empty($invitation)){
            $code = inviteCode();
            $nick_name = $this->user->nick_name;
            $content =  '您的好友"'.$nick_name.'"邀请您加入小蚁兵团万能营销神器。福利不拿白不拿、产品体验有奖励、活动促销有惊喜、品牌宣传有希望、产品推广有分成、广告投放传播快！';
            $data = [
                'user_id'       => $this->user->user_id,
                'title'         => '邀请函',
                'content'       => $content,
                'code'           => $code,
                'register_show' => 1,
                'download_show' => 1,
                'up_time'        => time(),
            ];
            $id = db('user_invitation')->insert($data);
            if(!empty($id)){
                return $code;
            }else{
                return false;
            }
        }else{
            return $invitation['code'];
        }
    }

    //生成32位唯一的uuid
    function guid(){
        if (function_exists('com_create_guid')){
            return com_create_guid();
        }else{
            $charid = strtoupper(md5(uniqid(rand(), true)));
            $hyphen = chr(45);// "-"
            $uuid =  substr($charid, 0, 8).$hyphen
                .substr($charid, 8, 4).$hyphen
                .substr($charid,12, 4).$hyphen
                .substr($charid,16, 4).$hyphen
                .substr($charid,20,12);
            return $uuid;
        }
    }
}
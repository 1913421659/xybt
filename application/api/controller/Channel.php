<?php
/**
 * 渠道
 * Created by PhpStorm.
 * User: gjb
 * Date: 2018/2/26
 * Time: 11:40
 */

namespace app\api\controller;
use anywhere\FW;
use app\common\VORsAjax;
use anywhere\VOPageInfo;
use think\Exception;
use app\common\user\MUser;

class Channel extends Api{
    protected $user;
    public function __construct(){
        $mod_user = MUser::getInstance();
        $mod_user->flushLogined();
        $this->user = $mod_user->getLogined();
        parent::__construct();
    }
    /**
     * 营销渠道列表，提交方式：post方式
     * @link  https://
     * @param int $page  描述：当前页，非必填，默认第1页
     * @param int $pageSize 描述：每页条数，没必填，默认为15条
     * @param int $category_id 描述：营销分类，1-应用，2-游戏，3-IOS，4-分成，5-代理，6-地推，7-校园，8-效果，9-兼职，10-其他，必填
     * @param int $region_id 描述：地区ID，非必填
     * @param string $name     描述：名称，非必填
     * @param int $order_type  描述：接单数量类型，1-【200以上】，2-【100~200】，3-【50~100】，4-【10~50】，5-【10以下】，非必填
     * @param int $price_type  描述：接单金额类型，1-【20万以上】，2-【10万~20万】，3-【5万~10万】，4-【1万~5万】，5-【1万以下】，非必填
     * @param int $company_grade 描述：星级，非必填
     * @return array  说明：获取成功，则返回数组。
     * id【渠道ID】，name【渠道名称】，price_total【交易总额】，company_grade【评分】，is_auth【是否认证：1-是，2-否】，
     * order_total【接单数】,company_head_portrait【企业头像】。
     */
    function channelList(){
        $rs = VORsAjax::getInstance();
        try{
            //接单量对应值
            $order_type_arr = [
                '200,',
                '100,200',
                '50,100',
                '10,50',
                ',10'
            ];
            //接单金额对应值
            $price_type_arr = [
                '200000,',
                '100000,200000',
                '50000,100000',
                '10000,50000',
                ',10000'
            ];
            $query = db('channel_company')->alias('cc')
                ->join('user_company uc','uc.id=cc.company_id', 'left')
                ->field('cc.id,cc.name,cc.price_total,cc.company_grade,cc.order_total,uc.is_auth,uc.company_head_portrait')
                ->order('cc.sort_order DESC,cc.price_total DESC,cc.order_total DESC,cc.id DESC');
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
            $order_type = intval(FW::_REQUEST('order_type', 0));
            if(in_array($order_type,[1,2,3,4,5])){
                $_tmp = explode(',', $order_type_arr[$order_type-1]);
                if(!empty($_tmp[0])){
                    $query->where('cc.order_total','>=', $_tmp[0]);
                }
                if(!empty($_tmp[1])){
                    $query->where('cc.order_total','<=', $_tmp[1]);
                }
            }

            //接单金额
            $price_type= intval(FW::_REQUEST('price_type',0));
            if(in_array($price_type,[1,2,3,4,5])){
                $_tmp = explode(',', $price_type_arr[$price_type-1]);
                if(isset($_tmp[0])){
                    $query->where('cc.price_total','>=', $_tmp[0]);
                }
                if(isset($_tmp[1])){
                    $query->where('cc.price_total','<=', $_tmp[1]);
                }
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
            $page_info->page_size = FW::_REQUEST('page_size', 15);
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

        }catch (\Exception $e){
            $rs->errByException($e);
        }

        $rs->outputJSON();
    }

    /**
     * 营销渠道详情，提交方式：post方式
     * @link
     * @param int $id 描述：营销渠道ID，必填
     * @return array 说明：获取成功，则返回数组。
     *  id【渠道ID】，name【渠道名称】，price_total【交易总额】，company_grade【评分】，is_auth【是否认证：1-是，2-否】，
     *  order_total【接单数】，content【渠道特长】,company_head_portrait【企业头像】，is_collect【是否已经收藏：1-是，0-否】，
     *  is_allow_comment【是否允许评论：1-是，0-否】
     */
    public function channelDetail(){
        $rs = VORsAjax::getInstance();
        try{
            $id = intval(FW::_REQUEST('id', 0));
            $channelInfo = db('channel_company')->alias('cc')
                ->join('user_company uc','uc.id=cc.company_id', 'left')
                ->field('cc.id,cc.name,cc.price_total,cc.company_grade,cc.order_total,cc.content,uc.is_auth,uc.company_head_portrait')
                ->where(['cc.id'=>$id])
                ->find();
            if(!$id || empty($channelInfo)){
                throw new \Exception('不存在的渠道ID',1);
            }
            //判断用户是否登录
            if(!empty($this->user)){
                //判断是否收藏
                $collectInfo = db('channel_company_collect')->where(['user_id'=>$this->user->user_id,'channel_company_id'=>$id])->find();
                $channelInfo['is_collect'] = !empty($collectInfo);
                //获取没有评论过的订单
                $orderInfo = db('channel_company_order')->where([
                    'channel_company_id'=>$id,
                    'user_id'=>$this->user->user_id,
                    'status'=>2,
                    'is_comment'=>0,])->find();
                if(empty($orderInfo)){
                    $channelInfo['is_allow_comment']=1;
                }else{
                    $channelInfo['is_allow_comment']=0;
                }
            }else{
                //未登录
                throw new \Exception('请先登录',1);
            }
            $channelInfo['company_head_portrait'] = get_image_path($channelInfo['company_head_portrait']);
            $rs->data = $channelInfo;
        }catch (\Exception $e){
            $rs->errByException($e);
        }
        $rs->outputJSON();
    }

    /**
     *  收藏营销渠道，提交方式：post方式
     * @link
     * @param  int $id  描述：营销渠道ID，必填
     * @return array
     */
    public function channelCollect(){
        $rs = VORsAjax::getInstance();
        try{
            $id = intval(FW::_REQUEST('id',0));
            if(empty($this->user)){
                throw new \Exception('请先登录',1);
            }
            //判断是否收藏
            $channelInfo = db('channel_company')->alias('cc')
                ->join('user_company uc','uc.id=cc.company_id', 'left')
                ->field('cc.id,cc.name,cc.price_total,cc.company_grade,cc.order_total,cc.content,uc.is_auth,uc.company_head_portrait')
                ->where(['cc.id'=>$id])
                ->find();
            if(!$id || empty($channelInfo)){
                throw new \Exception('不存在的渠道ID',1);
            }
            //判断是否收藏
            $channelCollectInfo = db('channel_company_collect')->where(['user_id'=>$this->user->user_id,'channel_company_id'=>$id]);
            if(!empty($channelCollectInfo)){
                throw new \Exception('已收藏',1);
            }

            $res = db('channel_company_collect')->insert([
                'channel_company_id'=>$id,
                'user_id'=>$this->user->user_id,
                'add_time'=>time()
            ]);
            if($res){
                throw new \Exception('收藏成功',0);
            }else{
                throw new \Exception('收藏失败',1);
            }
        }catch (\Exception $e){
            $rs->errByException($e);
        }

        $rs->outputJSON();
    }

    /**
     * 对营销渠道进行评论打分，提交方式：post方式
     * @link
     * @param int $id  描述：营销渠道ID，必填
     * @param int $company_grade  描述：评分，1-5颗星，必填
     * @param string $comment   描述：评论内容
     * @return array
     *
     */
    public function channelGrade(){
        $rs = VORsAjax::getInstance();
        try{
            if(!$this->user){
                throw new \Exception('请先登录',1);
            }
            $id = intval(FW::_REQUEST('id',0));
            $channelInfo = db('channel_company')->where(['id'=>$id])->find();
            if(empty($channelInfo)){
                throw new \Exception('不存在的渠道ID',1);
            }
            //评分
            $company_grade = intval(FW::_REQUEST('company_grade',''));
            if(in_array($company_grade,[1,2,3,4,5])){
                throw new \Exception('请输入1-5的整数评分值',1);
            }
            $comment=trim(FW::_REQUEST('comment',''));
            if(empty($comment)){
                throw new \Exception('评论内容不能为空',1);
            }
            //获取订单
            $orderInfo = db('channel_company_order')->where([
                'channel_company_id'=>$id,
                'user_id'=>$this->user->user_id])->find();
            //判断订单状态以及评论状态
            if(empty($orderInfo)){
                throw new \Exception('未下单不能评价',1);
            }else if($orderInfo['status']!=2){
                throw new \Exception('订单结束后才能评价',1);
            }else if($orderInfo['is_comment']==1){
                throw new \Exception('已评价',1);
            }
            $insert_data = [
                'channel_company_id' => $id,
                'channel_company_order_id'=>$orderInfo['id'],
                'company_grade'       => $company_grade,
                'comment'  =>$comment,
                'is_show'  =>0,
                'user_id'  => $this->user->user_id,
                'add_time' => time()
            ];
            $res = db('channel_company_grade')->insert($insert_data);
            if($res){
                throw new \Exception('评价成功',0);
            }else{
                throw new \Exception('评价失败',0);
            }
        }catch (\Exception $e){
            $rs->errByException($e);
        }
        $rs->outputJSON();
    }

    /**
     * 渠道的评论列表
     * @param int $channel_company_id
     * @param int $page  描述：页码，非必填
     * @param int $page_size 描述：页码大小，非必填
     * @return array
     *  列表字段说明：
     *  channel_company_id：渠道ID，
     *  channel_company_order_id：关联订单ID，
     *  company_grade：评分，
     *  user_id：评论人用户ID，
     *  add_time：评论时间
     *
     */
    public function channelGradeList(){
        $rs = VORsAjax::getInstance();
        try{
            $channel_company_id = intval(FW::_REQUEST('channel_company_id',0));
            $channelInfo = db('channel_company')->where(['id'=>$channel_company_id])->find();
            if(empty($channelInfo)){
                throw new \Exception('不存在的渠道ID',1);
            }
            $query = db('channel_company_grade')->alias('ccg')
                ->join('users u','u.user_id = ccg.user_id','left')
                ->where([
                    'channel_company_id'=>$channel_company_id,
                    'is_show'=>1,
                ])->field([
                    'u.nick_name',
                    'u.user_picture',
                    'ccg.user_id',
                    'ccg.comment',
                    'ccg.id',
                    'ccg.add_time',
                    'ccg.company_grade',
                    'ccg.channel_company_id',
                    'ccg.channel_company_order_id'
                ])->order('add_time');
            $page_info = VOPageInfo::getInstance();
            $page_info->page_size = FW::_REQUEST('page_size', 15);
            $page_info->page= FW::_REQUEST('page', 1);
            $query->where(['cc.status'=>1]);
            $data = $query->paginate($page_info->page_size, false,['page'=>$page_info->page]);
            $list =$data->items();
            if(!empty($list)){
                foreach($list as $k=>$v){
                    $list[$k]['user_picture'] = get_image_path($v['user_picture']);
                    $list[$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
                }
            }
            $page_info->total = $data->total();
            $rs->data = [
                'list' => $list,
                'page_info' => $page_info
            ];
        }catch (\Exception $e){
            $rs->errByException($e);
        }
        $rs->outputJSON();
    }

    /**
     * 渠道营销分类，提交方式：post方式
     * @return array         说明：获取成功，则返回数组。
     * type_id【资源ID】，type_name【资源名称】，category_list【资源分类列表】，id【分类ID】，category_name【分类名称】。
     */
    public function channelCategory(){
        $rs = VORsAjax::getInstance();
        try{
            $category_list = [
                ['type_id'=>1,'type_name'=>'渠道'],
                ['type_id'=>2,'type_name'=>'媒体']
            ];
            $channel_list = db('channel_company_category')->where([
                'is_show'=>1
            ])->field([
                'id','category_name'
            ])->select();
            $media_list = db('channel_media_cat')->where([
                'parent_id'=>0
            ])->field([
                'id','name as category_name'
            ])->select();
            if($channel_list !== false && $media_list !== false){
                //渠道
                $category_list[0]['category_list'] = $channel_list;
                //媒体
                $category_list[1]['category_list'] = $media_list;
                $rs->data = $category_list;
            }else{
                throw new Exception('获取失败',1);
            }
        }catch (\Exception $e){
            $rs->errByException($e);
        }
        $rs->outputJSON();
    }

    /**
     * 渠道-地区列表，提交方法：post方式
     * @link
     * @param  int   $parent_id  描述：父ID，非必填，默认为1。
     * @return array             说明：获取成功则返回二维数组。
     *  id【区域ID】，name【名称】
     */
    public function channelRegion(){
        $rs = VORsAjax::getInstance();
        try{
            $parent_id = intval(FW::_REQUEST('parent_id',1));
            $region_list = [];
            $children_region_list = db('region')->where([
                'parent_id' => $parent_id
            ])->field([
                'region_id as id',
                'region_name as name'
            ])->select();
            if (!empty($children_region_list)) {
                if($parent_id == 1){
                    $region_list[] = [
                        'id'   => 1,
                        'name' => '全国'
                    ];
                    foreach($children_region_list as $key=>$val){
                        $region_list[] = $val;
                    }
                }else{
                    $region_list = $children_region_list;
                }
                $rs->data = $region_list;
            } else {
                throw new Exception('获取失败',1);
            }
        }catch (\Exception $e){
            $rs->errByException($e);
        }
        $rs->outputJSON();
    }

    /**
     * 渠道入驻，提交方法：post方式。
     * @link
     * @param  int    $channel_type  描述：渠道类型，1-营销，2-媒体，3-服务，必填。
     * @param  int    $category_id   描述：营销分式，必填。
     * @param  int    $region_id     描述：营销地区，必填。
     * @param  string $name          描述：标题，必填。
     * @param  string $contact_name  描述：联系人，必填。
     * @param  string $contact_phone 描述：联系电话，必填。
     * @param  string $content       描述：渠道特长，非必填。
     * @return array
     */
    public function channelApply(){
        $rs = VORsAjax::getInstance();
        try{
            if(!$this->user){
                throw new Exception('请先登录',1);
            }
            $channel_type = intval(FW::_REQUEST('channel_type',0));
            if(in_array($channel_type,[1,2,3])){
                throw new Exception('请输入正确的渠道类型',1);
            }
            //营销分式
            $category_id = intval(FW::_REQUEST('category_id',0));
            if(!$category_id){
                throw new Exception('营销分式为空');
            }
            //营销地区
            $region_id = intval(FW::_REQUEST('region_id',0));
            if(!$region_id){
                throw new Exception('营销地区为空',1);
            }
            //标题
            $name = trim(FW::_REQUEST('name',''));
            if(empty($name)){
                throw new Exception('标题为空',1);
            }
            //联系人
            $contact_name = trim(FW::_REQUEST('contact_name',''));
            if(empty($contact_name)){
                throw new Exception('contact_name');
            }
            //联系电话
            $contact_phone = trim(FW::_REQUEST('contact_phone',''));
            if(!preg_match("/^1(3|4|5|7|8)\d{9}$/",$contact_phone)){
                throw new Exception('请输出正确的联系电话',1);
            }
            //渠道特长
            $content = trim(FW::_REQUEST('content',''));
            $companyInfo = db('user_company')->where([
                'founder_user_id'=>$this->user->user_id,'status'=>1
            ])->field(['id'])->find();
            if(empty($companyInfo)){
                throw new Exception('创建企业后才能申请入驻渠道',1);
            }
            $insert_data = [
                'name'           => $name,
                'channel_type'  => $channel_type,
                'category_id'   => $category_id,
                'province_id'   => $region_id,
                'company_id'    => $companyInfo['id'],
                'user_id'        => $this->user->user_id,
                'contact_name'  => $contact_name,
                'contact_phone' => $contact_phone,
                'content'        => $content,
                'apply_time'     => time()
            ];
            $result = db('channel_apply')->insert($insert_data);
            if($result){
                throw new Exception('申请成功',0);
            }else{
                throw new Exception('申请失败',1);
            }
        }catch (\Exception $e){
            $rs->errByException($e);
        }
        $rs->outputJSON();
    }

    /**
     * 渠道-营销派单，提交方法：post方式
     * @link
     * @param  int    $id               描述：营销渠道ID，必填。
     * @param  string $name             描述：项目名称，必填。
     * @param  int    $promotion_price  描述：推广价格，必填。
     * @param  int    $promotion_budget 描述：推广预算，必填。
     * @param  int    $file_path        描述：文档地址，非必填。
     * @param  string $contact_name     描述：联系人，必填。
     * @param  string $contact_phone    描述：联系电话，必填。
     * @param  string $content    		 描述：推广要求，必填。
     * @return array
     */
    public function channelDispatch(){
        $rs = VORsAjax::getInstance();
        try {
            //判断是否登录
            if (!$this->user) {
                throw new Exception('请先登录', 1);
            }
            //渠道ID
            $id = intval(FW::_REQUEST('id',0));
            $company_info = db('channel_company')->where(['id'=>$id])->field(['company_id'])->find();
            if(!$id || empty($company_info)){
                throw new Exception('不存在该渠道',1);
            }
            //项目名称
            $name = trim(FW::_REQUEST('name',''));
            if(empty($name)){
                throw new Exception('项目名称为空',1);
            }
            //推广价格
            $promotion_price = trim(FW::_REQUEST('promotion_price',''));
            if(empty($promotion_price)){
                throw new Exception('推广价格为空',1);
            }
            //推广预算
            $promotion_budget = trim(FW::_REQUEST('promotion_budget',''));
            if(empty($promotion_budget)){
                throw new Exception('推广预算为空',1);
            }
            //文档
            $file_path = trim(FW::_REQUEST('file_path',''));
            //联系人
            $contact_name = trim(FW::_REQUEST('contact_name',''));
            if(empty($contact_name)){
                throw new Exception('联系人为空',1);
            }
            //联系电话
            $contact_phone = trim(FW::_REQUEST('contact_phone',''));
            if(!preg_match("/^1(3|4|5|7|8)\d{9}$/",$contact_phone)){
                throw new Exception('请输入正确的联系电话');
            }
            //推广要求
            $content = trim(FW::_REQUEST('content'.''));
            if(empty($content)){
                throw new Exception('推广要求为空',1);
            }
            //派单企业ID
            $dispatch_company_info = db('users')->where(['user_id'=>$this->user->user_id])->field(['company_id'])->find();
            if(!empty($dispatch_company_info)){
                if($dispatch_company_info['company_id']==$company_info['company_id']){
                    throw new Exception('不能给自己渠道派单',1);
                }else{
                    $dispatch_company_id = $dispatch_company_info['company_id'];
                }
            }else{
                $dispatch_company_id = 0;
            }
            //订单号
            $ordersn = getOrdersn();
            $insert_data = [
                'ordersn'             => $ordersn,
                'name'                => $name,
                'contact_name'       => $contact_name,
                'contact_phone'      => $contact_phone,
                'promotion_price'    => $promotion_price,
                'promotion_budget'   => $promotion_budget,
                'file_path'           => $file_path,
                'content'             => $content,
                'channel_company_id' => $id,
                'company_id'          => $dispatch_company_id,
                'user_id'             => $this->user->user_id,
                'add_time'            => time()
            ];
            $result = db('channel_company_order')->insert($insert_data);
            if($result){
                throw new Exception('派单成功',0);
            }else{
                throw new Exception('派单失败',1);
            }
        }catch (\Exception $e){
            $rs->errByException($e);
        }
        $rs->outputJSON();
    }

    /**
     * 营销渠道派单管理-派单列表，提交方式：post方式
     * @link
     * @param  int    $page        描述：页数，非必填，默认为第1页。
     * @param  int    $pagesize    描述：每页条数，非必填，默认为15条。
     * @param  int    $category_id 描述：营销分类，1-应用，2-游戏，3-IOS，4-分成，5-代理，6-地推，7-校园，8-效果，9-兼职，10-其他，必填。
     * @param  string $nick_name   描述：派单人，非必填。
     * @param  string $start_time  描述：派单开始时间，格式：0000-00-00，非必填。
     * @param  string $end_time    描述：派单结束时间，格式：0000-00-00，非必填。
     * @param  int    $status      描述：状态：1-待接单，2-已接单，3-已拒绝，非必填。
     * @return array               说明：获取成功，则返回数组。
     * id【派单ID】，company_head_portrait【渠道企业头像】,is_auth【渠道是否已认证：1-是，2-否】，name【渠道名称】，
     * nick_name【派单人】，add_time【派单时间】,	receiving_channel_status【接单状态：0-待处理，1-已接单，2-已拒绝】。
     */
    public function channelDispatchList(){
        
    }
} 
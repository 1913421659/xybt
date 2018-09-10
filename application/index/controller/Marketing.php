<?php
namespace app\index\controller;


use app\common\model\MTaskCategory;
use app\common\model\MTask;
use think\Exception;

class Marketing extends Common
{
    public function index()
    {
        $host = config('url_domain_root');
        $t_id = input('key', 0, 'int');//顶级营销方式
        $top_promotion= api('Promotion/first', ['id' => $t_id])['data'];
//     	\anywhere\FW::debug($top_promotion);

        //营销方式（task_promotion表二级）
        $promotion_list = api('Task/promotionList', ['pid'=>$top_promotion['id']])['data'];
//     	\anywhere\FW::debug($promotion_list);

        //项目分类（task_category表)
        $mod_task_category = new MTaskCategory();
        $category_list = $mod_task_category->getTreeList();
        //任务列表//暂时直接读取数据库，完善api后调用api
        $where = [
            't.status' => 1,
            't.parent_promotion_id' => $top_promotion['id']
        ];
        $promotion_id = input('p_id', 0, 'int');
        if($promotion_id){
            $where['t.promotion_id'] = $promotion_id;
            $this_promotion = db('task_promotion')-> where('id', $promotion_id)->find();
        }else{
            $this_promotion = ['id'=>0,'promotion_name'=>'全部'];
        }
        $cat_id = input('c_id', 0, 'int');
        if($cat_id){
            $this_category = db('task_category')->where('id', $cat_id)->find();
            $cat_id_list= db('task_category')->where('id', $cat_id)->whereOr('parent_id', $cat_id)->column('id');
            $where['task_category_id'] = ['in', $cat_id_list];
        }else{
            $this_category = ['id'=>0,'category_name'=>'全部'];
        }
        $page_size = [
            0,20,21,20,21,20,21
        ];
        $pages = db('task')->alias('t')
            ->join('task_checkout_type tct', 'tct.id = t.task_checkout_type_id', 'left')
            ->join('users u', "u.user_id = t.advertiser_id", 'left')
            ->join('task_promotion tp', 'tp.id=t.promotion_id', 'left')
            ->where($where)
            ->where('t.qty>t.brought_total')      //判断剩余份数不为0的
            ->field('t.*, u.company_id,tct.checkout_name,tp.promotion_name')
            ->order('t.is_recommend desc, t.sort_order, t.add_time desc ')
            ->paginate($page_size[$top_promotion['id']], null, ['page'=>input('page',1,'int')])->toArray();
        $list = $pages['data'];
        unset($pages['data']);
        //分页信息（总页数totalPage，总条目数totalRecords，当前页pno）
        $mod = new MTask();
        //"热门市场营销"（5个）“热门商品营销”（5个）
        $hot_1 = $mod->getList([
            'parent_promotion_id' => 4,
            'pagesize' => 5
        ]);
        $hot_2 = $mod->getList([
            'parent_promotion_id' => 2,
            'pagesize' => 5
        ]);


        $this->assign([
            'hot_1' => $hot_1,
            'hot_2' => $hot_2,
            'top_promotion' => $top_promotion,
            'promotion_list' => $promotion_list,
            'category_list' => $category_list,
            'p_id' => $promotion_id,
            'this_promotion' => $this_promotion,
            'c_id' => $cat_id,
            'this_category' => $this_category,
            'list' => $list,
            'pages' => $pages,
            //顶级营销方式图标
            'ico_promotion' => ['default'=>[
                '1'=>'k1.png',
                '2'=>'k3.png',
                '3'=>'k5.png',
                '4'=>'k7.png',
                '5'=>'k9.png',
                '6'=>'k11.png',
            ],
                'act'=>[
                    '1'=>'k2.png',
                    '2'=>'k4.png',
                    '3'=>'k6.png',
                    '4'=>'k8.png',
                    '5'=>'k10.png',
                    '6'=>'k12.png',
                ]
            ]
        ]);
        //顶级营销方式图标
        $this->assign('exam_time_type',db('task_exam_time_type')->column('name', 'id'));
        return $this->fetch();
    }

    public function information()
    {
        $host = config('url_domain_root');
    	$t_id = input('key', 0, 'int');//顶级营销方式
    	$top_promotion= api('Promotion/first', ['id' => $t_id])['data'];
//     	\anywhere\FW::debug($top_promotion);
    	
    	//营销方式（task_promotion表二级）
    	$promotion_list = api('Task/promotionList', ['pid'=>$top_promotion['id']])['data'];
//     	\anywhere\FW::debug($promotion_list);
    	
    	//项目分类（task_category表)
    	$mod_task_category = new MTaskCategory();
    	$category_list = $mod_task_category->getTreeList();
    	//任务列表//暂时直接读取数据库，完善api后调用api
    	$where = [
    		't.status' => 1,
    		't.parent_promotion_id' => $top_promotion['id']
    	];
    	$promotion_id = input('p_id', 0, 'int');
    	if($promotion_id){
    		$where['t.promotion_id'] = $promotion_id;
    		$this_promotion = db('task_promotion')-> where('id', $promotion_id)->find();
    	}else{
    		$this_promotion = ['id'=>0,'promotion_name'=>'全部'];
    	}
    	$cat_id = input('c_id', 0, 'int');
    	if($cat_id){
    		$this_category = db('task_category')->where('id', $cat_id)->find();
    		$cat_id_list= db('task_category')->where('id', $cat_id)->whereOr('parent_id', $cat_id)->column('id');
    		$where['task_category_id'] = ['in', $cat_id_list];
    	}else{
    		$this_category = ['id'=>0,'category_name'=>'全部'];
    	}
	    $page_size = [
	    	0,20,21,20,21,20,21
	    ];
    	$pages = db('task')->alias('t')
	    	->join('task_checkout_type tct', 'tct.id = t.task_checkout_type_id', 'left')
	    	->join('users u', "u.user_id = t.advertiser_id", 'left')
	    	->join('task_promotion tp', 'tp.id=t.promotion_id', 'left')
	    	->where($where)
            ->where('t.qty>t.brought_total')      //判断剩余份数不为0的
	    	->field('t.*, u.company_id,tct.checkout_name,tp.promotion_name')
	    	->order('t.is_recommend desc, t.sort_order, t.add_time desc ')
	    	->paginate($page_size[$top_promotion['id']], null, ['page'=>input('page',1,'int')])->toArray();
    	$list = $pages['data'];
    	unset($pages['data']);
    	//分页信息（总页数totalPage，总条目数totalRecords，当前页pno）
    	$mod = new MTask();
    	//"热门市场营销"（5个）“热门商品营销”（5个）
    	$hot_1 = $mod->getList([
    		'parent_promotion_id' => 4,
    		'pagesize' => 5
    	]);
    	$hot_2 = $mod->getList([
    		'parent_promotion_id' => 2,
    		'pagesize' => 5
    	]);
    	
    	
    	$this->assign([
    		'hot_1' => $hot_1,
    		'hot_2' => $hot_2,
    		'top_promotion' => $top_promotion,
    		'promotion_list' => $promotion_list,
    		'category_list' => $category_list,
    		'p_id' => $promotion_id,
    		'this_promotion' => $this_promotion,
    		'c_id' => $cat_id,
    		'this_category' => $this_category,
    		'list' => $list,
    		'pages' => $pages,
    		//顶级营销方式图标
    		'ico_promotion' => ['default'=>[
    			'1'=>'k1.png',
    			'2'=>'k3.png',
    			'3'=>'k5.png',
    			'4'=>'k7.png',
    			'5'=>'k9.png',
    			'6'=>'k11.png',
    		],
    			'act'=>[
    				'1'=>'k2.png',
    				'2'=>'k4.png',
    				'3'=>'k6.png',
    				'4'=>'k8.png',
    				'5'=>'k10.png',
    				'6'=>'k12.png',
    			]
    		]
    	]);
    	//顶级营销方式图标
    	$this->assign('exam_time_type',db('task_exam_time_type')->column('name', 'id'));
        return $this->fetch();
    }

    public function moonlighting()
    {
        $host = config('url_domain_root');
        $t_id = input('key', 0, 'int');//顶级营销方式
        $top_promotion= api('Promotion/first', ['id' => $t_id])['data'];
//     	\anywhere\FW::debug($top_promotion);

        //营销方式（task_promotion表二级）
        $promotion_list = api('Task/promotionList', ['pid'=>$top_promotion['id']])['data'];
//     	\anywhere\FW::debug($promotion_list);

        //项目分类（task_category表)
        $mod_task_category = new MTaskCategory();
        $category_list = $mod_task_category->getTreeList();
        //任务列表//暂时直接读取数据库，完善api后调用api
        $where = [
            't.status' => 1,
            't.parent_promotion_id' => $top_promotion['id']
        ];
        $promotion_id = input('p_id', 0, 'int');
        if($promotion_id){
            $where['t.promotion_id'] = $promotion_id;
            $this_promotion = db('task_promotion')-> where('id', $promotion_id)->find();
        }else{
            $this_promotion = ['id'=>0,'promotion_name'=>'全部'];
        }
        $cat_id = input('c_id', 0, 'int');
        if($cat_id){
            $this_category = db('task_category')->where('id', $cat_id)->find();
            $cat_id_list= db('task_category')->where('id', $cat_id)->whereOr('parent_id', $cat_id)->column('id');
            $where['task_category_id'] = ['in', $cat_id_list];
        }else{
            $this_category = ['id'=>0,'category_name'=>'全部'];
        }
        $page_size = [
            0,20,21,20,21,20,21
        ];
        $pages = db('task')->alias('t')
            ->join('task_checkout_type tct', 'tct.id = t.task_checkout_type_id', 'left')
            ->join('users u', "u.user_id = t.advertiser_id", 'left')
            ->join('task_promotion tp', 'tp.id=t.promotion_id', 'left')
            ->where($where)
            ->where('t.qty>t.brought_total')      //判断剩余份数不为0的
            ->field('t.*, u.company_id,tct.checkout_name,tp.promotion_name')
            ->order('t.is_recommend desc, t.sort_order, t.add_time desc ')
            ->paginate($page_size[$top_promotion['id']], null, ['page'=>input('page',1,'int')])->toArray();
        $list = $pages['data'];
        unset($pages['data']);
        //分页信息（总页数totalPage，总条目数totalRecords，当前页pno）
        $mod = new MTask();
        //"热门市场营销"（5个）“热门商品营销”（5个）
        $hot_1 = $mod->getList([
            'parent_promotion_id' => 4,
            'pagesize' => 5
        ]);
        $hot_2 = $mod->getList([
            'parent_promotion_id' => 2,
            'pagesize' => 5
        ]);


        $this->assign([
            'hot_1' => $hot_1,
            'hot_2' => $hot_2,
            'top_promotion' => $top_promotion,
            'promotion_list' => $promotion_list,
            'category_list' => $category_list,
            'p_id' => $promotion_id,
            'this_promotion' => $this_promotion,
            'c_id' => $cat_id,
            'this_category' => $this_category,
            'list' => $list,
            'pages' => $pages,
            //顶级营销方式图标
            'ico_promotion' => ['default'=>[
                '1'=>'k1.png',
                '2'=>'k3.png',
                '3'=>'k5.png',
                '4'=>'k7.png',
                '5'=>'k9.png',
                '6'=>'k11.png',
            ],
                'act'=>[
                    '1'=>'k2.png',
                    '2'=>'k4.png',
                    '3'=>'k6.png',
                    '4'=>'k8.png',
                    '5'=>'k10.png',
                    '6'=>'k12.png',
                ]
            ]
        ]);
        //顶级营销方式图标
        $this->assign('exam_time_type',db('task_exam_time_type')->column('name', 'id'));
        return $this->fetch();
    } //兼职达人

    private $img = [
    	4 => [
    		['img_path'=>'data/task_default_img/wlyx/yingxiao_1.jpg'],
    		['img_path'=>'data/task_default_img/wlyx/yingxiao_2.jpg'],
    		['img_path'=>'data/task_default_img/wlyx/yingxiao_3.jpg'],
    		['img_path'=>'data/task_default_img/wlyx/yingxiao_4.jpg'],
    		['img_path'=>'data/task_default_img/wlyx/yingxiao_5.jpg'],
    		['img_path'=>'data/task_default_img/wlyx/yingxiao_6.jpg'],
    		['img_path'=>'data/task_default_img/wlyx/yingxiao_7.jpg'],
    		['img_path'=>'data/task_default_img/wlyx/yingxiao_8.jpg']
    	],
    	5 => [
    		['img_path'=>'data/task_default_img/cpfx/fenxiao_1.jpg'],
    		['img_path'=>'data/task_default_img/cpfx/fenxiao_2.jpg'],
    		['img_path'=>'data/task_default_img/cpfx/fenxiao_3.jpg'],
    		['img_path'=>'data/task_default_img/cpfx/fenxiao_4.jpg'],
    		['img_path'=>'data/task_default_img/cpfx/fenxiao_5.jpg'],
    		['img_path'=>'data/task_default_img/cpfx/fenxiao_6.jpg'],
    		['img_path'=>'data/task_default_img/cpfx/fenxiao_7.jpg'],
    		['img_path'=>'data/task_default_img/cpfx/fenxiao_8.jpg']
    	],
    	6 => [
    		['img_path'=>'data/task_default_img/ggtf/guanggao_1.jpg'],
    		['img_path'=>'data/task_default_img/ggtf/guanggao_2.jpg'],
    		['img_path'=>'data/task_default_img/ggtf/guanggao_3.jpg'],
    		['img_path'=>'data/task_default_img/ggtf/guanggao_4.jpg'],
    		['img_path'=>'data/task_default_img/ggtf/guanggao_5.jpg'],
    		['img_path'=>'data/task_default_img/ggtf/guanggao_6.jpg'],
    		['img_path'=>'data/task_default_img/ggtf/guanggao_7.jpg'],
    		['img_path'=>'data/task_default_img/ggtf/guanggao_8.jpg']
    	]
    ];

    public function info(){
    	$mod = new MTask();
    	$user = $this->user ? $this->user->toArray() : null;
    	$user_id = isset($user['user_id']) ? $user['user_id'] : 0;
    	$info = $mod->getInfo(input('key',0,'int'), $user_id);
        $task_id = input('key',0,'int');
    	if(empty($info['task_flow_img']) && in_array($info['parent_promotion_id'], [4,5,6])){
    		$info['task_flow_img'] = $this->img[$info['parent_promotion_id']];
    	}

    	$push = $mod->getList([
    		'parent_promotion_id' => $info['parent_promotion_id'],
    		'id_notin' => [$info['id']],
    		'pagesize'=>6
    	]);
    	//二维码
    	$info['qr_str'] = config('url_shop_root') . 'mobile/index.php?m=user&c=share&a=index&task_id=' . $info['id'];
    	if(!!$user){
    		$info['qr_str'] .= '&parent_id=' . $user['user_id'];
    	}
        //判断用户是否申请或者提交审核
        if(!empty($user)){
            //操作状态、说明
            //查询用户领取任务信息，判断是否已经领取该任务
            $taskUserInfo = db('task_user')->where(['task_id'=>$task_id,'user_id'=>$user_id])->field(['id','step','step_finish_time','status','exam_status'])->find();
            if(empty($taskUserInfo)){
                //网络营销、产品分销、广告申请
                if($info['share_type'] == 6){
                    $apply_info = db('task_adv_apply')->where(['task_id'=>$task_id,'user_id'=>$user_id])->field(['id','status'])->find();
                    if(empty($apply_info)){
                        $info['status'] = 5;
                        $info['operation'] = '马上申请';
                    }elseif($apply_info['status'] == 0){
                        $info['status'] = 6;
                        $info['operation'] = '待审核';
                    }elseif ($apply_info['status'] == 2){
                        $info['status'] = 7;
                        $info['operation'] = '审核未过';
                    }elseif ($apply_info['status'] == 4){
                        $info['status'] = 5;
                        $info['operation'] = '重新申请';
                    }elseif ($apply_info['status'] == 5){
                        $info['status'] = 18;
                        $info['operation'] = '已抢完';
                    }
                }else{
                    //免费福利、产品体验、促销活动
                    $info['status'] = 8;
                    $info['operation'] = '马上抢';
                }
            }else{
                //用户任务领取ID
                $info['task_user_id'] = $taskUserInfo['id'];
                //网络营销、产品分销、广告申请
                if($info['share_type'] == 6){
                    if($info['parent_promotion_id'] == 4){
                        //网络营销
                        $examInfo = db('task_distribution_exam')->where( ['task_user_id'=>$info['task_user_id'],'exam_status'=>0])->field(['id'])->find();
                        if(!empty($examInfo['id'])){
                            $info['status'] = 6;
                            $info['operation'] = '待审核';
                        }else{
                            $info['status'] = 21;
                            $info['operation'] = '上传审核';
                        }
                    }else{
                        //产品分销、广告投放
                        $info['status'] = 9;
                        $info['operation'] = '进行中';
                    }
                }else{
                    //免费福利、产品体验、促销活动
                    if($taskUserInfo['status'] == 1){
                        //产品体验
                        if($info['parent_promotion_id'] == 2){
                            $info['step'] = $taskUserInfo['step'] + 1;//当前执行到第几步
                            if(isset($info['experience_'.$info['step'].'_day_start']) && !empty($info['experience_'.$info['step'].'_day_start'])){
                                $task_start_time = $taskUserInfo['step_finish_time'] + ($info['experience_'.$info['step'].'_day_start']) * 86400;
                                $task_end_time = $taskUserInfo['step_finish_time'] + $info['experience_'.$info['step'].'_day_end'] * 86400;
                                if($task_start_time > time()){
                                    $info['status'] = 15;
                                    $info['operation'] = '未到体验时间';
                                }elseif($task_end_time < time()){
                                    $info['status'] = 16;
                                    $info['operation'] = '体验时间已到期';
                                }
                            }
                        }else{
                            //免费福利、促销活动
                            $info['step'] = 3;
                        }
                        //如果不存在操作状态
                        if(empty($info['status'])){
                            if($info['task_step_'.$info['step'].'_event'] == 1 || $info['task_step_'.$info['step'].'_event'] == 2){
                                if($info['parent_promotion_id'] == 2){
                                    $condition = ['task_user_id'=>$taskUserInfo['id'],'step'=>$info['step']];
                                }else{
                                    $condition = ['task_user_id'=>$taskUserInfo['id']];
                                }
                                $taskUserImgInfo = db('task_user_img')->where($condition)->find(['id'],$condition);
                                if(empty($taskUserImgInfo['id'])){
                                    if($info['task_step_'.$info['step'].'_event'] == 1){
                                        $info['status'] = 10;
                                        $info['operation'] = '上传截图';
                                    }else{
                                        $info['status'] = 11;
                                        $info['operation'] = '上传文件';
                                    }
                                }elseif($taskUserInfo['exam_status'] == 0){
                                    $info['status'] = 6;
                                    $info['operation'] = '待审核';
                                }elseif($taskUserInfo['exam_status'] == 2){
                                    $info['status'] = 7;
                                    $info['operation'] = '审核未过';
                                }elseif ($taskUserInfo['exam_status'] == 4){
                                    if($info['task_step_'.$info['step'].'_event'] == 1){
                                        $info['status'] = 10;
                                        $info['operation'] = '重新上传截图';
                                    }else{
                                        $info['status'] = 11;
                                        $info['operation'] = '重新上传文件';
                                    }
                                }
                            }elseif($info['task_step_'.$info['step'].'_event'] == 3){
                                $info['status'] = 12;
                                $info['operation'] = '再体验';
                            }elseif($info['task_step_'.$info['step'].'_event'] == 4){
                                $info['status'] = 17;
                                $info['operation'] = '再体验';
                            }elseif($info['task_step_'.$info['step'].'_event'] == 5){
                                $info['status'] = 20;
                                $info['operation'] = '再体验';
                            }else{
                                if(!empty($info['goods_id'])){
                                    $info['status'] = 19;
                                    $info['operation'] = '待支付';
                                }else{
                                    $info['status'] = 13;
                                    $info['operation'] = '领取完成';
                                }
                            }
                        }
                    }else{
                        $info['status'] = 14;
                        $info['operation'] = '再次领取';
                    }
                }
            }
        }
//print_r($info);die;
    	$this->assign([
    		'info' => $info,
    		'push' => $push
    	]);
    	$param = array(
    		url('marketing/index'),
    		$this->MarketingArray($info['parent_promotion_id']),
    		url('marketing/index',['key' => $info['parent_promotion_id']]),
    		$info['name'],
    	);
    	$this->que_menu($param);
    	$tpl = 'task/info_' . $info['parent_promotion_id'];
    	return $this->fetch($tpl);
    }

    public function adv_apply(){
        if(! session('user.user_id')){
            return json(['code' => 0, 'msg' => '请先登录']);
        }
        request()->isPost() or die();
        $post = request()->post();
        $post['user_id'] = session('user.user_id');
        $res = api('Advapply/create', $post);
        return json($res);
    }

    /**
     * 网络营销-上传审核
     */
    public function marketingPostExamine(){
        if(!$this->user){
            $this->returnJson(['code' => -1, 'msg' => '请先登录']);
        }

        $fileArr = isset($_POST['images'])?$_POST['images']:null;
        $task_user_id = input('task_user_id',0,'int');
        $fileFiles = isset($_POST['files'])?$_POST['files']:null;
        $fileFilesName = isset($_POST['filesName'])?$_POST['filesName']:'';
        if((empty($fileArr) && empty($fileFiles)) ||  empty($task_user_id)){
            $this->returnJson(['code' => 0, 'msg' => '参数有误！']);
        }
        //是否存在用户任务ID
        $taskUserInfo = db('task_user')->alias('tu')->join('task t','tu.task_id = t.id ')->where(['t.id'=>$task_user_id,'tu.user_id'=>$this->user->user_id])->field(['tu.id,t.parent_promotion_id','tu.user_id'])->find();
        if(empty($taskUserInfo)){
            $this->returnJson([],0,'不存在该用户任务ID');
        }elseif($taskUserInfo['parent_promotion_id'] != 4){
            $this->returnJson($task_user_id,0,'该任务不是网络营销类型');
        }elseif($taskUserInfo['user_id'] != $this->user->user_id){
            $this->returnJson($taskUserInfo,0,'没有权限');
        }

        //判断是否需要等待审核
        $examInfo = db('task_distribution_exam')->where(['task_user_id'=>$taskUserInfo['id'],'exam_status'=>0])->find();
        if(!empty($examInfo)){
            $this->returnJson([],0,'请耐心等待审核');
        }else{
            if(!empty($fileArr)){
                $res = $this->addTaskDistributionExam($taskUserInfo['id'],$fileArr,1);
            }

            if(!empty($fileFiles)){
                $ext = substr($fileFilesName,strripos($fileFilesName,'.'));//扩展名
                $res = $this->addTaskDistributionExam($taskUserInfo['id'],[$fileFiles],2,$ext);
            }
            if($res){
                $this->returnJson([],1,'提交成功');
            }else{
                $this->returnJson([],0,'提交失败');
            }

        }

    }

    //新增网络营销上传文件审核信息
    private function addTaskDistributionExam($task_user_id,$fileArr,$fileType,$ext=''){
        if(empty($task_user_id) || empty($fileArr)){
            return false;
        }
        $path = 'static/check_file/';
        $task_query = db('task_distribution_exam');
        try{
            $task_query ->startTrans();
            $task_query->insert([
                'task_user_id'=>$task_user_id,
                'add_time'=>time()
            ]);
            $exam_id = $task_query->getLastInsID();
            foreach($fileArr as $k=>$v){

                $start=strpos($v,',');
                $img= substr($v,$start+1);
                $img = str_replace(' ', '+', $img);
                $data = base64_decode($img);
                if($fileType==1){
                    $ext = !empty($ext)?$ext:'.png';
                    $fileName = uniqid() . $ext;
                }else if($fileType==2){
                    $ext = !empty($ext)?$ext:'.doc';
                    $fileName = uniqid() . $ext;
                }
                $full_path = ROOT_PATH.'public/'.$path.$fileName;
                file_put_contents($full_path, $data);
                \FileUpload::upload($full_path, $path.$fileName);//上传图片到阿里云
                db('task_distribution_exam_img')->insert([
                    'distribution_exam_id' => $exam_id,
                    'file_type'             => $fileType,
                    'file_path'             => $path.$fileName,
                    'sort_order'            => $k+1,
                    'add_time'              => time()
                ]);
            }
            $task_query->commit();
            return true;
        }catch (Exception $e){
            $task_query ->rollback();
            return false;
        }
    }

}

<?php
namespace app\index\controller;
use app\common\model\MTask;
use app\common\model\MTaskPromotion;
use app\common\user\MUserInvitation;
use cache\redis\RedisCache;
use app\common\user\MUserRank;
use anywhere\FW;
use think\Db;

class Index extends Common
{
    public function index()
    {
    	$task_list = [];
    	$mod = new MTask();
    	//p1免费福利
        $task_list[1] = $mod->getList([
    	    'parent_promotion_id'=>1,
        	'pagesize' => 8,
        	'order'=>'t.is_recommend desc, t.sort_order, t.add_time desc '
        ]);
        $task_list[2] = $mod->getList([
            'parent_promotion_id'=>2,
        	'pagesize' => 9,
        	'order'=>'t.is_recommend desc, t.sort_order desc, t.add_time desc '
        ]);
        $task_list[3] = $mod->getList([
            'parent_promotion_id'=>3,
        	'pagesize' => 7,
        	'order'=>'t.is_recommend desc, t.sort_order desc, t.add_time desc '
        ]);
        $task_list[4] = $mod->getList([
            'parent_promotion_id'=>4,
        	'pagesize' => 8,
        	'order'=>'t.is_recommend desc, t.sort_order desc, t.add_time desc '
        ]);
        $task_list[5] = $mod->getList([
            'parent_promotion_id'=>5,
        	'pagesize' => 8,
        	'order'=>'t.is_recommend desc, t.sort_order desc, t.add_time desc '
        ]);
        $task_list[6] = $mod->getList([
            'parent_promotion_id'=>6,
            'pagesize' => 9,
        	'order'=>'t.is_recommend desc, t.sort_order desc, t.add_time desc '
        ]);
        $this->assign('task_list', $task_list);
        $this->assign('exam_time_type',db('task_exam_time_type')->column('name', 'id'));

     	$f3_1 = api('Task/plist', ['parent_promotion_id' => 2, 'pagesize' => 9, 'relation' => 'taskExamTimeType'])['data'];
     	$p2_rank = db('task')->where('parent_promotion_id',2)->order('reward_total', 'desc')->limit(8)->select();
     	$p2_hot = db('task')->where('parent_promotion_id',2)->order('brought_total', 'desc')->limit(10)->select();

    	
    	$f6_new = $mod->getList([
    	    'parent_promotion_id'=>6,
    	    'pagesize' => 10,
    	    'order' => 't.add_time desc'
    	]);
    	$f6_hot = $mod->getList([
    	    'parent_promotion_id'=>6,
    	    'pagesize' => 10,
    	    'order' => 't.brought_total+t.brought_total_virtual desc'
    	]);
    	$f6_budget = $mod->getList([
    	    'parent_promotion_id'=>6,
    	    'pagesize' => 6,
    	    'order' => 't.budget desc'
    	]);

     	$this->assign('f3_1', $f3_1);
     	$this->assign('f3_rank', $p2_rank);
     	$this->assign('f3_hot', $p2_hot);

    	$this->assign('f6_new', $f6_new);
    	$this->assign('f6_hot', $f6_hot);
		$this->assign('f6_budget', $f6_budget);

		//获取首页轮播图
		$ad_list = [
			'banner'=>[],//[官网]首页轮播
			'f1_1'=>[],//[官网]首页1楼(上)
			'f1_2'=>[],//[官网]首页1楼(下)
			'f2_1'=>[],//[官网]首页2楼(上)
			'f2_2'=>[],//[官网]首页2楼(下)
			//三楼没有广告位
			'f4_1'=>[],//[官网]首页4楼(上)
			'f4_2'=>[],//[官网]首页4楼(下)
			'f5_1'=>[],//[官网]首页5楼(上)
			'f5_2'=>[],//[官网]首页5楼(下)
			'f6_1'=>[],//[官网]首页6楼(左)
		    'f6_2'=>[],//[官网]首页6楼(右)
		    'f6_2'=>[],//[官网]首页6楼(右)
		    'bottom'=>[],//[官网]首页bottom
		];
		$ad_position = [];
		$ad_position['banner'] = 0 + db('ad_position')->where('position_name', 'PC端首页轮播')->value('position_id');
		$pids = array_values($ad_position);
		$time = time();
		$ad_list_all = db('ad')->where(['start_time' => [ "<=", $time],
			'end_time' => [">=", $time]])->whereIn('position_id', $pids)->select();
		$ad_position = array_flip($ad_position);
		foreach($ad_list_all as $ad){
			$ad_list[$ad_position[$ad['position_id']]][] = $ad;
		}
		
		$this->assign('ad_list', $ad_list);
		$this->assign('notic_list', 
		    db('article')->alias('a')->join('article_cat ac', "ac.cat_id = a.cat_id")
// 		    ->where('ac.cat_name', '公告')
		    ->field('article_id,title,add_time')->limit(8)->select()
		    );
		//六大营销下的子模式
		$mod_task_promotion = new MTaskPromotion();
		$all_child_list = $mod_task_promotion->getAllChildListHasTask();
		$this->assign('all_child_list', $all_child_list);
		//公告位暂时放任务数据(免费福利、产品体验，促销活动)
		$plist = db('task')->alias('t')
			->join('task_promotion tp', 'tp.id = t.promotion_id', 'left')
			->where('status', 1)->whereIn('parent_promotion_id', [1,2,3])
			->order('brought_total desc')
			->field('t.*,tp.promotion_name')
			->limit(7)->select();

		$this->assign('hot_task_list', $plist);
		//公告栏。
		$notic_cat_id = 1002;
		$notic_list = db('article')
		->where('cat_id', $notic_cat_id)
		->where('is_open', 1)
		->order('article_type','desc')->order('add_time', 'desc')
		->limit(5)->select();
		is_array($notic_list) || $notic_list=[];
		$this->assign('notic_list', $notic_list);

		$res=Db::table('xybt_channel_company')->field('id,name,order_total')->limit(5)->select();
		$this->assign('res',$res);
        return $this->fetch();
    }

    
    /**
     * "小蚁学院"页，（达人级别进度）
     * @author 谭武云
     * @date 2017年9月13日
     */
    public function rank(){
    	
    	//达人等级
    	$rank_list = MUserRank::getInstance()->getAll();
    	foreach ($rank_list as $k => $v){
    		$rank_list[$k] = $v->toArray();
    	}
    	$this->assign('rank_list', $rank_list);
    	//获取达人升级条件及“我”的积分
    	$user_id = 0;
    	if(!!$this->user){
    		$user_id = $this->user->user_id;
    	}
    	$rank_upgrade_list = db('user_rank_upgrade')->alias('uru')
	    	->join('user_rank_upgrade_log urul', 'urul.upgrade_id = uru.id and urul.user_id=' . $user_id, 'left')
	    	->group('uru.id')->field('uru.*,ifnull(sum(add_point),0) user_point')
    		->select();
    	$rank_upgrade_list = array_combine(array_column($rank_upgrade_list, 'id'), $rank_upgrade_list);
    	$this->assign('rank_upgrade_list', $rank_upgrade_list);
    	
    	$mod = new MTask();
    	//奖励任务
    	$task_list_1= $mod->getList([
    		'parent_promotion_id'=>2,
    		'pagesize' => 5,
    		'order'=>'t.is_recommend desc, t.sort_order, t.add_time desc '
    	]);
    	$this->assign('task_list_1', $task_list_1);
    	//分红任务
    	$task_list_2= $mod->getList([
    		'parent_promotion_id'=>1,
    		'pagesize' => 5,
    		'order'=>'t.is_recommend desc, t.sort_order, t.add_time desc '
    	]);
    	$this->assign('task_list_2', $task_list_2);
    	//消费任务
    	$task_list_3= $mod->getList([
    		'parent_promotion_id'=>3,
    		'pagesize' => 5,
    		'order'=>'t.is_recommend desc, t.sort_order, t.add_time desc '
    	]);
        $this->assign('task_list_3', $task_list_3);
    	//常见问题
        $art = db('article')->where(['cat_id' =>1005])->select();
    	$this->assign('qa_list', $art);
    	return $this->fetch();
    }
    
    function aboutus(){
    	$this->que_menu();
    	$art = db('article')->where('article_id', 58)->find();
    	$this->assign('art', $art);
    	return $this->fetch();
    }
    
    
    
    function contactus(){
    	$this->que_menu();
    	$art = db('article')->where('article_id', 4)->find();
    	$this->assign('art', $art);
    	return $this->fetch();
    }
    
    function joinus(){
    	$this->que_menu();
    	$art = db('article')->where('article_id', 62)->find();
    	$this->assign('art', $art);
    	return $this->fetch();
    }
    
    /**
     * 常见问题列表
     * #TODO 暂时获取所有文章达到显示效果，待文章分类确定后修正cat_id值   帮助中心 分类cat_id   1005
     * @author 谭武云
     * @date 2017年9月13日
     * @return mixed|string
     */
    function quention(){
    	$this->que_menu();
//    	$art = db('article')->where('article_id', 59)->find();
        $art = db('article')->where(['cat_id' =>1005])->select();
    	$this->assign('list', $art);
        return $this->fetch();
    }
    
    /**
     * 常见问题详情页
     * @author 谭武云
     * @date 2017年9月13日
     * @return mixed|string
     */
    function quentioninfo(){
    	$this->que_menu();
    	$id = input('key',0,'int');
    	if(!$id){
    		//参数错误，跳转到列表页
    		$this->redirect('index/quention');
    	}
    	$art = db('article')->where('article_id', $id)->find();
    	$this->assign('art', $art);
    	return $this->fetch();
    }
    
    public function notice(){
    	$this->que_menu();
    	$id = input('id', 0, 'int');
    	if($id > 0){
    		$art = db('article')->where('article_id', $id)->find();
    		$this->assign('art', $art);
    		return $this->fetch('notice_info');
    	}else{
    		$notic_cat_id = 1002;
    		$notic_list = db('article')
    		->where('cat_id', $notic_cat_id)
    		->where('is_open', 1)
    		->order('article_type','desc')->order('add_time', 'desc')
    		->limit(5)->select();
    		is_array($notic_list) || $notic_list=[];
    		$this->assign('list', $notic_list);
//     		\anywhere\FW::debug($notic_list);
    		return $this->fetch('notice_list');
    	}
    }

    public function sharethehall(){

        $field ='ts.*,u.nick_name,user_picture,t.share_url,t.logo_img';
        $type = input('type', 1, 'int');
        $page = input('pno', 1, 'int');;
        $pagesize = input('pagesize', 15, 'int');

        $offset = ($page-1)*$pagesize;
        $length = $pagesize;
        if($type == 1){
            //最新分享(按时间倒序排序)
            $orderby = ' ts.add_time DESC ';
        }elseif($type == 2){
            //热门分享(按分享数倒序排序)
            $orderby = ' t.share_total DESC,ts.add_time ASC';
        }elseif($type == 3){
            //推荐分享(按点赞数倒序排序)
            $orderby = ' ts.favour_total DESC,ts.add_time ASC';
        }else{
            //热门收藏(按收藏数倒序排序)
            $orderby = ' t.collect_total DESC,ts.add_time ASC';
        }
        $shareList = db('task_share')->alias('ts')->join('users u','ts.user_id=u.user_id','left')->join('task t','ts.task_id=t.id','left')->field($field)->order($orderby)->paginate($pagesize,null,['page'=>$page])->toArray();

        $userInfo = $this->user ? $this->user->toArray() : null;

        if(!empty($shareList['data'])){
            foreach ($shareList['data'] as $k=>$v) {
                //评论数量
                $shareList['data'][$k]['comment_number'] = db('task_share_comment')->where(['task_share_id'=>$v['id']])->count('id');
                if(!empty($userInfo)){
                    $is_point = db('task_share_favour')->where(['task_share_id'=>$v['id'],'user_id'=>$userInfo['user_id']])->find();
                }
                if(isset($is_point) && !empty($is_point)){
                    $shareList['data'][$k]['is_point'] = 1;
                }else{
                    $shareList['data'][$k]['is_point'] = 0;
                }
                //评论列表
                $comomentList =  db('task_share_comment')->where(['task_share_id'=>$v['id']])->field('id,task_share_id,content,user_id,respond_user_id,add_time')->order('add_time ASC')->select();
                if(!empty($comomentList)){
                    foreach($comomentList as $key=>$val){
                        $comomentList[$key]['username'] = db('users')->where(['user_id'=>$val['user_id']])->field('nick_name,user_picture')->find();
                        if(!empty($val['respond_user_id'])){
                            $comomentList[$key]['respond_user_name'] = db('users')->where(['user_id'=>$val['respond_user_id']])->field('nick_name,user_picture')->find();
                        }
                        $comomentList[$key]['mine'] = empty($userInfo)?0:($userInfo['user_id']==$val['user_id']?1:0);
                    }
                    $shareList['data'][$k]['comment_list'] = $comomentList;
                }else{
                    $shareList['data'][$k]['comment_list'] = [];
                }
            }
        }

        $mod = new MTask();
        //"热门市场营销"（5个）“热门商品营销”（5个）
        $hot_1 = $mod->getList([
            'parent_promotion_id' => 4,
            'pagesize' => 5
        ]);
        if(!empty($hot_1)){
            foreach($hot_1 as $k=>$v){
                $hot_1[$k]['banner_img'] = get_image_path($v['banner_img']);
                $hot_1[$k]['logo_img'] = get_image_path($v['logo_img']);
            }
        }
        $hot_2 = $mod->getList([
            'parent_promotion_id' => 2,
            'pagesize' => 5
        ]);
        if(!empty($hot_2)){
            foreach($hot_2 as $k=>$v){
                $hot_2[$k]['banner_img'] = get_image_path($v['banner_img']);
                $hot_2[$k]['logo_img'] = get_image_path($v['logo_img']);
            }
        }

        //        print_r($shareList);die;
        $this->assign('shareList',$shareList);
        $this->assign('hot_1',json_encode($hot_1));
        $this->assign('hot_2',json_encode($hot_2));
        return $this->fetch('sharethehall');
    }


    //分享点赞\取消点赞
    public function shareFavour(){
    	$user = $this->user ? $this->user->toArray() : null;

        if(empty($user)){
            return json(['code'=>-1,'msg'=>'请先登录']);
        }

        $shareId = input('shareId', 0, 'int');
        $isFavour = input('isFavour',0,'int');

        if($shareId==0){
            return json(['code'=>0,'msg'=>'参数有误']);
        }

        $taskShare = db('task_share')->where(['id'=>$shareId])->find();
        if(empty($taskShare)){
            return json(['code'=>0,'msg'=>'不存在该分享任务']);
        }

        $shareFavour = db('task_share_favour')->where(['task_share_id'=>$shareId,'user_id'=>$user['user_id']])->find();

        if($isFavour==1 && empty($shareFavour)){//点赞
            $res = db('task_share_favour')->insert(['task_share_id'=>$shareId,'user_id'=>$user['user_id'],'add_time'=>time()]);
            db('task_share')->where(['id'=>$shareId])->setInc('favour_total',1);
        }else if($isFavour==2 && !empty($shareFavour)){//取消点赞
            $res = db('task_share_favour')->delete(['id'=>$shareFavour['id']]);
            db('task_share')->where(['id'=>$shareId])->setDec('favour_total',1);
        }else{
            $res = 0;
        }

        if($res){
            return json(['code'=>1,'msg'=>'操作成功','data'=>[]]);
        }else{
            return json(['code'=>0,'msg'=>'操作失败','data'=>[]]);
        }
    }

    //评论
    public function shareComment(){
    	$user = $this->user ? $this->user->toArray() : null;

        if(empty($user)){
            return json(['code'=>-1,'msg'=>'请先登录']);
        }

        $content = addslashes(input('content','','string'));
        $task_share_id = input('shareId',0,'int');
        $respond_user_id = input('resUserId',0,'int');

        if(empty($content) || empty($task_share_id)){
            return json(['code'=>0,'msg'=>'参数有误','data'=>[]]);
        }

        $taskShare = db('task_share')->where(['id'=>$task_share_id])->find();
        if(empty($taskShare)){
            return json(['code'=>0,'msg'=>'不存在该分享任务','data'=>[]]);
        }

        if($respond_user_id>0){
            $respond_user = db('users')->where(['user_id'=>$respond_user_id])->find();
            $respond_user_id = empty($respond_user)?0:$respond_user_id;
        }

        $data = [
            'task_share_id'=>$task_share_id,
            'content'=>$content,
            'respond_user_id'=>$respond_user_id,
            'user_id'=>$user['user_id'],
            'add_time'=>time()
        ];

        $res = db('task_share_comment')->insert($data);

        $resData = [
            'content'=>$content,
            'add_time'=>formatDateDiffNow($data['add_time']),
            'user'=>[
                'user_id'=>$user['user_id'],
                'user_picture'=>get_image_path($user['user_picture']),
                'nick_name'=>$user['nick_name']
            ],
            'respond'=>[]
        ];

        if(!empty($respond_user)){
            $resData['respond'] = [
                'respond_user_id'=>$respond_user['user_id'],
                'respond_user_name'=>$respond_user['nick_name']
            ];
        }

        if($res){
            return json(['code'=>1,'msg'=>'评论成功','data'=>$resData]);
        }else{
            return json(['code'=>0,'msg'=>'评论失败','data'=>[]]);
        }

    }

    public function app_intro(){
    	$mod = MUserInvitation::getInstance();
    	$user_id = request()->param('parent_id', 0);
    	$data = $mod->getOneByUserId($user_id);
    	$this->assign('data', $data->toArray());
    	return $this->fetch();
    }
    //搜索页
    public function search(){
        $search = input('search','','string');
        $type = input('type',1,'int');//1需求，2商品，3应用
        $orderBy = input('orderBy',0,'int');//排序 0,默认  1,最高人气  2,最新发布
        $category = input('category',0,'int');//0，全部  1-免费福利，2-产品体验，3-促销活动，4-网络营销，5-产品分销，6-广告投放
        $page = input('pno',1,'int');//分页
        $pageSize = 15; //每页条数
        $result = [];

        if($orderBy==1){
            $order = 'brought_total desc';  //人气
        }else if($orderBy==2){
            $order = 'add_time desc';       //发布
        }

        $keywordList = $this->hotSearch();
        if($type==1){
            $filed = 't.*,tp.promotion_name as promotion_name';
            $task = db('task')->alias('t')->join('task_promotion tp','t.parent_promotion_id=tp.id')->field($filed);//模糊匹配

            if(!empty($search)){
                $task->where('name|content','like','%'.$search.'%');
            }

            if($category>0){
                $task = $task->where('parent_promotion_id','=',$category);//六大营销分类
            }

            if($orderBy>0 && $orderBy<3){
                $task = $task->order($order);   //排序
            }

            $result = $task->paginate($pageSize,null,['page'=>$page])->toArray();

            if(!empty($result['data'])){
                foreach($result['data'] as $k=>$v){
                    $demand = db('task_promotion')->where(['id'=>$v['promotion_id']])->find('promotion_name');
                    $result['data'][$k]['prom_name']=$demand['promotion_name'];
                }

                if(!empty($search)){
                    $keywordList = $this->hotSearch($search);
                }

            }
        }else if($type==3){

        }

        $this->assign([
            'pageUrl'=>url('index/search',['type'=>$type,'search'=>$search,'orderBy'=>$orderBy,'category'=>$category]),
            'data'=>$result,
            'search'=>$search,
            'type'=>$type,
            'orderBy'=>$orderBy,
            'category'=>$category,
            'keywordList'=>$keywordList
        ]);
        return $this->fetch();
    }

    //存储/获取热搜关键词
    private function hotSearch($keyword=null){
        $redis =  \RedisCache::connect();
        if(!empty($keyword)){
            $redis ->zIncrBy('hotSearch',1,$keyword);
            $list = $redis->zRevRange('hotSearch',0,20);
        }else{
            $list = $redis->zRevRange('hotSearch',0,20);
        }

        return $list;
    }

    //加盟大厅
    public function joinhall(){
        if(!$this->user){
            $this->redirect('login/index', ['from' => urlencode(request()->path())]);
        }
        //判断是否是企业创建者
        if(empty($this->company) || $this->company['founder_user_id'] != $this->user->user_id || $this->company['status']!=1){
            return $this->redirect('index/joinhall_no',['status'=>0]);
        }else{
            $postAgentInfo = db('post_agent')->where(['company_id'=>$this->company['id']])->find();
            //判断是否是代理
            if(empty($postAgentInfo)){
                return $this->redirect('index/joinhall_no',['status'=>1]);
            }else{
                $arr = [
                    'performance'=>$postAgentInfo['performance'],//KPI
                    'finished_rate'=>$postAgentInfo['performance']==0?0:round($postAgentInfo['performance_finished'] * 100 / $postAgentInfo['performance']).'%',//完成率
                    'staff_num'=>db('users')->where(['company_id'=>$this->company['id']])->count('*'),//团队人数
                    'agent_type'=>$postAgentInfo['agent_type']
                ];

                //待领取任务
                $pageSize = 5;
                $taskList = db('post_recommend')->alias('pr')
                    ->join('users u','pr.user_id=u.user_id','left')
                    ->join('task t','t.id=pr.task_id','left')
                    ->join('task_exam_time_type tett','tett.id = t.exam_time_type_id','left')
                    ->join('task_promotion tp', 'tp.id=t.promotion_id', 'left')
                    ->where(['u.company_id'=>$this->company['id']])
                    ->whereIn('t.parent_promotion_id',[4,5,6],'AND')
                    ->order('pr.sort_order DESC,pr.id DESC')
                    ->field('t.*,tett.name as exam_time_name,tp.promotion_name')
                    ->paginate($pageSize, null, ['page'=>input('page',1,'int')])->toArray();
            }
        }

        $this->assign([
            'info'=>empty($arr)?'':$arr,
            'task_list'=>empty($taskList)?'':$taskList
        ]);

        return $this->fetch();
    }

    //我要培训
    public function joinhall_train(){
        if(!$this->user){
            $this->redirect('login/index', ['from' => urlencode(request()->path())]);
        }
        //判断是否是企业创建者
        if(empty($this->company) || $this->company['founder_user_id'] != $this->user->user_id || $this->company['status']!=1){
            return $this->redirect('index/joinhall_no',['status'=>0]);
        }else{
            $postAgentInfo = db('post_agent')->where(['company_id'=>$this->company['id']])->find();
            //判断是否是代理
            if(empty($postAgentInfo)){
                return $this->redirect('index/joinhall_no',['status'=>1]);
            }else{
                $arr = [
                    'performance'=>$postAgentInfo['performance'],//KPI
                    'finished_rate'=>$postAgentInfo['performance']==0?0:round($postAgentInfo['performance_finished'] * 100 / $postAgentInfo['performance']).'%',//完成率
                    'staff_num'=>db('users')->where(['company_id'=>$this->company['id']])->count('*'),//团队人数
                    'agent_type'=>$postAgentInfo['agent_type']
                ];
            }
        }

        $this->assign([
            'info'=>empty($arr)?'':$arr
        ]);

        return $this->fetch();
    }

    //已领取任务
    public function joinhall_task(){
        if(!$this->user){
            $this->redirect('login/index', ['from' => urlencode(request()->path())]);
        }
        //判断是否是企业创建者
        if(empty($this->company) || $this->company['founder_user_id'] != $this->user->user_id || $this->company['status']!=1){
            return $this->redirect('index/joinhall_no',['status'=>0]);
        }else{
            $postAgentInfo = db('post_agent')->where(['company_id'=>$this->company['id']])->find();
            //判断是否是代理
            if(empty($postAgentInfo)){
                return $this->redirect('index/joinhall_no',['status'=>1]);
            }else{
                $arr = [
                    'performance'=>$postAgentInfo['performance'],//KPI
                    'finished_rate'=>$postAgentInfo['performance']==0?0:round($postAgentInfo['performance_finished'] * 100 / $postAgentInfo['performance']).'%',//完成率
                    'staff_num'=>db('users')->where(['company_id'=>$this->company['id']])->count('*'),//团队人数
                    'agent_type'=>$postAgentInfo['agent_type']
                ];
                $pageSize =5;
                $taskList = db('post_recommend')->alias('pr')
                    ->join('users u','pr.user_id=u.user_id','left')
                    ->join('task t','t.id=pr.task_id','left')
                    ->join('task_user tu','tu.user_id=u.user_id','left')
                    ->join('task_exam_time_type tett','tett.id = t.exam_time_type_id','left')
                    ->join('task_promotion tp', 'tp.id=t.promotion_id', 'left')
                    ->where(['u.company_id'=>$this->company['id']])
                    ->whereIn('t.parent_promotion_id',[4,5,6],'AND')
                    ->field('t.*,tett.name as exam_time_name,tp.promotion_name')
                    ->paginate($pageSize, null, ['page'=>input('page',1,'int')])->toArray();
            }
        }

        $this->assign([
            'info'=>empty($arr)?'':$arr,
            'task_list'=>empty($taskList)?'':$taskList
        ]);

        return $this->fetch();
    }

    //加盟未加入前的页面
    public function joinhall_no(){
        $status = input('status',0);
        //等于1去判断是否申请加盟
        if($status==1){
            $applyInfo = db('post_agent_apply')->where(['company_id'=>$this->company['id'],'user_id'=>$this->user->user_id,'status'=>0])->find();
            if(!empty($applyInfo)){
                $status =2;
            }
        }
        $this->assign([
            'status'=>$status
        ]);
        return $this->fetch('joinhall_no');
    }

    //代理规则
    public function joinhall_camp(){
        if(!$this->user){
            $this->redirect('login/index', ['from' => urlencode(request()->path())]);
        }
        //判断是否是企业创建者
        if(empty($this->company) || $this->company['founder_user_id'] != $this->user->user_id || $this->company['status']!=1){
            return $this->redirect('index/joinhall_no',['status'=>0]);
        }else{
            $postAgentInfo = db('post_agent')->where(['company_id'=>$this->company['id']])->find();
            //判断是否是代理
            if(empty($postAgentInfo)){
                return $this->redirect('index/joinhall_no',['status'=>1]);
            }else{
                $arr = [
                    'performance'=>$postAgentInfo['performance'],//KPI
                    'finished_rate'=>$postAgentInfo['performance']==0?0:round($postAgentInfo['performance_finished'] * 100 / $postAgentInfo['performance']).'%',//完成率
                    'staff_num'=>db('users')->where(['company_id'=>$this->company['id']])->count('*'),//团队人数
                    'agent_type'=>$postAgentInfo['agent_type']
                ];
            }
        }
        $this->assign([
            'info'=>empty($arr)?'':$arr
        ]);
        return $this->fetch();
    }

    //申请加盟代理
    public function joinhall_in(){
        if(!$this->user){
            $this->returnJson('',0,'请先登录');
        }
        if(empty($this->company) || $this->company['founder_user_id'] != $this->user->user_id || $this->company['status']!=1){
            $this->returnJson('',0,'非企业创建人无法申请加盟');
        }
        $applyInfo = db('post_agent_apply')->where(['company_id'=>$this->company['id'],'user_id'=>$this->user->user_id,'status'=>0])->find();
        if(!empty($applyInfo)){
            $this->returnJson('',0,'已提交申请，请耐心等待');
        }
        $res = db('post_agent_apply')->insert([
            'company_id'=>$this->company['id'],
            'user_id'=>$this->user->user_id,
            'status'=>0,
            'add_time'=>time()
        ]);
        if($res){
            $this->returnJson('',1,'申请成功');
        }else{
            $this->returnJson('',0,'申请失败');
        }
    }


    //兼职代理
    public function parttime(){
        if(!$this->user){
            $this->redirect('login/index', ['from' => urlencode(request()->path())]);
        }
        $user_rank = db('user_rank')->where(['rank_id'=>$this->user->user_rank])->find();
        $postUser = db('post_user')->where(['user_id'=>$this->user->user_id])->find();
        if(empty($postUser)){
            return $this->redirect('index/parttime_no');
        }
        //待领取任务
        $pageSize = 5;
        $taskList = db('post_recommend')->alias('pr')
            ->join('users u','pr.user_id=u.user_id','left')
            ->join('task t','t.id=pr.task_id','left')
            ->join('task_exam_time_type tett','tett.id = t.exam_time_type_id','left')
            ->join('task_promotion tp', 'tp.id=t.promotion_id', 'left')
            ->where(['u.user_id'=>$this->user->user_id])
            ->whereIn('t.parent_promotion_id',[1,2,3],'AND')
            ->order('pr.sort_order DESC,pr.id DESC')
            ->field('t.*,tett.name as exam_time_name,tp.promotion_name')
            ->paginate($pageSize, null, ['page'=>input('page',1,'int')])->toArray();
        $this->assign([
            'user_info'=>[
                'nick_name'=>$this->user->nick_name,
                'user_picture'=>get_image_path($this->user->user_picture),
                'rank_name'=>$user_rank['rank_name'],
                'vip_level'=>$user_rank['sort_order'],
                'kpi'=>$postUser['performance'],
                'post_type'=>$postUser['post_type'],
                'finish_rate'=>$postUser['performance']==0?0:round($postUser['performance_finished'] * 100 / $postUser['performance']).'%',//完成率
            ],
            'task_list'=>$taskList
        ]);

        return $this->fetch();
    }


    //兼职代理-已领取任务
    public function parttime_task(){
        if(!$this->user){
            $this->redirect('login/index', ['from' => urlencode(request()->path())]);
        }
        $user_rank = db('user_rank')->where(['rank_id'=>$this->user->user_rank])->find();
        $postUser = db('post_user')->where(['user_id'=>$this->user->user_id])->find();
        if(empty($postUser)){
            return $this->redirect('index/parttime_no');
        }

        $pageSize =5;
        $taskList = db('post_recommend')->alias('pr')
            ->join('users u','pr.user_id=u.user_id','left')
            ->join('task_user tu','tu.user_id=u.user_id','left')
            ->join('task t','t.id=pr.task_id AND t.id=tu.task_id','left')
            ->join('task_exam_time_type tett','tett.id = t.exam_time_type_id','left')
            ->join('task_promotion tp', 'tp.id=t.promotion_id', 'left')
            ->where(['u.user_id'=>$this->user->user_id])
            ->whereIn('t.parent_promotion_id',[1,2,3],'AND')
            ->field('t.*,tett.name as exam_time_name,tp.promotion_name')
            ->paginate($pageSize, null, ['page'=>input('page',1,'int')])->toArray();

        $this->assign([
            'user_info'=>[
                'nick_name'=>$this->user->nick_name,
                'user_picture'=>get_image_path($this->user->user_picture),
                'rank_name'=>$user_rank['rank_name'],
                'vip_level'=>$user_rank['sort_order'],
                'kpi'=>$postUser['performance'],
                'post_type'=>$postUser['post_type'],
                'finish_rate'=>$postUser['performance']==0?0:round($postUser['performance_finished'] * 100 / $postUser['performance']).'%',//完成率
            ],
            'task_list'=>$taskList
        ]);

        return $this->fetch();
    }

    //兼职代理-我要培训
    public function parttime_train(){
        if(!$this->user){
            $this->redirect('login/index', ['from' => urlencode(request()->path())]);
        }
        $user_rank = db('user_rank')->where(['rank_id'=>$this->user->user_rank])->find();
        $postUser = db('post_user')->where(['user_id'=>$this->user->user_id])->find();
        if(empty($postUser)){
            return $this->redirect('index/parttime_no');
        }

        $this->assign([
            'user_info'=>[
                'nick_name'=>$this->user->nick_name,
                'user_picture'=>get_image_path($this->user->user_picture),
                'rank_name'=>$user_rank['rank_name'],
                'vip_level'=>$user_rank['sort_order'],
                'kpi'=>$postUser['performance'],
                'post_type'=>$postUser['post_type'],
                'finish_rate'=>$postUser['performance']==0?0:round($postUser['performance_finished'] * 100 / $postUser['performance']).'%',//完成率
            ]
        ]);

        return $this->fetch();
    }


    //未加入兼职页面
    public function parttime_no(){
        if(!$this->user){
            $this->redirect('login/index', ['from' => urlencode(request()->path())]);
        }
        return $this->fetch();
    }
    //兼职成长营|兼职大人规则
    public function parttime_camp(){
        if(!$this->user){
            $this->redirect('login/index', ['from' => urlencode(request()->path())]);
        }
        $user_rank = db('user_rank')->where(['rank_id'=>$this->user->user_rank])->find();
        $postUser = db('post_user')->where(['user_id'=>$this->user->user_id])->find();
        if(empty($postUser)){
            return $this->redirect('index/parttime_no');
        }

        $this->assign([
            'user_info'=>[
                'nick_name'=>$this->user->nick_name,
                'user_picture'=>get_image_path($this->user->user_picture),
                'rank_name'=>$user_rank['rank_name'],
                'vip_level'=>$user_rank['sort_order'],
                'kpi'=>$postUser['performance'],
                'post_type'=>$postUser['post_type'],
                'finish_rate'=>$postUser['performance']==0?0:round($postUser['performance_finished'] * 100 / $postUser['performance']).'%',//完成率
            ]
        ]);
        return $this->fetch();
    }

    //加入兼职
    public function parttime_in(){
        if(!$this->user){
            $this->returnJson('',0,'请先登录');
        }
        $applyInfo = db('post_user')->where(['user_id'=>$this->user->user_id])->find();
        if(!empty($applyInfo)){
            $this->returnJson('',1,'已加入兼职代理');
        }
        $res = db('post_user')->insert([
            'user_id'=>$this->user->user_id,
            'post_type'=>1,
            'add_time'=>time(),
        ]);
        if($res){
            $this->returnJson('',1,'加入成功');
        }else{
            $this->returnJson('',0,'加入失败');
        }
    }


}

<?php
namespace app\index\controller;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/22
 * Time: 9:24
 */
use rongCloud\RongCloud;
//const appKey = '8luwapkv8r49l';
//const appSecret = '9xKUVAQqynY';
const appKey = 'tdrvipkstqxz5';
const appSecret = 'lo1NtAShrAQ7';
//const appKey = 'lmxuhwagl0eqd';
//const appSecret = 'JnxxkubA4wVaR6';

class Chat extends Common{

    private $rongCloud;
    private $redis;
    public function __construct(){
        parent::__construct();
        if(!$this->user){
        	$this->redirect('login/index', ['from' => urlencode(request()->path())]);
        }

        $this->rongCloud = new RongCloud(appKey,appSecret);
    }

    public function index(){

        //远程获取token
        $user_picture = empty($this->user->user_picture)?'/resources/images/touxiang.png':get_image_path($this->user->user_picture);//用户图像

        $nick_name = empty($this->user->nick_name)?$this->user->user_name:$this->user->user_name;//用户名称
        $res = json_decode($this->rongCloud->User()->getToken($this->user->user_id,$nick_name,$user_picture),true);

        if($res['code']!=200){
            $this->redirect('/index');
            exit();
        }

        //用户聊天人记录   userChatContactHistoryList
        $userCCHList ='';

        //用户个人聊天记录
        $userCOH = null;
//
//        $userList = db('users')->paginate(200,null, ['page'=>input('page',1,'int')])->toArray();
//
//        foreach($userList['data'] as $k=>$v){
//            $userList['data'][$k]['rank'] = db('user_rank')->find($v['user_rank']);
//            if($v['user_id']==$this->user->user_id){
//                unset($userList['data'][$k]);
//            }
//        }

        //亲友圈
        $relativesList = db('users')->where(['parent_id'=>$this->user->user_id])->select();
        //朋友圈
        $friendList = db('users')->where(['two_parent_id'=>$this->user->user_id])->select();
        //关系圈
        $relationList = db('users')->where(['three_parent_id'=>$this->user->user_id])->select();

        //我的群组
        $groupList = db('user_group_about')->where(['user_id'=>$this->user->user_id])->alias('uga')->join('user_group ug','uga.group_id=ug.id','LEFT')->select();

        $this->joinGroup($groupList);

        $this->assign('token',$res['token']);
        $this->assign('appkey',appKey);
        $this->assign('curuser',$this->user ? $this->user->toArray() : null);
        $this->assign('relativesList',$relativesList);
        $this->assign('friendlist',$friendList);
        $this->assign('relationList',$relationList);
        $this->assign('groupList',$groupList);

        return $this->fetch();
    }

    public function index01(){

        //远程获取token
        $user_picture = empty($this->user->user_picture)?'/resources/images/touxiang.png':get_image_path($this->user->user_picture);//用户图像
        $res = json_decode($this->rongCloud->User()->getToken($this->user->user_id,$this->user->user_name,$user_picture),true);

        if($res['code']!=200){
            $this->redirect('/index');
            exit();
        }
        //我的上级
        $parentsList = [];
        $userInfo = db('users')->where(['user_id'=>$this->user->user_id])->find();
        if($userInfo['parent_id']>0){
            $u = db('users')->where(['user_id'=>$userInfo['parent_id']])->alias('u')->join('user_rank uk','u.user_rank=uk.rank_id','LEFT')->find();
            $parentsList[] = $u;
        }
        if($userInfo['two_parent_id']>0){
            $u = db('users')->where(['user_id'=>$userInfo['two_parent_id']])->alias('u')->join('user_rank uk','u.user_rank=uk.rank_id','LEFT')->find();
            $parentsList[] = $u;
        }
        if($userInfo['three_parent_id']>0){
            $u = db('users')->where(['user_id'=>$userInfo['three_parent_id']])->alias('u')->join('user_rank uk','u.user_rank=uk.rank_id','LEFT')->find();
            $parentsList[] = $u;
        }
        //亲友圈
        $relativesList = db('users')->where(['parent_id'=>$this->user->user_id])->alias('u')->join('user_rank uk','u.user_rank=uk.rank_id','LEFT')->select();
        //朋友圈
        $friendList = db('users')->where(['two_parent_id'=>$this->user->user_id])->alias('u')->join('user_rank uk','u.user_rank=uk.rank_id','LEFT')->select();
        //关系圈
        $relationList = db('users')->where(['three_parent_id'=>$this->user->user_id])->alias('u')->join('user_rank uk','u.user_rank=uk.rank_id','LEFT')->select();
        //群组列表
        $groupList = db('user_group_about')->where(['user_id'=>$this->user->user_id])->alias('uga')->join('user_group ug','uga.group_id=ug.id','LEFT')->select();

        //将已经加入去群组的，没有加入融云聊天的加进去
        $this->joinGroup($groupList);
        //获取最近联系的人
        $contact = null;
        $contectStr = '';
        //判断是否是从我的兵团过来的
        $uid = input('uid');
        if(!empty($uid)){
            $uidInfo = $u = db('users')->where(['user_id'=>$uid])->alias('u')->join('user_rank uk','u.user_rank=uk.rank_id','LEFT')->select();

            if(!empty($uidInfo)){
                $contact = $uidInfo;
                $contectStr = $uid.'_1,';
            }
        }

        $this->assign('lastContactList',$contact);
        $this->assign('lastContactStr',$contectStr);

        $this->assign('token',$res['token']);
        $this->assign('appkey',appKey);
        $this->assign('curuser',$this->user ? $this->user->toArray() : null);
        $this->assign('relativesList',$relativesList);
        $this->assign('friendList',$friendList);
        $this->assign('relationList',$relationList);
        $this->assign('groupList',$groupList);
        $this->assign('parentsList',$parentsList);

        return $this->fetch();
    }



    //如果群组不为空，加入融云群组
    public function joinGroup($groupList){
        foreach($groupList as $k=>$v){
            $res = $this->rongCloud->group()->create([$this->user->user_id], $v['id'], 'groupName1');
        }
    }


    //发送文本信息,暂时分为   私聊|群聊
    public function sendMessage(){
        $post = request()->post();
        if(!isset($post['targetid']) || empty($post['targetid'])){
            return $this->returnJson(null,0,'参数错误');
        }
        $pushData = time();
        if($post['type']==1){//私聊
            $targetUser = db('users')->where(['user_id'=>$post['targetid']])->find();
            if(!isset($targetUser) || empty($targetUser)){
                return $this->returnJson(null,0,'目标用户不存在');
            }

            $content = json_encode([
                'content'=>$post['content'],
                'user'=>[
                    'id'=>$this->user->user_id,
                    'name'=>empty($this->user->nick_name)?$this->user->user_name:$this->user->nick_name,
                    'portrait'=>empty($this->user->user_picture)?'http://'.$_SERVER['SERVER_NAME'].'/resources/images/touxiang.png':get_image_path($this->user->user_picture),
                    'rank_name'=>$this->user->rank['rank_name']
                ],
                'extra'=>[]
            ]);

            $res = $this->rongCloud->message()->publishPrivate($this->user->user_id,$post['targetid'],'RC:TxtMsg',$content,'',$pushData,'','1','1','1','0');

        }elseif($post['type']==3){//群聊
            $targetGroup = db('user_group')->where(['id'=>$post['targetid']])->find();
            if(!isset($targetGroup) || empty($targetGroup)){
                return $this->returnJson(null,0,'目标群组不存在');
            }

            $group = [
                'id'=>$targetGroup['id'],
                'name'=>$targetGroup['group_name'],
                'headimg'=>$targetGroup['group_img']
            ];
            $content = json_encode([
                'content'=>$post['content'],
                'user'=>[
                    'id'=>$this->user->user_id,
                    'name'=>empty($this->user->nick_name)?$this->user->user_name:$this->user->nick_name,
                    'portrait'=>empty($this->user->user_picture)?'http://'.$_SERVER['SERVER_NAME'].'/resources/images/touxiang.png':get_image_path($this->user->user_picture),
                    'rank_name'=>$this->user->rank['rank_name']
                ],
                'extra'=>$group
            ]);

            $res = $this->rongCloud->message()->publishGroup($this->user->user_id, $post['targetid'], 'RC:TxtMsg',$content, '', '', '1', '1', '0');
        }

        $result = json_decode($res,true);

        if($result['code']==200){
            //插入聊天记录
            //$this->setRecord($this->user->user_id,$post['targetid'],$post['type'],$content);
            //插入最近联系人
            //$this->setRecordContact($this->user->user_id,$post['targetid'],$post['type']);
            return $this->returnJson(null,1,'发送成功');
        }else{
            $r = ['code'=>$result['code']];
            return $this->returnJson($r,1,'发送失败');
        }

    }

    public function test(){
        return $this->fetch();
    }

    //插入聊天记录
    private function setRecord($sendUserId,$receiveUserId,$type,$content){

        $arr = [
            'send_user_id'=>$sendUserId,
            'receive_user_id'=>$receiveUserId,
            'type'=>$type,
            'content'=>$content,
            'send_time'=>time(),
            'created_at'=>time()
        ];

        $res = db('user_records')->insert($arr);

        if($res){
            return true;
        }else{
            return false;
        }
    }

    //插入或者更新最近了联系人      使用redis的集合
    private function setRecordContact($sendUserId,$receiveUserId,$type){
        $redis = \RedisCache::connect();

        $contactInfo = $redis->sIsMember($sendUserId,$receiveUserId.'_'.$type);

        if(!$contactInfo){
            $redis->sAdd($sendUserId,$receiveUserId.'_'.$type);
        }

        //单聊模式
        if($type==1 && !$redis->sIsMember($receiveUserId,$sendUserId.'_'.$type)){//为接收方添加一条联系人记录
            $redis->sAdd($receiveUserId,$sendUserId.'_'.$type);
        }
        return true;
    }

    //获取最近联系人记录     使用redis集合
    private function getRecordContactRedis($sendUserId){
        $redis =  \RedisCache::connect();
        $contactRedis = $redis->sMembers($sendUserId);
        if(empty($contactRedis)) return array('contactArr'=>null,'contactStr'=>null);
        $contact_str = '';
        $contactArr = null;
        foreach($contactRedis as $k=>$v){
            $arr = explode('_',$v);
            if($arr[1]==1){
                $u_info = db('users')->where(['user_id'=>$arr[0]])->find();
                $rank = db('user_rank')->where(['rank_id'=>$u_info['user_rank']])->find();
                $contactArr[] = array(
                    'receive_user_id'=>$arr[0],
                    'receive_user_name'=>empty($u_info['nick_name'])?$u_info['user_name']:$u_info['nick_name'],
                    'receive_user_img'=>$u_info['user_picture'],
                    'rank_name'=> $rank['rank_name'],
                    'type'=>$arr[1]
                );
            }else if($arr[1]==3){
                $u_info = db('user_group')->where(['id'=>$arr[0]])->find();
                $contactArr[] = array(
                    'receive_user_id'=>$arr[0],
                    'receive_user_name'=>$u_info['group_name'],
                    'receive_user_img'=>$u_info['group_img'],
                    'type'=>$arr[1]
                );
            }

            $contact_str .= $v.',';
        }

        return array(
            'contactArr'=>$contactArr,
            'contactStr'=>$contact_str
        );
    }

    //获取最近联系人记录
    private function getRecordContact($sendUserId){
        $contactArr = db('user_record_contact')->where(['send_user_id'=>$sendUserId])->whereOr(['receive_user_id'=>$sendUserId])->order(['update_at'=>'DESC'])->select();
        $contact_str = '';
        if(empty($contactArr)) return array('contactArr'=>null,'contactStr'=>null);
        foreach($contactArr as $k=>$v){

            if($sendUserId==$v['send_user_id']){
                if($v['type']==1){
                    $u_info = db('users')->where(['user_id'=>$v['receive_user_id']])->find();
                    $rank = db('user_rank')->where(['rank_id'=>$u_info['user_rank']])->find();
                    $contactArr[$k]['receive_user_name'] = empty($u_info['nick_name'])?$u_info['user_name']:$u_info['nick_name'];
                    $contactArr[$k]['receive_user_img'] = $u_info['user_picture'];
                    $contactArr[$k]['rank_name'] = $rank['rank_name'];
                }else if($v['type']==3){
                    $u_info = db('user_group')->where(['id'=>$v['receive_user_id']])->find();
                    $contactArr[$k]['receive_user_name'] = $u_info['group_name'];
                    $contactArr[$k]['receive_user_img'] = get_image_path($u_info['group_img']);
                }
            }else if($sendUserId==$v['receive_user_id']){
                if($v['type']==1){
                    $u_info = db('users')->where(['user_id'=>$v['send_user_id']])->find();
                    $rank = db('user_rank')->where(['rank_id'=>$u_info['user_rank']])->find();
                    $contactArr[$k]['receive_user_name'] = empty($u_info['nick_name'])?$u_info['user_name']:$u_info['nick_name'];
                    $contactArr[$k]['receive_user_img'] = get_image_path($u_info['user_picture']);
                    $contactArr[$k]['rank_name'] = $rank['rank_name'];
                }else if($v['type']==3){
                    $u_info = db('user_group')->where(['id'=>$v['send_user_id']])->find();
                    $contactArr[$k]['receive_user_name'] = $u_info['group_name'];
                    $contactArr[$k]['receive_user_img'] = empty($u_info['group_img'])?'/resources/':get_image_path($u_info['group_img']);
                }
            }

            //获取最后一条聊天记录
            $lastRecord = db('user_records')->where(['send_user_id'=>$sendUserId,'receive_user_id'=>$v['receive_user_id'],'type'=>$v['type']])->order(['send_time'=>'DESC'])->find();
            $content = '';
            if(!empty($lastRecord)){
                $content = json_decode($lastRecord['content'],true)['content'];
            }
            $contactArr[$k]['last_record'] = $content;
            $contact_str .= $v['receive_user_id'].'_'.$v['type'].',';
        }

        return array(
            'contactArr'=>$contactArr,
            'contactStr'=>$contact_str
        );
    }

    //获取聊天记录
    private function getRecordList($sendUserId,$receiveUserId,$type){
        $records = db('user_records')->where(['send_user_id'=>$sendUserId,'receive_user_id'=>$receiveUserId,'type'=>$type])->select();

        if(empty($records)) return null;

        return $records;
    }

    //删除最近联系人
    public function delRecordContact(){
        $post = request()->post();
        if(!isset($post['targetid']) || empty($post['targetid'])){
            return $this->returnJson(null,0,'参数错误');
        }

        $redis =  \RedisCache::connect();
        $redis->sRem($this->user->user_id,$post['targetid']);

        return $this->returnJson(null,1,'删除成功');
    }

    //获取联系人的姓名，头像，称谓
    public function getContactInfo(){
        $str = request()->post('key');
        if(!isset($str) || empty($str)){
            return $this->returnJson(null,0,'参数有误');
        }

        $arr = explode('_',$str);
        $res = '';
        if($arr[1] == 1){//私聊
            $data = db('users')->where(['user_id'=>$arr[0]])->alias('u')->join('user_rank uk','u.user_rank=uk.rank_id','LEFT')->column('u.user_id,u.nick_name,u.user_name,u.user_picture,uk.rank_name');
            if(empty($data)) {
                $error = '用户不存在';
            }else{
                $res = array(
                    'id'=>$data[$arr[0]]['user_id'].'',
                    'name'=>empty($data[$arr[0]]['nick_name'])?$data[$arr[0]]['user_name']:$data[$arr[0]]['nick_name'],
                    'portrait'=>empty($data[$arr[0]]['user_picture'])?'http://'.$_SERVER['SERVER_NAME'].'/resources/images/touxiang.png':get_image_path($data[$arr[0]]['user_picture']),
                    'rank_name'=>$data[$arr[0]]['rank_name'],
                    'type'=>$arr[1]
                );
            }
        }else if($arr[1]==3){//群组
            $data = db('user_group')->where(['id'=>$arr[0]])->column('id,group_name,group_img');
            if(empty($data)) {
                $error = '群组不存在';
            }else{
                $res = array(
                    'id'=>$data[$arr[0]]['id'].'',
                    'name'=>$data[$arr[0]]['group_name'],
                    'headimg'=>empty($data[$arr[0]]['group_img'])?'http://'.$_SERVER['SERVER_NAME'].'/resources/images/group-img.png':get_image_path($data[$arr[0]]['group_img']),
                    'type'=>$arr[1]
                );
            }
        }

        if(isset($error)){
            return $this->returnJson(null,0,$error);
        }

        return $this->returnJson($res,1,'获取成功');

    }



}
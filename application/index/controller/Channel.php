<?php
namespace app\index\controller;

use anywhere\FW;
use rpc\RpcClass;

class Channel extends Common{
	
	public function index(){
		$category_id = request()->param('category_id', 1);
		//dump($category_id);
		$this->assign('category_id', $category_id);
		return $this->fetch();
	}
	
	public function test(){
		$class = new RpcClass('Media');
		$s = $class->getAllAttrByGroup(1);
		FW::debug($s);
	}

    public function channel_details(){
        $ccId = intval(input('id', 0));
        $query = db('channel_company')->alias('cc')
            ->join('user_company uc','uc.id=cc.company_id', 'left')
            ->join('channel_company_category ccc','ccc.id=cc.category_id','left')
            ->where(['cc.id'=>$ccId,'cc.status'=>1])
            ->field('cc.*,uc.is_auth,uc.company_head_portrait,ccc.category_name');
        $info = $query->find();
        if(!empty($info)){
            $info['company_head_portrait'] = get_image_path($info['company_head_portrait']);
        }

        $this->assign([
            'info' => $info
        ]);
        return $this->fetch();
    }

    //渠道派单
    public function immediate_dispatch(){
        $id = intval(input('key',0));
        if(empty($this->user->user_id)){
            $this->redirect('login/index', ['from' => urlencode(request()->path())]);
        }
        $query = db('channel_company')->alias('cc')
            ->join('user_company uc','uc.id=cc.company_id', 'left')
            ->join('channel_company_category ccc','ccc.id=cc.category_id','left')
            ->where(['cc.id'=>$id,'cc.status'=>1])
            ->field('cc.*,uc.is_auth,uc.company_head_portrait,ccc.category_name');
        $info = $query->find();
        if(!$id || empty($info)){
            $this->error('该渠道不存在');
        }

        if($_POST){
            $name = input('name','');
            $contact_name = input('contact_name','');
            $contact_phone = input('contact_phone','');
            $promotion_price = floatval(input('promotion_price',0));
            $promotion_budget = floatval(input('promotion_budget',0));
            $content = input('content','');
            $ordersn = getOrdersn();
            //查看有没有下过单，如果有下过单，这个单子的状态是否完成
            $order_query = db('channel_company_order')->where([
                'user_id'=>$this->user->user_id,
                'channel_company_id'=>$info['id']]);
            if(!empty($this->company['id'])){
                $order_query->where([ 'company_id'=>$this->company['id']]);
            }
            $orderInfo = $order_query->find();
            if(!empty($orderInfo) &&($orderInfo['status']!=2 || $orderInfo['status']!=3)){
                $this->error('您已经派过类似的单子了，请耐心的等待派单完成',url('company/channel_dispatch',['category_id'=>$info['category_id']]));
            }
            if(!empty($this->company['id']) && $info['company_id'] == $this->company['id']){
                $this->error('不能给自己派单');
            }
            $file_path = $this->uploadhead();
            $data = [
                'ordersn'=>$ordersn,
                'name'               => $name,
                'contact_name'       => $contact_name,
                'contact_phone'      => $contact_phone,
                'promotion_price'    => $promotion_price,
                'promotion_budget'   => $promotion_budget,
                'file_path'          => $file_path,
                'content'            => $content,
                'channel_company_id' => $info['id'],
                'company_id'         => empty($this->company['id'])?0:$this->company['id'],
                'user_id'            => $this->user->user_id,
                'add_time'           => time()
            ];
            $res = db('channel_company_order')->insert($data);
            if($res){
                $this->success('派单成功',url('company/channel_dispatch',['category_id'=>$info['category_id']]));
            }else{
                $this->success('派单失败');
            }
        }
        $this->assign([
            'info'=>$info
        ]);
        return $this->fetch();
    }

    /**
     * 上传头像
     */
    private function uploadhead(){
        try{
            $file = request()->file('file_path');

            if(empty($file)) return false;
            $path = 'static/channel_file';
            $file_name = 'channel_' . $this->company['id'] . date("_Ymd_") . rand(100,999);
            $info = $file->move(ROOT_PATH . 'public' . DS . $path, $file_name);
            if(empty($info)){
                throw new \Exception(__LINE__);
            }
            $file_name = $info->getSaveName();
            $file_link = $path . '/' . $file_name;
            try{
                $file_tmp_path = ROOT_PATH . 'public/' .$file_link;
                \FileUpload::upload($file_tmp_path, $file_link);
                return $file_link;
            }catch (OssException $e) {
                return  false;
            }
        }catch(\Exception $e){
           return  false;
        }

    }
}


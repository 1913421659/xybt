<?php
namespace app\api\controller;
use app\api\vo\VOUserBase;
use app\common\user\MUser;
use anywhere\FW;
use app\common\VORsAjax;
use app\common\user\MUserRank;
use anywhere\VOParams;

/**
 * 用户相关接口
 * @author 谭武云
 *
 */
class User extends Api{
	
	public function __construct(){
		$mod_user = MUser::getInstance();
		$mod_user->flushLogined();
		$this->user = $mod_user->getLogined();
		parent::__construct();
	}
	
	protected $user = null;
	
	/**
	 * 获取用户等级信息
	 * @author 谭武云
	 * @date 2017年9月13日
	 */
	public function rankList(){
		$rs = VORsAjax::getInstance();
		try{
			$params = VOParams::getInstance();
			$rs->data = MUserRank::getInstance()->getList($params);
			$rs->status = 1;
		}catch (\Exception $e){
			$rs->errByException($e);
		}
		$rs->outputJSON();
	}
	
	/**
	 * 达人升级条件
	 * @author 谭武云
	 * @date 2017年9月13日
	 */
	public function rankUpgradeList(){
		$this->param()->sign();
		$param = request()->post();
		$list = db('user_rank_upgrade')->order('id')->select();
		$this->back(1, $list);
	}

	/**
	 * 获取用户基础信息
	 * @date 2018-02-05
	 * 
	 * @author darkcloud
	 */
	public function baseInfo(){
		$rs = VORsAjax::getInstance();
		try{
			if(!$this->user){
				throw new \Exception('请先登录', 1001);
			}
			$vo = new VOUserBase();
			$vo->loadFromDBArray($this->user->toDBArray());
			if($this->user->nick_name != ''){
				$vo->user_name = $this->user->nick_name;
			}elseif($this->user->mobile_bind){
				$vo->user_name = $this->user->mobile_phone;
			}else{
				$vo->user_name = $this->user->user_name;
			}
// 			$mod->checkCurrencyTotal($vo);
			$rs->data = $vo->toArray();
			$rs->status = 1;
		}catch (\Exception $e){
			$rs->errByException($e);
		}
		$rs->outputJSON();
	}

	public function bindAlipay(){
		$rs = VORsAjax::getInstance();
		try{
			$mod_user = MUser::getInstance();
			$user = $mod_user->getLogined();
			if($user->alipay_bind == 1){
				throw new \Exception('您已经绑定过支付宝');
			}
			$data = [
				'alipay_bind' => 1,
				'alipay_name' => FW::_POST('v')
			];
			db($mod_user->table)
			->where('user_id', $user->user_id)
			->update($data);
			$user = $mod_user->getOneByPrimaryKey($user->user_id);
			$mod_user->setLogined($user);
			$rs->status = 1;
		}catch (\Exception $e){
			$rs->errByException($e);
		}
		$rs->outputJSON();
	}
	
	public function withdraw(){
		$rs = VORsAjax::getInstance();
		try{
			db()->startTrans();
			$list_method_name = ['钱包','支付宝'];
			$method = intval(FW::_POST('method', 0));
			$money = intval(FW::_POST('money'));
			if(!isset($list_method_name[$method])){
				throw new \Exception('不支持的提现方式');
			}
			if($money <=0){
				throw new \Exception('提现金额有误');
			}
			$this->user = MUser::getInstance()->flushLogined();
			if(!$this->user){
				throw new \Exception('登录信息错误，请重新登录');
			}
			if($money > $this->user->yi_currency_total/100){
				throw new \Exception('可提现金额不足');
			}
			//判断是否第一次提现
			$times = db('user_account')
			->where('user_id', $this->user->user_id)
			->where('process_type',1)
			->value('count(*)');
			if($times > 0 && $money <5){
				throw new \Exception('非首次提现，金额不得低于5元');
			}
			db('user_account')->insert([
				'user_id'=>$this->user->user_id,
				'amount'=>0-$money,
				'add_time'=>time(),
				'process_type' => 1,
				'payment'=>$list_method_name[$method],
				'status'=>0,
				'admin_note'=>'',
				'user_note'=>'',
				'admin_user'=>'',
			]);
			db()->commit();
			if($times){//以前提现次数>0
				$rs->msg = '您的提现已记录，将于48小时内审核。';
			}else{
				$rs->msg = '提现成功，请关注账户余额';
			}
			$rs->status = 1;
		}catch (\Exception $e){
			throw $e;
			db()->rollback();
			$rs->errByException($e);
		}
		$rs->outputJSON();
	}
	
	
	
}
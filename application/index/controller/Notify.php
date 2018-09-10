<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/25
 * Time: 17:15
 */

namespace app\index\controller;


use think\Controller;

class Notify extends Controller{

    //支付宝回调入口
    public function recharge_notify(){
        $result = \alipay\Notify::check($_POST);
        if(!$result){
            echo 'fail';die;
        }
        $out_trade_no = $_POST['out_trade_no'];
        //支付宝交易号
        $trade_no = $_POST['trade_no'];
        //交易状态
        $trade_status = $_POST['trade_status'];
        $orderInfo = db('user_bill_record')->where(['ordersn'=>$out_trade_no])->find();
        if(empty($orderInfo) || empty($orderInfo)){
            echo 'fail';die;
        }
        if($trade_status=='TRADE_FINISHED'){

        }else if($trade_status=='TRADE_SUCCESS'){
            if(isset($orderInfo['status']) && $orderInfo['status']==0){
                $res = $this->recharge_order_update($orderInfo,$trade_no,$out_trade_no);
                echo $res;die;
            }else{
                echo 'success';
            }
        }else{
            echo 'fail';die;
        }
    }

    //支付回调处理订单
    private function recharge_order_update($orderInfo,$trade_no,$out_trade_no){
        db()->startTrans();
        try{
            if($orderInfo['company_id']==0){
                $r = db('users')->where(['user_id'=>$orderInfo['user_id']])->setInc('user_company',$orderInfo['money']);
            }else{
                $r = db('user_company')->where(['id'=>$orderInfo['company_id']])->setInc('company_money',$orderInfo['money']);
            }
            if($r){
                $res = db('user_bill_record')->where(['ordersn'=>$out_trade_no,'status'=>0])->update(['status'=>1,'order_serial_number'=>$trade_no,'finish_time'=>time()]);
                db()->commit();
                return $res?'success':'fail';
            }else {
                db()->rollback();
                return 'fail';
            }
        }catch (Exception $e){
            db()->rollback();
            return 'fail';
        }
    }
} 
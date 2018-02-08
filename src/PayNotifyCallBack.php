<?php
/**
 * Created by PhpStorm.
 * User: W
 * Date: 2017/12/2
 * Time: 19:51
 */

namespace wxpay;

use wxpay\database\WxPayOrderQuery;

class PayNotifyCallBack extends WxPayNotify {

    //查询订单
    public function QueryOrder($transaction_id) {
        $input = new WxPayOrderQuery();
        $input->SetTransaction_id($transaction_id);
        $result = WxPayApi::orderQuery($input);
        ##Log::DEBUG("query:" . json_encode($result));
        ##file_put_contents( 'QueryOrder_' . time() . '.txt', "query:" . json_encode($result) );
        if(array_key_exists("return_code", $result)
            && array_key_exists("result_code", $result)
            && $result["return_code"] == "SUCCESS"
            && $result["result_code"] == "SUCCESS")
        {
            return true;
        }
        return false;
    }

    //重写回调处理函数
    public function NotifyProcess($data, &$msg) {
        ##\\Log::DEBUG("call back:" . json_encode($data));
        ##file_put_contents( 'NotifyProcess_' . time() . '.txt', "call back:" . json_encode($data) );
        $notfiyOutput = array();

        if(!array_key_exists("transaction_id", $data)){
            $msg = "输入参数不正确";
            return false;
        }
        //查询订单，判断订单真实性
        if(!$this->QueryOrder($data["transaction_id"])){
            $msg = "订单查询失败";
            return false;
        }
        return true;
    }

}

<?php
/**
 * 文件名简单介绍
 *
 * Author        范文刚
 * Email         464884785@qq.com
 * Date          2019/11/15
 * Time          上午10:31
 * Version       1.0 版本号
 */

namespace join\card;


use join\utils\HttpHelper;
use think\facade\Env;
use think\facade\Log;

/**
 * 模板消息
 */
class TemplateMessage
{
    /**
     * 发送服务到期通知消息
     * @param $member_id
     * @param $merchant_appid
     * @param $merchant_name
     * @param $order
     * @return array
     */
    public static function SendServiceNotify($member_id,$merchant_appid,$merchant_name,$order){

        //{{first.DATA}}
        //业务号码：{{keyword1.DATA}}
        //业务类型：{{keyword2.DATA}}
        //到期时间：{{keyword3.DATA}}
        //{{remark.DATA}}

        $data = [
            "wxtemplate" => 'OPENTM408237933',
            "CardMemberID" => $member_id,
            "appid"=>$merchant_appid,
            "body"=>json_encode([
                'first'=>['value'=>'距离您上次享受服务已过去很久'],
                'keyword1'=>['value'=>$order['id']],
                'keyword2'=>['value'=>$order['service_name']],
                'keyword3'=>['value'=>$order['send_time']],
                'remark'=>['value'=>$order['content']],
            ])
        ];

        $url = Env::get('join_card.api_url') . '/api/WxTemplate/Send';
        $res = HttpHelper::post_json($url, $data);
        return $res;
    }


    /**
     * 发送用户待支付通知消息
     * @param $member_id
     * @param $merchant_appid
     * @param $merchant_name
     * @param $pay_url
     * @param $order
     * @return array
     */

    public static function SendPayNotify($member_id,$merchant_appid,$merchant_name,$pay_url,$order){

//        {{first.DATA}}
//        成交信息：{{keyword1.DATA}}
//        费用类型：{{keyword2.DATA}}
//        待付金额：{{keyword3.DATA}}
//        {{remark.DATA}}

        $data = [
            "wxtemplate" => 'OPENTM406963151',
            "CardMemberID" => $member_id,
            "appid"=>$merchant_appid,
            "url"=>$pay_url,
            "body"=>json_encode([
                'first'=>['value'=>'您有待支付订单，请点击完成支付'],
                'keyword1'=>['value'=>"商户：".$merchant_name],
                'keyword2'=>['value'=>'服务及商品费用'],
                'keyword3'=>['value'=>"¥ ".$order['total_amount'],'color'=>'#ee0a24'],
                'remark'=>['value'=>'点击完成支付'],
            ])
        ];

        $url = Env::get('join_card.api_url') . '/api/WxTemplate/Send';
        $res = HttpHelper::post_json($url, $data);
        return $res;
    }


    /**
     * 发送商户新订单通知
     * @param $merchant_appid
     * @param $member_name
     * @param $order_code
     * @param $amount
     * @param $time
     * @param $goods_name
     * @return array
     */
    public static function SendNewOrderNotify($merchant_appid,$member_name,$order_code,$amount,$time,$goods_name){

        //{{first.DATA}}
        //订单商品：{{keyword1.DATA}}
        //订单编号：{{keyword2.DATA}}
        //订单金额：{{keyword3.DATA}}
        //支付时间：{{keyword4.DATA}}
        //{{remark.DATA}}

        $data = [
            "wxtemplate" => 'OPENTM410086703',
            "appid"=>$merchant_appid,
            "body"=>json_encode([
                'first'=>['value'=>'客户：'.$member_name.' 已下单，请尽快处理'],
                'keyword1'=>['value'=>$goods_name],
                'keyword2'=>['value'=>$order_code],
                'keyword3'=>['value'=>$amount],
                'keyword4'=>['value'=>$time],
                'remark'=>['value'=>'请尽快处理'],
            ])
        ];

        $url = Env::get('join_card.api_url') . '/api/WxTemplate/MerchantSend';
        $res = HttpHelper::post_json($url, $data);
        return $res;
    }


}
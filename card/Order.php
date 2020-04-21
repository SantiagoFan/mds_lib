<?php
/**
 * 文件名简单介绍
 *
 * Author        范文刚
 * Email         464884785@qq.com
 * Date          2019/11/15
 * Time          上午8:56
 * Version       1.0 版本号
 */

namespace join\card;

use join\utils\HttpHelper;
use think\facade\Env;

/**
 * 订单相关
 */
class Order
{
    public static function   Send_PrepayOrder($order,$MerchantAppid){

        $url = Env::get('join_card.api_url') . '/api/PrepayOrder/AddOrder';

        $url_web = Env::get('join_card.api_web_url');
        //创建总台支付单

        $params=[
            "OrderNum"=>$order["id"],
            "Amount"=>$order["total_amount"],
            "NotifyLink"=>'http://mei.card.nmgjoin.com/admin/order/index',// PC结算成功后跳转链接
            "NotifyMobileLink"=>'http://mei.card.nmgjoin.com/wap/order/index',// 移动版结算成功后跳转链接
            "DetailUrl"=>"",//PC 订单详情
            "NotifyUrl"=>'http://mei.card.nmgjoin.com/api/Notify/index',//结算成功后通知成功url//返回{code:"0"},
            "MShopID"=>$order["store_id"],
            "Memo"=>'服务订单',
            "appid"=>$MerchantAppid,
            "Type"=>1
        ];
        $res = HttpHelper::post_json($url,$params);
        return $res;
    }

}
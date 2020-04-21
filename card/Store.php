<?php
/**
 * 会员卡  门店信息
 *
 * Author        范文刚
 * Email         464884785@qq.com
 * Date          2019/11/13
 * Time          下午3:55
 * Version       1.0 版本号
 */

namespace join\card;

use join\utils\HttpHelper;
use think\facade\Env;

class Store
{
    /**
     * @param $merchant_id
     * @param $pageIndex
     * @param $pageSize
     * @param string $search 昵称/姓名/电话
     * @return array
     */
    public static function getList($merchant_id)
    {
        $url = Env::get('join_card.api_url') . '/api/Shop?MerchantID='.$merchant_id;
        $Merchant = json_decode(HttpHelper::get($url),true);

        return $Merchant;
    }

}

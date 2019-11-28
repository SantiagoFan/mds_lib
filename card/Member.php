<?php
/**
 * 会员卡 会员信息查询
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

class Member
{
    /**
     * 获取多个会员信息
     * @param $merchant_id
     * @param $pageIndex
     * @param $pageSize
     * @param string $search 昵称/姓名/电话
     * @return array
     */
    public static function getList($merchant_id, $pageIndex, $pageSize, $search = '')
    {
        $url = Env::get('join_card.api_url') . '/api/MemberInfo/GetList';

        $data = [
            "MerchantID" => $merchant_id,
            "pageIndex" => $pageIndex,
            "pageSize" => $pageSize,
            "search" => $search
        ];
        $res = HttpHelper::post_json($url, $data);
        return $res;
    }


    /**
     * 获取单个会员信息
     * @param $member_id
     * @return array
     */
    public static function get($member_id){
        $url = Env::get('join_card.api_url') . '/api/MemberInfo/GetDetail';
        $data = [
            "MemberID" => $member_id,
        ];
        $res = HttpHelper::post_json($url, $data);
        return $res;
    }
}
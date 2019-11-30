<?php
/**
 * 用户登录授权相关
 *
 * Author        范文刚
 * Email         464884785@qq.com
 * Date          2019/11/28
 * Time          上午8:38
 * Version       1.0 版本号
 */

namespace join\card;


use join\utils\ErrorCode;
use join\utils\JWT;
use think\exception\HttpResponseException;
use think\facade\Env;
use think\facade\Request;


class Auth
{
    //jwt token_id  不同id 区分不同区域jwt
    private static $merchant_token_id = 'be0748ec77bd';
    private static $factory_token_id = 'factory';
    private static $member_token_id = 'lingjiuser';


    /**
     * 从JWT 反解商户信息并返回
     * 不存在则跳转登录
     *
     * MerchantID
     * MerchantName
     * MShopID
     * MShopName
     * MAccountRole
     * MerchantAppid
     */
    public static function GetMerchant_From_JWT(){

        $request_url = \request()->Url(true);//原始请求URL
        $login_url = Env::get('join_card.api_url')."/Login?return_url=" . urlencode($request_url);

        //从 cookie 或者url 中获取 token
        $token = cookie("jwt");

        if (empty($token)) {
            //ajax
            if (Request::isAjax()) {
                $response = json(['code' => 50014, 'message' => '未登录请登录后重试']);
                throw new HttpResponseException($response);// 参考tp 框架内部处理redirect 和error的思路直接输出结果
            } else {
                throw new HttpResponseException(redirect($login_url));
            }
        } else {
            $is_success = JWT::validateToken($token, self::$merchant_token_id);

            if ($is_success["code"] != 0) { //校验不成功

                if (Request::isAjax()) {
                    $response = json(['code' => 50012, 'message' => '登录超时请重新登录']);
                    throw new HttpResponseException($response);// 参考tp 框架内部处理redirect 和error的思路直接输出结果
                } else {
                    throw new HttpResponseException(redirect($login_url));
                }

            } else {//添加用戶状态信息
                return JWT::getAdminData($token);
            }
        }
    }

    /**
     * 从JWT 反解用户信息并返回
     * 不存在则跳转登录
     * ID
     * openid
     * MerchantID
     * MerchantName
     * MerchantAppid
     * Name
     * nickname
     * headimgurl
     */
    public static function GetMember_From_JWT(){
        $appid =input('get.gfappid');
        $request_url = \request()->Url(true);//原始请求URL
        $login_url = Env::get('join_card.api_web_url')."/SubSystemLogin/?gfappid=".$appid."&return_url=".urlencode($request_url);

        //从 cookie 或者url 中获取 token
        $token = cookie("jwtuser");

        if (empty($token)) {
            //ajax
            if (Request::isAjax()) {
                $response = json(['code' => 50014, 'message' => '未登录请登录后重试']);
                throw new HttpResponseException($response);// 参考tp 框架内部处理redirect 和error的思路直接输出结果
            } else {
                throw new HttpResponseException(redirect($login_url));
            }
        } else {
            $is_success = JWT::validateToken($token, self::$member_token_id);

            if ($is_success["code"] != 0) { //校验不成功

                if (Request::isAjax()) {
                    $response = json(['code' => 50012, 'message' => '登录超时请重新登录']);
                    throw new HttpResponseException($response);// 参考tp 框架内部处理redirect 和error的思路直接输出结果
                } else {
                    throw new HttpResponseException(redirect($login_url));
                }

            } else {//添加用戶状态信息
                $member = JWT::GetUserData($token);
                 //判断当前商户是否为用户登录商户
                if(!empty($appid)&&$member["MerchantAppid"]!=$appid){
                     throw new HttpResponseException(redirect($login_url));
                }
                return $member;
            }
        }

    }

     /**
     * 从JWT 反解工厂信息并返回
     * 不存在则跳转登录
     */
    public static function GetFactory_From_JWT(){

        $request_url = \request()->Url(true);//原始请求URL

        $login_url = "/factory/Login/index?return_url=" . urlencode($request_url);

        //从 cookie 或者url 中获取 token
        $token = cookie("factory_jwt");

        if (empty($token)) {
            //ajax
            if (Request::isAjax()) {
                $response = json(['code' => ErrorCode::login_error, 'message' => '未登录请登录后重试']);
                throw new HttpResponseException($response);// 参考tp 框架内部处理redirect 和error的思路直接输出结果
            } else {
                 throw new HttpResponseException(redirect($login_url));
            }
        } else {
            $is_success = JWT::validateToken($token, self::$factory_token_id);

            if ($is_success["code"] != 0) { //校验不成功

                if (Request::isAjax()) {
                    $response = json(['code' => ErrorCode::login_timeout, 'message' => '登录超时请重新登录']);
                    throw new HttpResponseException($response);// 参考tp 框架内部处理redirect 和error的思路直接输出结果
                } else {
                     throw new HttpResponseException(redirect($login_url));
                }
            } else {//添加用戶状态信息
                return JWT::getFactoryData($token);
            }
        }


    }
}
<?php
namespace join\wx;

use EasyWeChat\Factory;
use think\exception\HttpResponseException;
use think\facade\Config;
use think\facade\Cookie;

/**
 * 微信公众号 相关封装
 */
class WxOfficialAccount
{
    // 微信授权信息的cookie 名称
    private  static $WECHAT_COOKIE_NAME = 'wechat_user';
    // +----------------------------------------------------------------------
    // | 公众号配置
    // +----------------------------------------------------------------------
    public static function getConfig(){
        return Config::get('wx_official');
    }

    /***
     * 获得页面wchat.js 的配置参数
     */
    public static function getJDKConfig(){
        $config=self::getConfig();
        $config['response_type'] ='array';
        $app = Factory::officialAccount($config);
        $APIs =['updateAppMessageShareData','updateTimelineShareData','getLocation','openLocation','scanQRCode'];
        $jsconfig = $app->jssdk->buildConfig($APIs, false);
        return $jsconfig;
    }

    /**
     * 拉取微信用户信息
     * @param null $config 不传值则使用 config 文件中的配置
     * @param string $scopes 授权方式 $scopes: snsapi_userinfo(默认,显示授权)/snsapi_base(静默)
     * @return 用户
     */
    public static function getUserInfo($config=null,$scopes='snsapi_userinfo'){
        // 当前页面地址
        $target_url ='http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        if($config==null){
            $config = self::getConfig();
        }
        $config['oauth']=[
            'scopes'   => [$scopes],
            'callback' => $target_url,
        ];
        $app = Factory::officialAccount($config);
        $user = Cookie::get(self::$WECHAT_COOKIE_NAME);
        // 未登录
        if (empty($user)) {
            try {
                if(input('code')==null){
                    return $app->oauth->redirect()->send();
                }
                else{
                    // 获取 OAuth 授权结果用户信息
                    $user = $app->oauth->user();
                    // 已经回调 重新设置session
                    Cookie::set(self::$WECHAT_COOKIE_NAME,$user->toArray());
                }
            } catch (\Throwable $th) {//防止页面刷新code被重复使用报错  重现方式:url code有 cookie 失效
                 $response = json(['code' => 50050, 'message' => 'WX错误:'.$th->getMessage()]);
                 throw new HttpResponseException($response);
            }
        }
        // 已经登录过
        return $user;
    }

}

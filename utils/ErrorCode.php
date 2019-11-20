<?php
/**
 * 统一错误代码枚举
 *
 * Author        范文刚
 * Email         464884785@qq.com
 * Date          2019/11/20
 * Time          上午11:55
 * Version       1.0 版本号
 *  系统级别错误
 *  服务级别错误
 *
 */

namespace join\utils;


class ErrorCode
{
    const success = 20000;
    const error = 50000;
    /**
     * 登录失效
     */
    const login_error = 50014;
    /**
     * 登录过期
     */
    const  login_timeout = 50012;


}
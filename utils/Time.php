<?php
/**
 * 文件名简单介绍
 *
 * Author        范文刚
 * Email         464884785@qq.com
 * Date          2020/3/31
 * Time          上午10:51
 * Version       1.0 版本号
 */

namespace join\utils;


class Time
{
    /**
     * 当前时间
     * @param string $format
     * @return false|string
     */
    public static function now($format='Y-m-d H:i:s'){
        return date($format);
    }
    
    /**
     * 当前时间加几天
     * @param $number
     * @param string $format  格式标准
     * @return false|string
     */
    public static function addDay($number,$format='Y-m-d H:i:s'){
        return date($format,strtotime("+".$number." day"));
    }
}

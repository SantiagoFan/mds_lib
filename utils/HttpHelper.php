<?php
/**
 * Created by PhpStorm.
 * User: Administrator-范文刚
 * Date: 2019/3/30
 * Time: 15:11
 */

namespace join\utils;

/**
 * http 请求帮助工具类
 * Class HttpHelper
 * @package app\common
 */
class HttpHelper
{
    /**
     * 发送get请求
     * @param $url
     * @return bool|string
     */
    public static function  get($url){
        $info = curl_init();
        curl_setopt($info,CURLOPT_URL,$url);
        curl_setopt($info,CURLOPT_HEADER,0);
        curl_setopt($info,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($info,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($info,CURLOPT_SSL_VERIFYHOST,false);
        curl_setopt($info,CURLOPT_CONNECTTIMEOUT,10);

        curl_setopt($info,CURLOPT_NOBODY,0);
        $output = curl_exec($info);
        curl_close($info);
        return $output;
    }

    /**
     * 发送post请求
     * @param string $url 请求地址
     * @param array $post_data post键值对数据
     * @return string
     */
    public static function post($url, $post_data) {
        $postdata = http_build_query($post_data);
        $options = array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-type:application/x-www-form-urlencoded',
                'content' => $postdata,
                'timeout' => 15 * 60 // 超时时间（单位:s）
            )
        );
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        return $result;
    }

    /**
     * 发送post请求 返回数组对象
     * @param string $url 请求地址
     * @param string $post_data 键值对数据
     * @return array
     */
    public static function post_json($url, $post_data) {
        $postdata = json_encode($post_data);
        $options = array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-type: application/json',
                'content' => $postdata,
                'timeout' => 15 * 60 // 超时时间（单位:s）
            )
        );
        $context = stream_context_create($options);

        $result = file_get_contents($url, false, $context);

        return json_decode($result,true);
    }

    public static function http_curl($url,$type='get',$res='json',$arr=''){
        //1.初始化curl
        $ch = curl_init();
        //2.设置curl的参数
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if($type == 'post'){
            curl_setopt($ch, CURLOPT_POST,1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $arr);
        }
        //3.采集
        $output = curl_exec($ch);
        //4.关闭
        curl_close($ch);
        if($res=='json'){
            if(curl_error($ch)){
                //请求失败，返回错误信息
                return curl_error($ch);
            }else{
                //请求成功，返回信息
                return json_decode($output,true);
            }
        }
    }

}
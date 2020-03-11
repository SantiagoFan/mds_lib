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

    /**
     * @param $url  访问的URL
     * @param string $post post数据(不填则为GET)
     * @param string $cookie 提交的$cookies
     * @param int $returnCookie 是否返回$cookies
     * @return bool|string
     */
     public  function curl_request($url,$post='',$cookie='', $returnCookie=0){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)');
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
        curl_setopt($curl, CURLOPT_REFERER, "http://XXX");
        if($post) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post));
        }
        if($cookie) {
            curl_setopt($curl, CURLOPT_COOKIE, $cookie);
        }
        curl_setopt($curl, CURLOPT_HEADER, $returnCookie);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($curl);
        if (curl_errno($curl)) {
            return curl_error($curl);
        }
        curl_close($curl);
        if($returnCookie){
            list($header, $body) = explode("\r\n\r\n", $data, 2);
            preg_match_all("/Set\-Cookie:([^;]*);/", $header, $matches);

            $info['cookie']  = [];
            foreach ($matches[1] as $k=>$v){
                $ls = explode("=",$v);
                $info['cookie'][trim($ls[0])] =$ls[1];
            }
            $info['content'] = json_decode($body,true);
            return $info;
        }else{
            return $data;
        }
    }


}

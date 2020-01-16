<?php


/**
 * 文件名简单介绍
 *
 * Author        范文刚
 * Email         464884785@qq.com
 * Date          2020/1/4
 * Time          上午8:45
 * Version       1.0 版本号
 */
namespace join\ai;

use think\facade\Env;
use AipOcr;

class baiduAI
{
    /**
     * 营业执照识别
     * @param $config
     * @param $file_path
     * @return array
     */
    public static function ocr_businessLicense($config,$file_path){

        require_once Env::get('vendor_path').'baidu/aip-sdk/AipOcr.php';

        $client = new AipOcr($config['APP_ID'],$config['API_KEY'],$config['SECRET_KEY']);

        $image = file_get_contents($file_path);


        $res = $client->businessLicense($image);
        $data=[];

        if($res['words_result']['单位名称']['words']!='无'){
            $data['full_name']=$res['words_result']['单位名称']['words'];
        }
        if($res['words_result']['法人']['words']!='无'){
            $data['principal_name']=$res['words_result']['法人']['words'];
        }
        if($res['words_result']['地址']['words']!='无'){
            $data['address']=$res['words_result']['地址']['words'];
        }
        return $data;
    }


    public static function ocr_bankcard($config,$file_path){

        require_once Env::get('vendor_path').'baidu/aip-sdk/AipOcr.php';

        $client = new AipOcr($config['APP_ID'],$config['API_KEY'],$config['SECRET_KEY']);

        $image = file_get_contents($file_path);


        $res = $client->bankcard($image);
        $data=[];

        if($res['result']['bank_card_number']!='无'){
            $data['bank_card']=$res['result']['bank_card_number'];
        }
        return $data;
    }

}

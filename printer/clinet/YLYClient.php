<?php
/**
 * 文件名简单介绍
 *
 * Author        范文刚
 * Email         464884785@qq.com
 * Date          2020/1/10
 * Time          下午4:58
 * Version       1.0 版本号
 */

namespace  join\printer\client;

use join\printer\PrinterInterface;

//composer require yly-openapi/yly-openapi-sdk:v1.0.1

use App\Config\YlyConfig;
use App\Oauth\YlyOauthClient;
use App\Api\PrinterService;
use App\Api\PrintService;
use App\Api\PicturePrintService;

/**
 * 易连云打印机 客户端
 * Class YLYClient
 * @package join\printer\client
 */
class YLYClient implements PrinterInterface
{
    private $config;
    private $client;
    private $cache_file='yly_print_token_cache.txt';//token 缓存文件

    public function __construct(){
        //驻客应用参数 自用型
        $clientId = '1093484911';
        $clientSecret='9f563acf8da3c12606a5fcab8f0e92e7';
        //初始化配置
        $this->config=new YlyConfig($clientId,$clientSecret);
        $this->client = new YlyOauthClient($this->config);
    }

    /**
     * 获取token 文件缓存
     * 有效期不重新获取
     */
    private function getToken(){
        //文件存在
        if(file_exists($this->cache_file)){
            return json_decode(file_get_contents($this->cache_file));
        }
        // 文件不存在 远程获取
        else{
            $token = $this->client->getToken();
            file_put_contents($this->cache_file,json_encode($token));
            return $token;
        }
    }


    /**
     * 调用打印机测试页
     * @param $machineCode 打印机编号
     * @return mixed
     */
    public function PrintTest($machineCode)
    {
        try{
            //调取文本打印
            $token = $this->getToken();
            $print = new PrintService($token->access_token, $this->config);
            //-----------------------内容区域-----------------------------
            $content = "<FS2><center>**#1 驻客测试**</center></FS2>";
            $content .= "<FS2><center>**#1 完**</center></FS2>";
            //-----------------------内容区域-----------------------------
            $data = $print->index($machineCode,$content,'1');
            return $data;
        }
        catch (\Exception $e){
            return (object)["error"=>500,"error_description"=>$e->getMessage()];
        }
    }

    /**
     * 初始化打印机
     * @param $params
     * @return mixed
     */
    public function initPrint($params)
    {
        // TODO: Implement initPrint() method.
    }

    /**
     * 添加打印机设备
     * @param $params
     * @return mixed
     */
    public function addPrinter($params)
    {
        try{
            $token = $this->getToken();
            $printer = new PrinterService($token->access_token,$this->config);
            $data = $printer->addPrinter($params['machineCode'],$params['machineSign'],$params['name']);
            return $data;
        }
        catch (\Exception $e){
           return (object)["error"=>500,"error_description"=>$e->getMessage()];
        }
    }

    /**
     * 删除打印机设备
     * @param $params
     * @return mixed
     */
    public function delPrinter($params)
    {
        // TODO: Implement delPrinter() method.
    }

    /**
     * 打印内容
     * @param $params
     * @return mixed
     */
    public function Printing($params)
    {
        $token = $this->getToken();
        $print = new PrintService($token->access_token, $this->config);
        return $print->index($params['machineCode'],$params['content'],$params['orginId']);
    }


//            $content = "<MN>".$n."</MN>";//打印份数
//            $content .= "<FH2><FB><center>".$order["Memo"]."</center></FB></FH2>\n";
//            //-----------------------内容区域-----------------------------
//            if(count($pr['goods'])==0)return;
////                $content .= "<FH><FW> --".$c['title']."--</FW></FH>\n";
//            $content .= "<table>";
//            foreach ($pr['goods'] as $gk=>$g){
//
//                if(!in_array($g["goods_category"],$printCategory)){
//                    $content .= "<tr><td>........(".$g["category_title"].")........</td></tr>";
//                    array_push($printCategory,$g["goods_category"]);
//                }
//                $content .= "<tr><td><FS2>".$g["goods_title"]."</FS2></td><td><FS2>x".$g["quantity"]."</FS2></td><td><FS2>".$g["goods_unit"]."</FS2></td></tr>";
//            }
//            $content .= "</table>";
//            //-----------------------内容区域-----------------------------
//            $content .= "\n<FS>用户备注:</FS>";
//            $content .= $order["mark"]." \n";
//            $content .= "时间:".$order["submit_time"]. "\n";
//            $content .= "编号:".$order["order_code"]."";
//


}

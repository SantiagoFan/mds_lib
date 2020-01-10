<?php
/**
 * Created by PhpStorm.
 * User: santiago-范文刚
 * Date: 19-6-20
 * Time: 下午6:00
 */

namespace join\printer;

use join\printer\client\YLYClient;

/**
 * 打印机实例工程类 通过
 * 设备商编码获取打印机操作类实例
 * @package app\common
 */
class PrinterFactory
{
    private static function CreatePrinter($code) {

        switch ($code){
            case 'YLY':return new YLYClient();// 易连云打印
            default: return new YLYClient();
        }
    }

    /**
     * 添加打印机
     * @param $p
     * @return mixed
     */
    public static function AddPrint($p){
        $print =PrinterFactory::CreatePrinter($p['machineCode']);
        return $print->addPrinter($p);
    }
    /**
     * 打印测试页
     */
    public static function PrintTestPage($p){
        $print =PrinterFactory::CreatePrinter($p['machineCode']);
        return $print->PrintTest($p['machineCode']);
    }

    /**
     * 打印内容
     * @param $p
     * @param $content
     * @return object
     */
    public static function Printing($p,$content){

        $print =PrinterFactory::CreatePrinter($p['machineCode']);
        $p['content'] =$content;
        $print->Printing($p);
        return (object)["code"=>0,"message"=>'success'];

    }
}

<?php
/**
 * 文件名简单介绍
 *
 * Author        范文刚
 * Email         464884785@qq.com
 * Date          2020/1/10
 * Time          下午4:49
 * Version       1.0 版本号
 */

namespace join\printer;

/**
 * 打印机接口
 * Interface PrinterInterface
 * @package join\printer
 */
interface PrinterInterface
{
    /**
     * 调用打印机测试页
     * @param $machineCode 打印机编号
     * @return mixed
     */
     public function PrintTest($machineCode);

    /**
     * 初始化打印机
     * @param $params
     * @return mixed
     */
     public function initPrint($params);

     /**
     * 添加打印机设备
     * @param $params
     * @return mixed
     */
     public function addPrinter($params);
      /**
     * 删除打印机设备
     * @param $params
     * @return mixed
     */
     public function delPrinter($params);

    /**
     * 打印内容
     * @param $params
     * @return mixed
     */
     public function Printing($params);
}

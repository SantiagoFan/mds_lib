<?php
/**
 * Created by PhpStorm.
 * User: Administrator-范文刚
 * Date: 2019/3/30
 * Time: 15:11
 */

namespace join\utils;

/**
 * 数组集合 等处理函数
 * Class Collection
 * @package join\utils
 */
class Collection
{
    /**
     * 从数组中查询 key为value 的对象 若果只有一个则返回该对象，否则返回符合条件的数组集合
     * @param $array
     * @param $key
     * @param $value
     * @return array|mixed
     */
    public static function array_filter($array, $key, $value)
    {
        $data = [];
        foreach ($array as $k => $v) {
            if ($v[$key] == $value) {
                array_push($data, $v);
            }
        }
        if (count($data) == 1) {
            return $data[0];
        } else {
            return $data;
        }
    }
}
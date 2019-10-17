<?php
/**
 * Created by PhpStorm.
 * User:
 * Date: 2019/7/19
 * Time: 11:40
 */

namespace app\common\lib;

/**
 * Class Time
 * @package app\common\lib
 */
class Time
{
    /**
     * 获取13位的时间戳
     * @return string
     */
    public static function get13Timestamp()
    {
        list($t1, $t2) = explode(' ', microtime()); // microtime() 函数返回当前 Unix 时间戳的微秒数。
        return $t2 . ceil($t1 * 1000); // ceil() 函数向上舍入为最接近的整数
    }
}
<?php

namespace app\api\controller;

use think\Controller;

/**
 * APP与服务器端时间一致性解决方案
 * 背景：APP时间与服务器时间不一致
 * 解决方案：获取服务端时间，与APP本地时间作对比，补上时间差
 *
 * Class Time
 * @package app\api\controller
 */
class Time extends Controller
{
    /**
     * 获取服务端时间
     * @return \think\response\Json
     */
    public function index()
    {
        return show(config('code.success'), 'OK', time());
    }
}

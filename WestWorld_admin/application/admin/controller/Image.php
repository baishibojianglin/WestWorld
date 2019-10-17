<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;

/**
 * 后台图片上传控制器类
 * Class Image
 * @package app\admin\controller
 */
class Image extends Base
{
    /**
     * 上传图片
     * @return \think\response\Json
     */
    public function upload()
    {
        $fileInfo = upload('file');
        if ($fileInfo) {
            return show(config('code.success'), '上传成功', $fileInfo);
        } else {
            return show(config('code.error'), $fileInfo, [], 404);
        }
    }
}

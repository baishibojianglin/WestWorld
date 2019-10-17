<?php

namespace app\api\controller\v1;

use think\Controller;
use think\Request;

/**
 * api模块客户端图片上传控制器类
 * Class Image
 * @package app\api\controller\v1
 */
class Image extends AuthBase
{
    /**
     * 保存新建的资源（上传图片）
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //print_r($_FILES);
        $fileInfo = upload('file');
        if ($fileInfo) {
            return show(config('code.success'), '上传成功', $fileInfo);
        } else {
            return show(config('code.error'), $fileInfo, [], 404);
        }
    }
}

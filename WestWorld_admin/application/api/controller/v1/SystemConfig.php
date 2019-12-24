<?php
/**
 * Created by PhpStorm.
 * User: Yan
 * Date: 2019/12/23
 * Time: 11:57
 */

namespace app\api\controller\v1;
use app\common\lib\exception\ApiException;

/**
 * api模块客户端系统配置控制器类
 * Class SystemConfig
 * @package app\api\controller\v1
 */
class SystemConfig
{
    /**
     * 显示指定的资源
     *
     * @param  int $id
     * @return \think\Response
     * @throws ApiException
     */
    public function read($id)
    {
        // 判断为GET请求
        if (request()->isGet()) {
            try {
                $data = model('Config')->find($id);
            } catch (\Exception $e) {
                throw new ApiException($e->getMessage(), 500, config('code.error'));
            }

            if ($data) {
                return show(config('code.success'), 'ok', $data);
            } else {
                return show(config('code.error'), 'Not Found', $data, 404);
            }
        }
    }
}
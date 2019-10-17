<?php

namespace app\api\controller\v1;

use app\api\controller\Common;
use app\common\lib\exception\ApiException;
use think\Cache;
use think\Controller;

/**
 * api模块客户端店鋪比赛场次模板控制器类
 * Class SessionTemplate
 * @package app\api\controller\v1
 */
class SessionTemplate extends Common
{
    /**
     * 显示指定的店鋪比赛场次模板资源（读取缓存）
     * @param int $id
     * @return \think\response\Json
     * @throws ApiException
     */
    public function read($id)
    {
        // 判断为GET请求
        if (request()->isGet()) {
            // 判断是否支持redis缓存（如果不支持redis扩展，则直接读取数据库数据）
            if (!extension_loaded('redis')) { // extension_loaded ( string $name ) : bool 检查一个扩展是否已经加载。
                $data = $this->_read($id);
                if ($data) {
                    return show(config('code.success'), 'OK', $data);
                } else {
                    return show(config('code.error'), 'Not Found', [], 404);
                }
            }

            // 定义缓存变量名
            $cacheName = 'session_template_data_' . $id; // 拼接id是为了保证缓存的唯一性

            // 判断缓存是否存在（先判断再取值，如果缓存不存在，读取数据库数据写入缓存）
            if (!Cache::store('redis')->get($cacheName)) {
                // 读取数据
                $data = $this->_read($id);
                if ($data) {
                    // 写入缓存
                    Cache::store('redis')->set($cacheName, $data);
                } else {
                    return show(config('code.error'), 'Not Found', [], 404);
                }
            }

            // 读取缓存
            $data = Cache::store('redis')->get($cacheName);

            return show(config('code.success'), 'OK', $data);
        }
    }

    /**
     * 显示指定的店鋪比赛场次模板资源（读取数据）
     * @param int $id
     * @return mixed
     * @throws ApiException
     */
    private function _read($id)
    {
        // 查询条件
        $map = [];
        $map['st.is_default'] = ['eq', 1];

        // 读取数据
        try {
            $data = model('SessionTemplate')->getSessionTemplateById($map, $id);
        } catch (\Exception $e) {
            throw new ApiException($e->getMessage(), 500, config('code.error'));
        }
        if ($data) {
            // 处理数据
            // 定义status_msg
            $status = config('code.status');
            $data['status_msg'] = $status[$data['status']];
            // 将session_template的json数据转数组
            $data['session_template'] = json_decode($data['session_template']);
        }

        return $data;
    }
}

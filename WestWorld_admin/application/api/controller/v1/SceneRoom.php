<?php

namespace app\api\controller\v1;

use app\api\controller\Common;
use app\common\lib\exception\ApiException;
use think\Controller;

/**
 * api模块客户端场景房间控制器类
 * Class SceneRoom
 * @package app\admin\controller
 */
class SceneRoom extends Common
{
    /**
     * 获取场景房间资源列表
     * @return \think\response\Json
     */
    public function index()
    {
        // 判断为GET请求
        if (request()->isGet()) {
            // 传入的参数
            $param = input('param.');

            // 查询条件
            $map = [];
            $map['sr.status'] = config('code.status_enable'); // 状态
            $map['sr.is_booked'] = 0; // 是否被预订
            if (!empty($param['venue_id'])) { // 场馆ID
                $map['sr.venue_id'] = intval($param['venue_id']);
            }
            if (!empty($param['scene_id'])) { // 场景ID
                $map['sr.scene_id'] = intval($param['scene_id']);
            }

            // 获取分页page、size
            $this->getPageAndSize($param);

            // 获取分页列表数据 模式一：基于paginate()自动化分页
            $data = model('SceneRoom')->getSceneRoom($map, $this->size);
            $status = config('code.status');
            foreach($data as $key => $value){
                $data[$key]['status_msg'] = $status[$value['status']]; // 状态说明
            }

            return show(config('code.success'), 'ok', $data);
        }
    }

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
                $data = model('SceneRoom')->find($id);
            } catch (\Exception $e) {
                throw new ApiException($e->getMessage(), 500, config('code.error'));
            }

            if ($data) {
                // 处理数据
                // 定义status_msg
                $status = config('code.status');
                $data['status_msg'] = $status[$data['status']];

                return show(config('code.success'), 'ok', $data);
            } else {
                return show(config('code.error'), 'Not Found', $data, 404);
            }
        }
    }
}

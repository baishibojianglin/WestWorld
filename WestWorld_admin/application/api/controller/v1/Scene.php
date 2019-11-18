<?php

namespace app\api\controller\v1;

use app\api\controller\Common;
use app\common\lib\exception\ApiException;
use think\Controller;

/**
 * api模块客户端场景控制器类
 * Class Scene
 * @package app\api\controller\v1
 */
class Scene extends Common
{
    /**
     * 获取场景资源列表
     * @return \think\response\Json
     */
    public function index()
    {
        // 判断为GET请求
        if (request()->isGet()) {
            // 传入的参数
            $param = input('param.');
            //$query = http_build_query($param); // 生成 URL-encode 之后的请求字符串 //halt($query);

            // 查询条件
            $map = [];
            $map['s.status'] = config('code.status_enable'); // 状态
            if (!empty($param['venue_id'])) { // 场馆ID
                $map['s.venue_id'] = intval($param['venue_id']);
            }

            // 获取分页page、size
            $this->getPageAndSize($param);

            // 获取分页列表数据 模式一：基于paginate()自动化分页
            $data = model('Scene')->getScene($map, $this->size);
            //$status = config('code.status');
            $issetSessionSceneIds = []; // 定义已设置场次的场景ID集合
            $issetSceneRoomSceneIds = []; // 定义已设置场景房间的场景ID集合
            foreach($data as $key => $value){
                // 处理数据

                //$data[$key]['status_msg'] = $status[$value['status']]; // 状态说明

                // 获取已设置场次的场景ID集合
                $session = model('Session')->where(['scene_id' => $value['scene_id'], 'status' => config('code.status_enable')])->find();
                //if (!$session) {unset($data[$key]);} // 从数组（场景列表$data）中移除（该场景未设置场次的）元素
                if ($session) {
                    $issetSessionSceneIds[] = $value['scene_id'];
                }

                // 获取已设置场景房间的场景ID集合
                $sceneRoom = model('SceneRoom')->where(['scene_id' => $value['scene_id'], 'is_booked' => 0, 'status' => config('code.status_enable')])->find();
                if ($sceneRoom) {
                    $issetSceneRoomSceneIds[] = $value['scene_id'];
                }
            }
            $sceneIds = array_intersect($issetSessionSceneIds, $issetSceneRoomSceneIds); // array_intersect() 函数用于比较两个（或更多个）数组的键值，并返回交集。
            $map['s.scene_id'] = ['in', $sceneIds]; // 处理后的场景ID

            // 获取处理后的数据
            $data = model('Scene')->getScene($map, $this->size);

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
                $data = model('Scene')->find($id);
            } catch (\Exception $e) {
                throw new ApiException($e->getMessage(), 500, config('code.error'));
            }

            if ($data) {
                // 处理数据
                // 定义状态说明status_msg
                $status = config('code.status');
                $data['status_msg'] = $status[$data['status']];

                return show(config('code.success'), 'ok', $data);
            } else {
                return show(config('code.error'), 'Not Found', $data, 404);
            }
        }
    }
}

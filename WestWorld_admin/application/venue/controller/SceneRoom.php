<?php

namespace app\venue\controller;

use app\common\lib\exception\ApiException;
use think\Controller;
use think\Request;

/**
 * venue模块场景房间管理控制器类
 * Class SceneRoom
 * @package app\admin\controller
 */
class SceneRoom extends Base
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        // 判断为GET请求
        if (request()->isGet()) {
            return $this->fetch('', ['sceneList' => Scene::sceneList()]);
        }
    }

    /**
     * 获取场景房间资源列表
     * @return \think\response\Json
     */
    public function getSceneRoom()
    {
        // 判断为GET请求
        if (request()->isGet()) {
            // 传入的参数
            $param = input('param.');
            $query = http_build_query($param); // 生成 URL-encode 之后的请求字符串 //halt($query);

            // 查询条件
            $map = [];
            $map['sr.venue_id'] = $this->session_venue->venue_id; // 场馆ID
            if (!empty($param['scene_id'])) {
                $map['sr.scene_id'] = intval($param['scene_id']);
            }

            // 获取分页page、size
            $this->getPageAndSize($param);

            // 获取分页列表数据 模式一：基于paginate()自动化分页
            $data = model('SceneRoom')->getSceneRoom($map, $this->size);
            $status = config('code.status');
            $is_booked = config('code.is_booked');
            foreach($data as $key => $value){
                $data[$key]['status_msg'] = $status[$value['status']];
                $data[$key]['booked_txt'] = $is_booked[$value['is_booked']];
            }

            return show(config('code.success'), 'ok', $data);
        }
    }

    /**
     * 获取场景房间资源列表（静态方法）
     * @return false|\PDOStatement|string|\think\Collection
     */
    public static function sceneRoomList()
    {
        $map = [
            'venue_id' => self::$static_session_venue->venue_id,
            'status' => config('code.status_enable')
        ];
        $data = db('scene_room')->field('room_id, room_name, scene_id')->where($map)->select();
        return $data;
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        // 判断为GET请求
        if (request()->isGet()) {
            return view('', ['sceneList' => Scene::sceneList()]);
        }
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        // 判断为POST请求
        if(request()->isPost()){
            $data = input('post.');
            $data['venue_id'] = $this->session_venue->venue_id; // 场馆ID

            // 入库操作
            try {
                $id = model('SceneRoom')->add($data, 'room_id');
            } catch (\Exception $e) {
                return show(0, '新增失败 ' . $e->getMessage(), [], 400);
            }
            if ($id) {
                return show(config('code.success'), '新增成功', ['url' => url('SceneRoom/index')], 201);
            } else {
                return show(0, '新增失败', [], 400);
            }
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

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        // 判断为GET请求
        if (request()->isGet()) {
            return view('', ['sceneList' => Scene::sceneList()]);
        }
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request $request
     * @param  int $id
     * @return \think\Response
     * @throws ApiException
     */
    public function update(Request $request, $id)
    {
        // 判断为PUT请求
        if (request()->isPut()) {
            // 传入的参数
            $param = input('param.');

            // 判断数据是否存在
            $data = [];
            if (!empty($param['scene_id'])) { // 场景
                $data['scene_id'] = trim($param['scene_id']);
            }
            if (!empty($param['room_name'])) { // 场景房间名称
                $data['room_name'] = trim($param['room_name']);
            }
            if (isset($param['room_price'])) { // 房间价格
                $data['room_price'] = trim($param['room_price']);
            }
            if (isset($param['available_number'])) { // 可容纳人数
                $data['available_number'] = trim($param['available_number']);
            }
            if (isset($param['is_booked'])) { // 是否被预订
                $data['is_booked'] = input('param.is_booked', null, 'intval');
            }
            if (isset($param['status'])) { // 状态
                $data['status'] = input('param.status', null, 'intval');
            }

            if (empty($data)) {
                return show(config('code.error'), '数据不合法', [], 404);
            }

            // 更新
            try {
                $result = model('SceneRoom')->save($data, ['room_id' => $id]); // 更新
            } catch (\Exception $e) {
                throw new ApiException($e->getMessage(), 500, config('code.error'));
            }
            if (false === $result) {
                return show(config('code.error'), '更新失败', [], 403);
            } else {
                return show(config('code.success'), '更新成功', ['url' => 'parent'], 201);
            }
        }
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }
}

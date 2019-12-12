<?php

namespace app\venue\controller;

use app\common\lib\exception\ApiException;
use think\Controller;
use think\Request;

/**
 * venue模块装备管理控制器类
 * Class Equipment
 * @package app\venue\controller
 */
class Equipment extends Base
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
     * 获取装备资源列表
     * @return \think\response\Json
     */
    public function getEquipment()
    {
        // 判断为GET请求
        if (request()->isGet()) {
            // 传入的参数
            $param = input('param.');
            $query = http_build_query($param); // 生成 URL-encode 之后的请求字符串 //halt($query);

            // 查询条件
            $map = [];
            $map['e.venue_id'] = $this->session_venue->venue_id; // 场馆ID
            if (!empty($param['equipment_name'])) {
                $map['e.equipment_name'] = ['like', '%' . trim($param['equipment_name']) . '%'];
            }
            if (!empty($param['scene_id'])) { // 场景ID
                $map['e.scene_id'] = intval($param['scene_id']);
            }
            if (isset($param['equipment_type']) && ($param['equipment_type'] != null)) { // 装备类型
                $map['e.equipment_type'] = intval($param['equipment_type']);
            }

            // 获取分页page、size
            $this->getPageAndSize($param);

            // 获取分页列表数据 模式一：基于paginate()自动化分页
            $data = model('Equipment')->getEquipment($map, $this->size);

            return show(config('code.success'), 'ok', $data);
        }
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

            // 根据装备类型赋值装备使用费
            if (isset($data['equipment_type']) && ($data['equipment_type'] == 0)) { // 基本装备
                $data['use_fee'] = 0;
            } elseif (isset($data['equipment_type']) && ($data['equipment_type'] == 1) && $data['use_fee'] == 0) { // 付费装备
                return show(config('code.error'), '请输入装备使用费', [], 403);
            }

            // 入库操作
            try {
                $id = model('Equipment')->add($data, 'equipment_id');
            } catch (\Exception $e) {
                return show(config('code.error'), '新增失败 ' . $e->getMessage(), [], 400);
            }
            if ($id) {
                return show(config('code.success'), '新增成功', ['url' => url('Equipment/index')], 201);
            } else {
                return show(config('code.error'), '新增失败', [], 400);
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
                $data = model('Equipment')->find($id);
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
                $data['scene_id'] = intval($param['scene_id']);
            }
            if (isset($param['equipment_type']) && ($param['equipment_type'] != null)) { // 装备类型
                $data['equipment_type'] = intval($param['equipment_type']);
            }
            if (!empty($param['equipment_name'])) { // 装备名称
                $data['equipment_name'] = trim($param['equipment_name']);
            }
            if (!empty($param['thumb'])) { // 装备缩略图
                $data['thumb'] = trim($param['thumb']);

                // 获取更新成功前的缩略图thumb
                $equipment = model('Equipment')->field('thumb')->find($id);
            }
            if (isset($param['use_fee'])) { // 装备使用费
                $data['use_fee'] = trim($param['use_fee']);

                // 根据装备类型赋值装备使用费
                if (isset($param['equipment_type']) && ($param['equipment_type'] == 0)) { // 基本装备
                    $data['use_fee'] = 0;
                } elseif (isset($data['equipment_type']) && ($data['equipment_type'] == 1) && $data['use_fee'] == 0) { // 付费装备
                    return show(config('code.error'), '请输入装备使用费', [], 403);
                }
            }
            if (isset($param['use_number'])) { // 装备使用数量
                $data['use_number'] = trim($param['use_number']);
            }
            if (isset($param['equipment_number'])) { // 装备数量
                $data['equipment_number'] = trim($param['equipment_number']);
            }
            if (!empty($param['equipment_description'])) { // 装备描述
                $data['equipment_description'] = $param['equipment_description'];
            }

            if (empty($data)) {
                return show(config('code.error'), '数据不合法', [], 404);
            }

            // 更新
            try {
                $result = model('Equipment')->save($data, ['equipment_id' => $id]); // 更新
            } catch (\Exception $e) {
                throw new ApiException($e->getMessage(), 500, config('code.error'));
            }
            if (false === $result) {
                return show(config('code.error'), '更新失败', [], 403);
            } else {
                // 删除更新成功前的缩略图thumb文件
                if (!empty($param['thumb']) && trim($param['thumb']) != $equipment['thumb']) {
                    // 删除文件
                    @unlink(ROOT_PATH . 'public' . DS . $equipment['thumb']);
                }

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

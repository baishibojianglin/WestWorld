<?php

namespace app\venue\controller;

use app\common\lib\exception\ApiException;
use think\Controller;
use think\Request;

/**
 * venue模块场景管理控制器类
 * Class Scene
 * @package app\admin\controller
 */
class Scene extends Base
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
            return $this->fetch();
        }
    }

    /**
     * 获取场景资源列表
     * @return \think\response\Json
     */
    public function getScene()
    {
        // 判断为GET请求
        if (request()->isGet()) {
            // 传入的参数
            $param = input('param.');
            $query = http_build_query($param); // 生成 URL-encode 之后的请求字符串 //halt($query);

            // 查询条件
            $map = [];
            $map['s.venue_id'] = $this->session_venue->venue_id; // 场馆ID
            if (!empty($param['scene_name'])) {
                $map['s.scene_name'] = ['like', '%' . trim($param['scene_name']) . '%'];
            }

            // 获取分页page、size
            $this->getPageAndSize($param);

            // 获取分页列表数据 模式一：基于paginate()自动化分页
            $data = model('Scene')->getScene($map, $this->size);
            $status = config('code.status');
            foreach($data as $key => $value){
                $data[$key]['status_msg'] = $status[$value['status']];
            }

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
            return view();
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
                $id = model('Scene')->add($data, 'scene_id');
            } catch (\Exception $e) {
                return show(0, '新增失败 ' . $e->getMessage(), [], 400);
            }
            if ($id) {
                return show(config('code.success'), '新增成功', ['url' => url('Scene/index')], 201);
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
                $data = model('Scene')->find($id);
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
            return view();
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
            if (!empty($param['scene_name'])) { // 场景名称
                $data['scene_name'] = trim($param['scene_name']);
            }
            if (!empty($param['thumb'])) { // 场景缩略图
                $data['thumb'] = trim($param['thumb']);
            }
            if (!empty($param['scene_description'])) { // 场景介绍
                $data['scene_description'] = $param['scene_description'];
            }
            if (!empty($param['game_rules'])) { // 游戏规则
                $data['game_rules'] = $param['game_rules'];
            }
            if (isset($param['status'])) { // 状态
                $data['status'] = input('param.status', null, 'intval');
            }

            if (empty($data)) {
                return show(config('code.error'), '数据不合法', [], 404);
            }

            // 更新
            try {
                $result = model('Scene')->save($data, ['scene_id' => $id]); // 更新
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

<?php

namespace app\venue\controller;

use app\common\lib\exception\ApiException;
use think\Controller;
use think\Request;

/**
 * venue模块场次管理控制器类
 * Class Session
 * @package app\admin\controller
 */
class Session extends Base
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
     * 获取场次资源列表
     * @return \think\response\Json
     */
    public function getSession()
    {
        // 判断为GET请求
        if (request()->isGet()) {
            // 传入的参数
            $param = input('param.');
            $query = http_build_query($param); // 生成 URL-encode 之后的请求字符串 //halt($query);

            // 查询条件
            $map = [];
            $map['se.venue_id'] = $this->session_venue->venue_id; // 场馆ID
            if (!empty($param['scene_id'])) {
                $map['se.scene_id'] = intval($param['scene_id']);
            }

            // 获取分页page、size
            $this->getPageAndSize($param);

            // 获取分页列表数据 模式一：基于paginate()自动化分页
            $data = model('Session')->getSession($map, $this->size);
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
            list($data['start_time'], $data['end_time']) = explode('-', $data['session_time']);

            // 入库操作
            try {
                $id = model('Session')->add($data, 'session_id');
            } catch (\Exception $e) {
                return show(0, '新增失败 ' . $e->getMessage(), [], 400);
            }
            if ($id) {
                return show(config('code.success'), '新增成功', ['url' => url('Session/index')], 201);
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
                $data = model('Session')->find($id);
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
            }if (!empty($param['session_name'])) { // 场次名称
                $data['session_name'] = trim($param['session_name']);
            }
            if (isset($param['session_price'])) { // 场次价格
                $data['session_price'] = trim($param['session_price']);
            }
            if (!empty($param['session_time'])) { // 场次时间
                list($data['start_time'], $data['end_time']) = explode('-', $param['session_time']);
            }
            if (!empty($param['close_date'])) { // 可预订日期内指定的停场日期
                $data['close_date'] = $param['close_date'];
            }
            if (isset($param['status'])) { // 状态
                $data['status'] = input('param.status', null, 'intval');
            }

            if (empty($data)) {
                return show(config('code.error'), '数据不合法', [], 404);
            }

            // 更新
            try {
                $result = model('Session')->save($data, ['session_id' => $id]); // 更新
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

<?php

namespace app\store\controller;

use app\common\lib\exception\ApiException;
use think\Controller;
use think\Request;

/**
 * store模块店鋪比赛场次模板控制器类
 * Class SessionTemplate
 * @package app\store\controller
 */
class SessionTemplate extends Base
{
    /**
     * 显示店鋪比赛场次模板资源列表
     * @return \think\response\Json
     * @throws ApiException
     */
    public function index()
    {
        // 判断为GET请求
        if (request()->isGet()) {
            // 传入的数据
            $param = input('param.');
            $query = http_build_query($param); // 生成 URL-encode 之后的请求字符串

            // 查询条件
            $map = [];
            $map['st.store_id'] = $this->session_store->store_id;
            if (!empty($param['store_name'])) {
                $map['s.store_name'] = ['like', '%' . $param['store_name'] . '%'];
            }
            if (!empty($param['create_time'])) {
                $map['st.create_time'] = $param['create_time'];
            }

            // 获取分页page、size
            $this->getPageAndSize($param);

            // 获取分页列表数据 模式一：基于paginate()自动化分页
            try {
                $data = model('SessionTemplate')->getSessionTemplate($map, $this->size);
            } catch (\Exception $e) {
                throw new ApiException($e->getMessage(), 500, config('code.error'));
            }

            if ($data) {
                // 处理数据
                $status = config('code.status');
                foreach ($data as $key => $value) {
                    $data[$key]['status_msg'] = $status[$value['status']]; // 定义status_msg
                    //$data[$key]['store_info'] = model('Store')->find($value['store_id']); // 获取店鋪信息
                }

                return show(config('code.success'), 'ok', $data);
            }
        }
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //
    }

    /**
     * 显示指定的店鋪比赛场次模板资源
     * @param int $id
     * @return \think\response\Json
     * @throws ApiException
     */
    public function read($id)
    {
        // 判断为GET请求
        if (request()->isGet()) {
            // 查询条件
            $map = [];
            $map['st.store_id'] = $this->session_store->store_id;

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

                return show(config('code.success'), 'OK', $data);
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
        //
    }

    /**
     * 保存更新的店鋪比赛场次模板资源
     * @param int $id
     * @return \think\response\Json
     * @throws ApiException
     */
    public function update($id)
    {
        // 判断为PUT请求
        if (request()->isPut()) {
            // 传入的数据
            $param = input('param.');

            // validate验证
            $validate = validate('SessionTemplate');
            if (!$validate->check($param, [], 'update')) {
                return show(config('code.error'), $validate->getError(), [], 403);
            }

            // 判断数据是否存在
            $data = [];
            if (!empty($param['session_template'])) {
                $data['session_template'] = $param['session_template'];
            }
            if (isset($param['status'])) { // 不能用 !empty() ，否则 status = 0 时也判断为空
                $data['status'] = input('param.status', null, 'intval');
            }

            if (empty($data)) {
                return show(config('code.error'), '数据不合法', [], 404);
            }

            // 查询条件
            $map = [];
            $map['store_id'] = $this->session_store->store_id;

            // 更新
            try {
                $result = model('SessionTemplate')->save($data, ['session_template_id' => $id, 'store_id' => $map['store_id']]); // 更新
            } catch (\Exception $e) {
                throw new ApiException($e->getMessage(), 500, config('code.error'));
            }
            if ($result) {
                return show(config('code.success'), '更新成功', [], 201);
            } else {
                return show(config('code.error'), '更新失败', [], 403);
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

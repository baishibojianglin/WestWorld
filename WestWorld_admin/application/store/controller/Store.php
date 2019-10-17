<?php

namespace app\store\controller;

use app\common\lib\exception\ApiException;
use app\common\lib\IAuth;
use think\Controller;
use think\Request;

/**
 * store模块店铺信息控制器类
 * Class Store
 * @package app\store\controller
 */
class Store extends Base
{
    /**
     * 显示指定的店铺资源
     * @param int $id
     * @return \think\response\Json
     * @throws ApiException
     */
    public function read($id)
    {
        // 判断为GET请求
        if (request()->isGet()) {
            // 当前登录店铺id
            $id = ($id == $this->session_store->store_id) ? $id : 0;

            try {
                $data = model('Store')->find($id);
            } catch (\Exception $e) {
                throw new ApiException($e->getMessage(), 500, config('code.error'));
            }

            if ($data) {
                // 处理数据
                // 定义status_msg
                $status = config('code.status');
                $data['status_msg'] = $status[$data['status']];

                // 最近登录时间
                $data['last_login_time'] = date('Y-m-d H:i:s', $data['last_login_time']);

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
        //
    }

    /**
     * 保存更新的店铺资源
     * @param Request $request
     * @param int $id
     * @return \think\response\Json
     * @throws ApiException
     */
    public function update(Request $request, $id)
    {
        // 判断为PUT请求
        if (request()->isPut()) {
            // 传入的参数
            $param = input('param.');

            // 当前登录店铺id
            $id = ($id == $this->session_store->store_id) ? $id : 0;

            // validate验证
            $validate = validate('Store');
            if (!$validate->check($param, [], 'update')) {
                return show(config('code.error'), $validate->getError(), [], 403);
            }

            // 判断数据是否存在
            $data = [];
            if (!empty($param['store_name'])) { // 店铺名称
                $data['store_name'] = trim($param['store_name']);
            }
            if (!empty($param['store_account'])) { // 店铺账号
                $data['store_account'] = trim($param['store_account']);
            }
            if (!empty($param['password'])) { // 密码
                $data['password'] = IAuth::encrypt($param['password']);
            }
            if (isset($param['grade_id'])) { // 店铺等级id
                $data['grade_id'] = input('param.grade_id', null, 'intval');
            }
            if (!empty($param['store_phone'])) { // 店铺电话
                $data['store_phone'] = trim($param['store_phone']);
            }
            if (!empty($param['address'])) { // 详细地址
                $data['address'] = trim($param['address']);
            }
            if (!empty($param['store_description'])) { // 店铺介绍
                $data['store_description'] = $param['store_description'];
            }
            if (!empty($param['store_manager'])) { // 店长
                $data['store_manager'] = trim($param['store_manager']);
            }
            if (!empty($param['manager_phone'])) { // 店长电话
                $data['manager_phone'] = trim($param['manager_phone']);
            }
            if (isset($param['status'])) { // 状态 //不能用 !empty() ，否则 status = 0 时也判断为空
                $data['status'] = input('param.status', null, 'intval');
            }

            if (empty($data)) {
                return show(config('code.error'), '数据不合法', [], 404);
            }

            // 更新
            try {
                $result = model('Store')->save($data, ['store_id' => $id]); // 更新
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
}

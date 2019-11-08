<?php

namespace app\venue\controller;

use app\common\lib\exception\ApiException;
use app\common\lib\IAuth;
use think\Controller;
use think\Request;

/**
 * venue模块场馆信息控制器类
 * Class Venue
 * @package app\venue\controller
 */
class Venue extends Base
{

    public function index()
    {
        // 判断为GET请求
        if (request()->isGet()) {
            return $this->fetch();
        }
    }

    /**
     * 显示指定的场馆资源
     * @param int $id
     * @return \think\response\Json
     * @throws ApiException
     */
    public function read($id)
    {
        // 判断为GET请求
        if (request()->isGet()) {
            // 当前登录场馆id
            //$id = ($id == $this->session_venue->venue_id) ? $id : 0;
            $id = $this->session_venue->venue_id;

            try {
                $data = model('Venue')->find($id);
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
        // 判断为GET请求
        if (request()->isGet()) {
            return $this->fetch();
        }
    }

    /**
     * 保存更新的场馆资源
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

            // 当前登录场馆id
            $id = $id ? : $this->session_venue->venue_id;

            // validate验证
            $validate = validate('Venue');
            if (!$validate->check($param, [], 'update')) {
                return show(config('code.error'), $validate->getError(), [], 403);
            }

            // 判断数据是否存在
            $data = [];
            if (!empty($param['venue_name'])) { // 场馆名称
                $data['venue_name'] = trim($param['venue_name']);
            }
            if (!empty($param['venue_account'])) { // 场馆账号
                $data['venue_account'] = trim($param['venue_account']);
            }
            if (!empty($param['password'])) { // 密码
                $data['password'] = IAuth::encrypt($param['password']);
            }
            if (!empty($param['venue_phone'])) { // 场馆电话
                $data['venue_phone'] = trim($param['venue_phone']);
            }
            if (!empty($param['address'])) { // 详细地址
                $data['address'] = trim($param['address']);
            }
            if (!empty($param['longitude'])) { // 场馆经度
                $data['longitude'] = trim($param['longitude']);
            }
            if (!empty($param['latitude'])) { // 场馆纬度
                $data['latitude'] = trim($param['latitude']);
            }
            if (!empty($param['thumb'])) { // 场馆缩略图
                $data['thumb'] = trim($param['thumb']);
            }
            if (!empty($param['venue_description'])) { // 场馆介绍
                $data['venue_description'] = $param['venue_description'];
            }
            if (!empty($param['venue_manager'])) { // 店长
                $data['venue_manager'] = trim($param['venue_manager']);
            }
            if (!empty($param['manager_phone'])) { // 店长电话
                $data['manager_phone'] = trim($param['manager_phone']);
            }

            if (empty($data)) {
                return show(config('code.error'), '数据不合法', [], 404);
            }

            // 更新
            try {
                $result = model('Venue')->save($data, ['venue_id' => $id]); // 更新
            } catch (\Exception $e) {
                throw new ApiException($e->getMessage(), 500, config('code.error'));
            }
            if ($result) {
                return show(config('code.success'), '更新成功', ['url' => url('Venue/index')], 201);
            } else {
                return show(config('code.error'), '更新失败', [], 403);
            }
        }
    }
}

<?php

namespace app\admin\controller;

use app\common\lib\exception\ApiException;
use think\Controller;
use think\Request;

/**
 * 后台比赛场次订单控制器类
 * Class SessionOrder
 * @package app\admin\controller
 */
class SessionOrder extends Base
{
    /**
     * 显示比赛场次订单资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        // 判断为GET请求
        if (request()->isGet()) {
            return $this->fetch('', ['order_status' => config('code.order_status'), 'sceneList' => Scene::sceneList()]);
        }
    }

    /**
     * 获取比赛场次订单资源列表
     * @return \think\response\Json
     * @throws ApiException
     */
    public function getSessionOrder()
    {
        // 判断为GET请求
        if (request()->isGet()) {
            // 传入的数据
            $param = input('param.');
            $query = http_build_query($param); // 生成 URL-encode 之后的请求字符串

            // 查询条件
            $map = [];
            if (!empty($param['user_name'])) { // 用户名称
                $map['u.user_name|u.phone'] = ['like', '%' . $param['user_name'] . '%']; // 多个字段之间用|分割表示OR查询，用&分割表示AND查询
            }
            if (!empty($param['order_sn'])) { // 订单编号
                $map['so.order_sn'] = ['like', '%' . $param['order_sn'] . '%'];
            }
            if (!empty($param['order_time'])) { // 下单时间
                $map['so.order_time'] = ['between time', [$param['order_time'], $param['order_time'] . '23:59:59']];
            }
            if (isset($param['order_status']) && $param['order_status'] != null) { // 订单状态：0未付款，1已付款预约，2进行中，3已完成，4已取消
                $map['so.order_status'] = $param['order_status'];
            }
            if (isset($param['pay_status']) && $param['pay_status'] != null) { // 支付状态
                $map['so.pay_status'] = $param['pay_status'];
            }
            if (!empty($param['pay_time'])) { // 付款时间
                $map['so.pay_time'] = ['between time', [$param['pay_time'], $param['pay_time'] . '23:59:59']];
            }
            if (!empty($param['venue_name'])) { // 场馆
                $map['v.venue_name|s.scene_name'] = ['like', '%' . $param['venue_name'] . '%'];
            }
            if (!empty($param['scene_id'])) { // 场景ID
                $map['so.scene_id'] = intval($param['scene_id']);
            }
            if (!empty($param['session_date'])) { // 比赛场次日期
                $map['so.session_date'] = $param['session_date'];
            }

            // 获取分页page、size
            $this->getPageAndSize($param);

            // 获取分页列表数据 模式一：基于paginate()自动化分页
            try {
                $data = model('SessionOrder')->getSessionOrder($map, $this->size);
            } catch (\Exception $e) {
                throw new ApiException($e->getMessage(), 500, config('code.error'));
            }

            if ($data) {
                // 处理数据
                $orderStatus = config('code.order_status');
                $payStatus = config('code.pay_status');
                foreach ($data as $key => $value) {
                    $data[$key]['order_time'] = $value['order_time'] ? date('Y-m-d H:i:s', $value['order_time']) : ''; // 下单时间
                    $data[$key]['pay_time'] = $value['pay_time'] ? date('Y-m-d H:i:s', $value['pay_time']) : ''; // 付款时间
                    $data[$key]['order_status_msg'] = $orderStatus[$value['order_status']]; // 定义订单状态信息order_status_msg
                    $data[$key]['pay_status_msg'] = $payStatus[$value['pay_status']]; // 定义付款状态信息pay_status_msg
                    //$data[$key]['venue_info'] = model('Venue')->find($value['venue_id']); // 获取店鋪信息
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
     * 显示指定的比赛场次订单资源
     * @param int $id
     * @return \think\response\Json
     * @throws ApiException
     */
    public function read($id)
    {
        // 判断为GET请求
        if (request()->isGet()) {
            try {
                $data = model('SessionOrder')->getSessionOrderById($id);
            } catch (\Exception $e) {
                throw new ApiException($e->getMessage(), 500, config('code.error'));
            }

            if ($data) {
                // 处理数据
                // 定义status_msg
                $status = config('code.pay_status');
                $data['pay_status_msg'] = $status[$data['pay_status']];

                return show(config('code.success'), 'ok', $data);
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
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 删除指定比赛场次订单资源
     * @param int $id
     * @return \think\response\Json
     * @throws ApiException
     */
    public function delete($id)
    {
        // 显示指定的比赛场次订单
        try {
            $data = model('SessionOrder')->find($id);
            //return show(config('code.success'), 'ok', $data);
        } catch (\Exception $e) {
            throw new ApiException($e->getMessage(), 500, config('code.error'));
        }

        // 判断数据是否存在
        if ($data['session_order_id'] != $id) {
            return show(config('code.error'), '数据不存在');
        }

        // 软删除
        if ($data['is_delete'] != config('code.is_delete')) {
            // 捕获异常
            try {
                $result = model('SessionOrder')->softDelete('session_order_id', $id);
            } catch (\Exception $e) {
                throw new ApiException($e->getMessage(), 500, config('code.error'));
            }

            if (!$result) {
                return show(config('code.error'), '软删除失败');
            } else {
                return show(config('code.success'), '软删除成功');
            }
        }

        // 真删除
        if ($data['is_delete'] == config('code.is_delete')) {
            $result = model('SessionOrder')->destroy($id);
            if (!$result) {
                return show(config('code.error'), '删除失败');
            } else {
                return show(config('code.success'), '删除成功');
            }
        }
    }
}

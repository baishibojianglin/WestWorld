<?php

namespace app\api\controller\v1;

use app\common\lib\exception\ApiException;
use think\Controller;
use think\Request;

/**
 * api模块客户端用户比赛场次订单控制器类
 * Class SessionOrder
 * @package app\api\controller\v1
 */
class SessionOrder extends AuthBase
{
    /**
     * 显示用户比赛场次订单资源列表
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
            $map['so.user_id'] = isset($this->user->user_id) ? $this->user->user_id : 0; // 当前登录用户id
            if (!empty($param['venue_name'])) { // 场馆名称
                $map['s.venue_name'] = ['like', '%' . $param['venue_name'] . '%'];
            }
            if (!empty($param['user_name'])) { // 用户名称
                $map['u.user_name'] = ['like', '%' . $param['user_name'] . '%'];
            }
            if (!empty($param['order_sn'])) { // 订单编号
                $map['so.order_sn'] = ['like', '%' . $param['order_sn'] . '%'];
            }
            if (!empty($param['order_time'])) { // 下单时间
                $map['so.order_time'] = $param['order_time'];
            }
            if (!empty($param['pay_status'])) { // 支付状态
                $map['so.pay_status'] = $param['pay_status'];
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
                $status = config('code.pay_status');
                foreach ($data as $key => $value) {
                    $data[$key]['pay_status_msg'] = $status[$value['pay_status']]; // 定义status_msg
                    //$data[$key]['venue_info'] = model('Venue')->find($value['venue_id']); // 获取店鋪信息
                }

                return show(config('code.success'), 'OK', $data);
            }
        }
    }

    /**
     * 保存新建的资源（生成订单）
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        // 判断为POST请求
        if(request()->isPost()){
            $data = input('post.');

            // 生成唯一订单编号 order_sn
            $data['order_sn'] = $this->_getOrderSn();

            // validate验证，包括判断订单是否重复提交
            $validate = validate('SessionOrder');
            if (!$validate->check($data)) {
                return show(config('code.error'), $validate->getError(), [], 403);
            }

            // 入库操作
            try {
                $id = model('SessionOrder')->add($data, 'session_order_id');
            } catch (\Exception $e) {
                return show(config('code.error'), '新增失败 ' . $e->getMessage(), [], 400);
            }
            if ($id) {
                return show(config('code.success'), '新增成功', ['order_sn' => $data['order_sn']], 201);
            } else {
                return show(config('code.error'), '新增失败', [], 400);
            }
        }
    }

    /**
     * 生成唯一订单编号 order_sn
     * @return string
     */
    private function _getOrderSn()
    {
        // 保证不会有重复订单号存在
        while(true){
            $order_sn = date('YmdHis').rand(1000,9999); // 订单编号
            $order_sn_count = model('SessionOrder')->where("order_sn = '$order_sn'")->count();
            if($order_sn_count == 0)
                break;
        }
        return $order_sn;
    }

    /**
     * 显示指定的用户比赛场次订单资源
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
            $map['so.user_id'] = isset($this->user->user_id) ? $this->user->user_id : 0; // 当前登录用户id

            try {
                $data = model('SessionOrder')->getSessionOrderById($map, $id);
            } catch (\Exception $e) {
                throw new ApiException($e->getMessage(), 500, config('code.error'));
            }

            if ($data) {
                // 处理数据
                // 定义status_msg
                $status = config('code.pay_status');
                $data['pay_status_msg'] = $status[$data['pay_status']];

                return show(config('code.success'), 'ok', $data);
            } else {
                return show(config('code.error'), '数据不存在', [], 404);
            }
        }
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
     * 删除指定用户比赛场次订单资源
     * @param $id
     * @return \think\response\Json
     * @throws ApiException
     */
    public function delete($id)
    {
        // 判断为DELETE请求
        if (request()->isDelete()) {
            // 查询条件
            $map = [];
            $map['user_id'] = isset($this->user->user_id) ? $this->user->user_id : 0; // 当前登录用户id

            // 显示指定的比赛场次订单
            try {
                $data = model('SessionOrder')->where($map)->find($id);
                //return show(config('code.success'), 'ok', $data);
            } catch (\Exception $e) {
                throw new ApiException($e->getMessage(), 500, config('code.error'));
            }

            // 判断数据是否存在
            if ($data['session_order_id'] != $id || $data['is_delete'] == config('code.is_delete')) {
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
                    return show(config('code.error'), '删除失败');
                } else {
                    return show(config('code.success'), '删除成功');
                }
            }
        }
    }
}

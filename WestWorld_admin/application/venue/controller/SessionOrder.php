<?php

namespace app\venue\controller;

use app\common\lib\exception\ApiException;
use think\Controller;
use think\Db;
use think\Request;

/**
 * venue模块比赛场次订单控制器类
 * Class SessionOrder
 * @package app\venue\controller
 */
class SessionOrder extends Base
{
    /**
     * 显示比赛场次订单资源列表
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
            $map['s.venue_id'] = isset($this->session_venue->venue_id) ? $this->session_venue->venue_id : 0; // 当前登录场馆id
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
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
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
     * 保存更新的该比赛场次订单用户获得的比赛积分（场馆手动修改积分）
     * @param $id
     * @return \think\response\Json
     * @throws ApiException
     */
    public function updateSessionOrderPoints($id)
    {
        // 判断为PUT请求
        if (request()->isPut()) {
            // 传入的数据
            $param = input('param.');

            // 判断数据是否存在
            $data = [];
            $data['session_order_id'] = (isset($param['session_order_id'])) ? $param['session_order_id'] : $id;
            if (isset($param['get_points'])) {
                $data['get_points'] = $param['get_points'];
            }

            // 手动控制事务
            // 启动事务
            Db::startTrans();
            $result = [];
            try{
                // 查询 session_order 表 user_id, venue_id, get_points
                $result[] = $session_order = Db::name('session_order')->field('order_sn, user_id, venue_id, get_points')->find($data['session_order_id']);
                $user_id = $session_order['user_id'];
                $venue_id = $session_order['venue_id'];
                $change_points = $data['get_points'] - $session_order['get_points']; // 变动积分 = 传入的积分 - 订单原来的积分

                // 查询用户积分 get_points, user_points
                $result[] = $user = Db::name('user')->field('get_points, user_points')->find($user_id);

                // 更新 session_order 表积分 get_points
                $result[] = db('session_order', [], false)->update($data);

                // 更新用户积分（加上要更新传入的积分，同时减去订单表待更新的原积分）
                $result[] = db('user', [], false)->update([
                    'get_points' => $user['get_points'] + $change_points,
                    'user_points' => $user['user_points'] + $change_points,
                    'user_id' => $user_id
                ]);

                // 新增用户积分变动日志
                $user_points_log_data = [
                    'user_id' => $user_id,
                    'venue_id' => $venue_id,
                    'before_points' => $user['user_points'],
                    'change_points' => $change_points,
                    'after_points' => ($user['user_points'] + $change_points),
                    'log_content' => '场馆手动修改积分：订单编号' . $session_order['order_sn'],
                    'log_time' => time(),
                    'operator_type' => 2,
                    'operator_id' => $this->session_venue->venue_id,
                ];
                $result[] = db('user_points_log', [], false)->insert($user_points_log_data);

                // 查询用户在各场馆积分 get_points
                $result[] = $user_venue_points = db('user_venue_points', [], false)->field('get_points')->where(['user_id' => $user_id, 'venue_id' => $venue_id])->find();

                // 更新用户在各场馆积分
                $result[] = db('user_venue_points', [], false)
                    ->where(['user_id' => $user_id, 'venue_id' => $venue_id])
                    ->update(['get_points' => $user_venue_points['get_points'] + $change_points]);

                // 提交事务
                Db::commit();
                return show(config('code.success'), '更新成功', [], 201);
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                throw new ApiException($e->getMessage(), 500, config('code.error'));
            }

            // 更新
            /*try {
                $result = model('SessionOrder')->save($data, ['session_order_id' => $id]); // 更新
            } catch (\Exception $e) {
                throw new ApiException($e->getMessage(), 500, config('code.error'));
            }
            if ($result) {
                return show(config('code.success'), '更新成功', [], 201);
            } else {
                return show(config('code.error'), '更新失败', [], 403);
            }*/
        } else {
            return show(config('code.error'), '请求不合法', [], 400);
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

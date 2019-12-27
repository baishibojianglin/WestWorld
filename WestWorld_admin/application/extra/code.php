<?php
/**
 * Created by PhpStorm.
 * User: Yan
 * Date: 2019/7/22
 * Time: 17:52
 */

// 状态码配置
return [
    // API接口状态码
    'error' => 0,
    'success' => 1,

    // 用户及其他状态
    'status' => [
        0 => '停用',
        1 => '启用',
        2 => '待审核',
    ],

    'status_disable' => 0, // 关闭/停用
    'status_enable' => 1, // 开启/启用
    'status_pending' => 2, // 待审核

    // 是否删除
    'not_delete' => 0, // 未删除
    'is_delete' => 1, // 删除

    // 性别：0保密，1男，2女
    'gender' => [
        0 => '保密',
        1 => '男',
        2 => '女',
    ],

    // 订单状态：0未付款，1已付款预约，2进行中，3已完成，4已取消
    'order_status' => [
        0 => '未付款',
        1 => '已预约',
        2 => '进行中', // TODO：已支付订单 pay_status = 1，且之前是已预约 order_status = 1 的订单，若当前时间处于比赛场次开始时间与结束时间之间 start_time <= NOW() < end_time，使用数据库存储过程作`进行中`处理
        3 => '已完成', // TODO：已支付订单 pay_status = 1，不论之前是已预约 order_status = 1 或进行中 order_status = 2 的订单，若当前时间大于比赛场次结束时间 NOW() > end_time，使用数据库存储过程作`已完成`处理
        4 => '已取消', // TODO：过期未支付订单，即当前时间大于或等于比赛场次开始时间 NOW() >= start_time 且 pay_status = order_status = 0，使用数据库存储过程作`已取消`处理
    ],

    // 支付状态
    'pay_status' => [
        0 => '未支付',
        1 => '已支付',
    ],

    // （场景房间）预订状态
    'is_booked' => [
        0 => '未预订',
        1 => '已预订',
    ],
];
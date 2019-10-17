<?php

namespace app\common\model;

use think\Model;

/**
 * 比赛场次订单模型类
 * Class SessionOrder
 * @package app\common\model
 */
class SessionOrder extends Base
{
    /**
     * 获取比赛场次订单列表数据（基于paginate()自动化分页）
     * @param array $map
     * @param int $size
     * @return \think\Paginator
     */
    public function getSessionOrder($map = [], $size = 5)
    {
        if(!isset($map['so.is_delete'])) {
            $map['so.is_delete'] = ['neq', config('code.is_delete')];
        }

        $order = ['so.session_order_id' => 'desc'];
        $join = [
            ['__USER__ u', 'u.user_id = so.user_id', 'LEFT'],
            ['__STORE__ s', 's.store_id = so.store_id', 'LEFT'],
            ['__SESSION_TEMPLATE__ st', 'st.session_template_id = so.session_template_id', 'LEFT'],
        ];

        $result = $this->alias('so')
            ->field($this->_getListField())
            ->join($join)
            ->where($map)
            ->order($order)
            ->paginate($size);
        return $result;
    }

    /**
     * 通过主键id获取指定的比赛场次订单
     * @param array $map
     * @param int $id
     * @return array|false|\PDOStatement|string|Model
     */
    public function getSessionOrderById($map = [], $id)
    {
        if(!isset($map['so.is_delete'])) {
            $map['so.is_delete'] = ['neq', config('code.is_delete')];
        }

        $join = [
            ['__USER__ u', 'u.user_id = so.user_id', 'LEFT'],
            ['__STORE__ s', 's.store_id = so.store_id', 'LEFT'],
            ['__SESSION_TEMPLATE__ st', 'st.session_template_id = so.session_template_id', 'LEFT'],
        ];

        $data = model('SessionOrder')
            ->alias('so')
            ->field($this->_getListField())
            ->join($join)
            ->where($map)
            ->find($id);
        return $data;
    }

    /**
     * 通用化获取参数的数据字段
     * @return array
     */
    private function _getListField()
    {
        return [
            'so.session_order_id',
            'so.order_sn',
            'so.user_id',
            'so.store_id',
            'so.session_template_id',
            'so.session_price',
            'so.session_date',
            'so.session_start_time',
            'so.session_end_time',
            'so.order_time',
            'so.pay_status',
            'so.pay_time',
            'so.get_points',
            'u.user_name',
            'u.phone user_phone',
            'u.head_pic',
            's.store_name',
            's.store_account',
            's.store_phone',
            's.store_manager',
        ];
    }
}

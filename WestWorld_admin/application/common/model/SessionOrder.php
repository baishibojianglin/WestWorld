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
            ['__VENUE__ v', 'v.venue_id = so.venue_id', 'LEFT'],
            ['__SCENE__ s', 's.scene_id = so.scene_id', 'LEFT'],
            ['__SCENE_ROOM__ sr', 'sr.room_id = so.room_id', 'LEFT'],
            ['__EQUIPMENT__ e', 'e.equipment_id = so.equipment_id', 'LEFT'],
            ['__SESSION__ se', 'se.session_id = so.session_id', 'LEFT']
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
            ['__VENUE__ v', 'v.venue_id = so.venue_id', 'LEFT'],
            ['__SCENE__ s', 's.scene_id = so.scene_id', 'LEFT'],
            ['__SCENE_ROOM__ sr', 'sr.room_id = so.room_id', 'LEFT'],
            ['__EQUIPMENT__ e', 'e.equipment_id = so.equipment_id', 'LEFT'],
            ['__SESSION__ se', 'se.session_id = so.session_id', 'LEFT']
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
            'so.session_teams_id',
            'so.team_id',
            'so.user_id',
            'so.venue_id',
            'so.scene_id',
            'so.session_date',
            'so.session_id',
            'so.session_price',
            'so.room_id',
            'so.room_price',
            'so.equipment_id',
            'so.equipment_use_fee',
            'so.order_time',
            'so.order_status',
            'so.pay_status',
            'so.pay_time',
            'so.order_price',
            'so.total_price',
            'so.get_integrals',
            'u.user_name',
            'u.phone user_phone',
            'u.avatar',
            'v.venue_name',
            'v.venue_account',
            'v.thumb',
            'v.venue_phone',
            'v.venue_manager',
            's.scene_name',
            'sr.room_name',
            'e.equipment_name',
            'se.start_time',
            'se.end_time'
        ];
    }
}

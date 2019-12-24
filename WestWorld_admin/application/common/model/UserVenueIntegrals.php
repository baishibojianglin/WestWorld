<?php

namespace app\common\model;

use think\Model;

/**
 * 用户在各场馆积分模型类
 * Class UserVenueIntegrals
 * @package app\common\model
 */
class UserVenueIntegrals extends Base
{
    /**
     * 获取用户在各场馆积分列表数据（基于paginate()自动化分页）
     * @param array $map
     * @param int $size
     * @return \think\Paginator
     */
    public function getUserVenueIntegrals($map = [], $size = 5)
    {
        $order = ['uvi.id' => 'desc'];
        $join = [
            ['__USER__ u', 'u.user_id = uvi.user_id', 'LEFT'],
            ['__VENUE__ s', 's.venue_id = uvi.venue_id', 'LEFT'],
        ];

        $result = $this->alias('uvi')
            ->field($this->_getListField())
            ->join($join)
            ->where($map)
            ->order($order)
            ->paginate($size);
        return $result;
    }

    /**
     * 通用化获取参数的数据字段
     * @return array
     */
    private function _getListField()
    {
        return [
            'uvi.id',
            'uvi.user_id',
            'uvi.venue_id',
            'uvi.pay_money',
            'uvi.get_integrals',
            'u.user_name',
            'u.phone user_phone',
            'u.avatar',
            's.venue_name',
            's.venue_account',
            's.venue_phone',
            's.venue_manager',
        ];
    }
}

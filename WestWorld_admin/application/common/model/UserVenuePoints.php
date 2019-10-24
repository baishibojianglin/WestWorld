<?php

namespace app\common\model;

use think\Model;

/**
 * 用户在各场馆积分模型类
 * Class UserVenuePoints
 * @package app\common\model
 */
class UserVenuePoints extends Base
{
    /**
     * 获取用户在各场馆积分列表数据（基于paginate()自动化分页）
     * @param array $map
     * @param int $size
     * @return \think\Paginator
     */
    public function getUserVenuePoints($map = [], $size = 5)
    {
        $order = ['usp.id' => 'desc'];
        $join = [
            ['__USER__ u', 'u.user_id = usp.user_id', 'LEFT'],
            ['__VENUE__ s', 's.venue_id = usp.venue_id', 'LEFT'],
        ];

        $result = $this->alias('usp')
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
            'usp.id',
            'usp.user_id',
            'usp.venue_id',
            'usp.pay_money',
            'usp.get_points',
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

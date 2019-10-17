<?php

namespace app\common\model;

use think\Model;

/**
 * 用户在各店铺积分模型类
 * Class UserStorePoints
 * @package app\common\model
 */
class UserStorePoints extends Base
{
    /**
     * 获取用户在各店铺积分列表数据（基于paginate()自动化分页）
     * @param array $map
     * @param int $size
     * @return \think\Paginator
     */
    public function getUserStorePoints($map = [], $size = 5)
    {
        $order = ['usp.id' => 'desc'];
        $join = [
            ['__USER__ u', 'u.user_id = usp.user_id', 'LEFT'],
            ['__STORE__ s', 's.store_id = usp.store_id', 'LEFT'],
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
            'usp.store_id',
            'usp.pay_money',
            'usp.get_points',
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

<?php

namespace app\common\model;

use think\Model;

/**
 * 店铺模型类
 * Class Store
 * @package app\common\model
 */
class Store extends Base
{
    /**
     * 获取店铺列表数据（基于paginate()自动化分页）
     * @param array $map
     * @return \think\Paginator
     */
    public function getStore($map = [])
    {
        if(!isset($map['is_delete'])) {
            $map['is_delete'] = ['neq', config('code.is_delete')];
        }

        $order = ['store_id' => 'desc'];

        $result = $this->field('store_description', true) // 字段排除
            ->where($map)
            ->order($order)
            ->paginate();
        return $result;
    }

    /**
     * 根据条件获取店铺列表数据
     * @param array $map
     * @param int $from
     * @param int $size
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getStoreByCondition($map = [], $from = 0, $size = 5)
    {
        if(!isset($map['is_delete'])) {
            $map['is_delete'] = ['neq', config('code.is_delete')];
        }

        $order = ['store_id' => 'desc'];

        $result = $this->where($map)
            ->field($this->_getListField())
            ->limit($from, $size)
            ->order($order)
            ->select();
        //echo $this->getLastSql();
        return $result;
    }

    /**
     * 根据条件获取店铺列表数据的总数
     * @param array $map
     * @return int|string
     * @throws \think\Exception
     */
    public function getStoreCountByCondition($map = [])
    {
        if(!isset($map['is_delete'])) {
            $map['is_delete'] = ['neq', config('code.is_delete')];
        }

        $count = $this->where($map)->count();
        return $count;
    }

    /**
     * 通用化获取参数的数据字段
     * @return array
     */
    private function _getListField()
    {
        return [
            'store_id',
            'store_name',
            'store_account',
            'grade_id',
            'store_phone',
            'address',
            'head_pic',
            //'store_description',
            'store_manager',
            'manager_phone',
            'store_sales',
            'store_money',
            'pending_money',
            'status',
            'store_close_info',
            'is_delete',
            'create_time',
            'update_time',
        ];
    }
}

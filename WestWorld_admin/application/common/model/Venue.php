<?php

namespace app\common\model;

use think\Model;

/**
 * 场馆模型类
 * Class Venue
 * @package app\common\model
 */
class Venue extends Base
{
    /**
     * 获取场馆列表数据（基于paginate()自动化分页）
     * @param array $map
     * @return \think\Paginator
     */
    public function getVenue($map = [])
    {
        if(!isset($map['is_delete'])) {
            $map['is_delete'] = ['neq', config('code.is_delete')];
        }

        $order = ['venue_id' => 'desc'];

        $result = $this->field('venue_description', true) // 字段排除
            ->where($map)
            ->order($order)
            ->paginate();
        return $result;
    }

    /**
     * 根据条件获取场馆列表数据
     * @param array $map
     * @param int $from
     * @param int $size
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getVenueByCondition($map = [], $from = 0, $size = 5)
    {
        if(!isset($map['is_delete'])) {
            $map['is_delete'] = ['neq', config('code.is_delete')];
        }

        $order = ['venue_id' => 'desc'];

        $result = $this->where($map)
            ->field($this->_getListField())
            ->limit($from, $size)
            ->order($order)
            ->select();
        //echo $this->getLastSql();
        return $result;
    }

    /**
     * 根据条件获取场馆列表数据的总数
     * @param array $map
     * @return int|string
     * @throws \think\Exception
     */
    public function getVenueCountByCondition($map = [])
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
            'venue_id',
            'venue_name',
            'venue_account',
            'grade_id',
            'venue_phone',
            'address',
            'thumb',
            //'venue_description',
            'venue_manager',
            'manager_phone',
            'venue_sales',
            'venue_money',
            'pending_money',
            'status',
            'venue_close_info',
            'is_delete',
            'create_time',
            'update_time',
        ];
    }
}

<?php

namespace app\common\model;

use think\Model;

/**
 * 场景模型类
 * Class Scene
 * @package app\common\model
 */
class Scene extends Base
{
    /**
     * 获取场景列表数据（基于paginate()自动化分页）
     * @param array $map
     * @return \think\Paginator
     */
    public function getScene($map = [], $size = 5)
    {
        /*if(!isset($map['is_delete'])) {
            $map['is_delete'] = ['neq', config('code.is_delete')];
        }*/

        $order = ['s.scene_id' => 'asc'];

        $result = $this->alias('s')
            ->field('s.scene_id, s.scene_name, s.venue_id, s.thumb, s.booking_days, s.status, v.venue_name') // 字段排除 s.scene_description, s.game_rules等
            ->join('__VENUE__ v', 's.venue_id = v.venue_id', 'LEFT')
            ->where($map)
            ->order($order)
            ->paginate($size);
        return $result;
    }

    /**
     * 根据条件获取场景列表数据
     * @param array $map
     * @param int $from
     * @param int $size
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getSceneByCondition($map = [], $from = 0, $size = 5)
    {
        /*if(!isset($map['is_delete'])) {
            $map['is_delete'] = ['neq', config('code.is_delete')];
        }*/

        $order = ['scene_id' => 'desc'];

        $result = $this->where($map)
            ->field($this->_getListField())
            ->limit($from, $size)
            ->order($order)
            ->select();
        return $result;
    }

    /**
     * 根据条件获取场景列表数据的总数
     * @param array $map
     * @return int|string
     * @throws \think\Exception
     */
    public function getSceneCountByCondition($map = [])
    {
        /*if(!isset($map['is_delete'])) {
            $map['is_delete'] = ['neq', config('code.is_delete')];
        }*/

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
            'scene_id',
            'scene_name',
            'venue_id',
            'thumb',
            'booking_days',
            'status',
        ];
    }
}

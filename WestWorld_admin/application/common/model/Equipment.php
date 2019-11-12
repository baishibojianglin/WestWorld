<?php

namespace app\common\model;

use think\Model;

/**
 * 装备模型类
 * Class Equipment
 * @package app\common\model
 */
class Equipment extends Base
{
    /**
     * 获取装备列表数据（基于paginate()自动化分页）
     * @param array $map
     * @param int $size
     * @return \think\Paginator
     */
    public function getEquipment($map = [], $size = 5)
    {
        /*if(!isset($map['is_delete'])) {
            $map['is_delete'] = ['neq', config('code.is_delete')];
        }*/

        $order = ['e.equipment_id' => 'asc'];

        $result = $this->alias('e')
            ->field('e.equipment_id, e.equipment_name, e.scene_id, e.venue_id, e.thumb, e.use_fee, e.use_number, e.equipment_number, s.scene_name, v.venue_name')
            ->join('__SCENE__ s', 'e.scene_id = s.scene_id', 'LEFT')
            ->join('__VENUE__ v', 'e.venue_id = v.venue_id', 'LEFT')
            ->where($map)
            ->order($order)
            ->paginate($size);
        return $result;
    }
}

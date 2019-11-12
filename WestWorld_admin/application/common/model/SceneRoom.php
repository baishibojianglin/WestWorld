<?php

namespace app\common\model;

use think\Model;

/**
 * 场景房间模型类
 * Class SceneRoom
 * @package app\common\model
 */
class SceneRoom extends Base
{
    /**
     * 获取场景房间列表数据（基于paginate()自动化分页）
     * @param array $map
     * @param int $size
     * @return \think\Paginator
     */
    public function getSceneRoom($map = [], $size = 5)
    {
        /*if(!isset($map['is_delete'])) {
            $map['is_delete'] = ['neq', config('code.is_delete')];
        }*/

        $order = ['sr.room_id' => 'asc'];

        $result = $this->alias('sr')
            ->field('sr.room_id, sr.room_name, sr.scene_id, sr.venue_id, sr.room_price, sr.available_number, sr.join_number, sr.is_booked, sr.status, s.scene_name, v.venue_name')
            ->join('__SCENE__ s', 'sr.scene_id = s.scene_id', 'LEFT')
            ->join('__VENUE__ v', 'sr.venue_id = v.venue_id', 'LEFT')
            ->where($map)
            ->order($order)
            ->paginate($size);
        return $result;
    }
}

<?php

namespace app\common\model;

use think\Model;

/**
 * 场次模型类
 * Class Session
 * @package app\common\model
 */
class Session extends Base
{
    /**
     * 获取场次列表数据（基于paginate()自动化分页）
     * @param array $map
     * @param int $size
     * @return \think\Paginator
     */
    public function getSession($map = [], $size = 5)
    {
        /*if(!isset($map['is_delete'])) {
            $map['is_delete'] = ['neq', config('code.is_delete')];
        }*/

        $order = ['se.session_id' => 'asc'];

        $result = $this->alias('se')
            ->field('se.session_id, se.session_name, se.scene_id, se.venue_id, se.session_price, se.start_time, se.end_time, se.close_date, se.status, s.scene_name, v.venue_name')
            ->join('__SCENE__ s', 'se.scene_id = s.scene_id', 'LEFT')
            ->join('__VENUE__ v', 'se.venue_id = v.venue_id', 'LEFT')
            ->where($map)
            ->order($order)
            ->paginate($size);
        return $result;
    }
}

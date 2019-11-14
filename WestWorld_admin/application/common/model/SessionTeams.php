<?php

namespace app\common\model;

use think\Model;

/**
 * 比赛场次组队模型类
 * Class SessionTeams
 * @package app\common\model
 */
class SessionTeams extends Base
{
    /**
     * 获取比赛场次组队列表数据（基于paginate()自动化分页）
     * @param array $map
     * @param int $size
     * @return \think\Paginator
     */
    public function getSessionTeams($map = [], $size = 5)
    {
        if(!isset($map['st.session_date'])) { // 默认获取今天的比赛场次
            $map['st.session_date'] = date('Y-m-d', time());
        }

        $order = ['st.session_date' => 'desc']; // 比赛场次日期

        $result = $this->alias('st')
            ->field('st.session_teams_id, st.venue_id, st.scene_id, st.session_date, st.session_id, st.room_id, v.venue_name, sc.scene_name, se.session_name, se.start_time, se.end_time, sr.room_name')
            ->join('__VENUE__ v', 'st.venue_id = v.venue_id', 'LEFT')
            ->join('__SCENE__ sc', 'st.scene_id = sc.scene_id', 'LEFT')
            ->join('__SESSION__ se', 'st.session_id = se.session_id', 'LEFT')
            ->join('__SCENE_ROOM__ sr', 'st.room_id = sr.room_id', 'LEFT')
            ->where($map)
            ->order($order)
            ->paginate($size);
        return $result;
    }
}

<?php

namespace app\common\model;

use think\Model;

/**
 * 店鋪比赛场次模板模型类
 * Class SessionTemplate
 * @package app\common\model
 */
class SessionTemplate extends Base
{
    /**
     * 获取店鋪比赛场次模板列表数据（基于paginate()自动化分页）
     * @param array $map
     * @param int $size
     * @return \think\Paginator
     */
    public function getSessionTemplate($map = [], $size = 5)
    {
        if(!isset($map['st.is_delete'])) {
            $map['st.is_delete'] = ['neq', config('code.is_delete')];
        }

        $order = ['st.session_template_id' => 'desc'];

        $result = $this->alias('st')
            ->field($this->_getListField())
            ->join('__VENUE__ s', 's.venue_id = st.venue_id', 'LEFT')
            ->where($map)
            ->order($order)
            ->paginate($size);
        return $result;
    }

    /**
     * 通过主键id获取指定的店鋪比赛场次模板
     * @param array $map
     * @param int $id
     * @return array|false|\PDOStatement|string|Model
     */
    public function getSessionTemplateById($map = [], $id)
    {
        if(!isset($map['st.is_delete'])) {
            $map['st.is_delete'] = ['neq', config('code.is_delete')];
        }

        $data = model('SessionTemplate')
            ->alias('st')
            ->field('st.*, s.venue_name, s.venue_account, s.venue_phone, s.venue_manager')
            ->join('__VENUE__ s', 's.venue_id = st.venue_id', 'LEFT')
            ->where($map)
            ->find($id);
        return $data;
    }

    /**
     * 通用化获取参数的数据字段
     * @return array
     */
    private function _getListField()
    {
        return [
            'st.session_template_id',
            'st.venue_id',
            'st.status',
            'st.is_delete',
            'st.create_time',
            'st.update_time',
            's.venue_name',
            's.venue_account',
            's.venue_phone',
            's.venue_manager',
        ];
    }
}

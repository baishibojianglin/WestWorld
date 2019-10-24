<?php

namespace app\common\model;

use think\Model;

/**
 * 用户积分变动日志模型类
 * Class UserPointsLog
 * @package app\common\model
 */
class UserPointsLog extends Base
{
    /**
     * 获取用户积分变动日志列表数据（基于paginate()自动化分页）
     * @param array $map
     * @param int $size
     * @return \think\Paginator
     */
    public function getUserPointsLog($map = [], $size = 5)
    {
        $order = ['upl.id' => 'desc'];
        $join = [
            ['__USER__ u', 'u.user_id = upl.user_id', 'LEFT'],
            ['__VENUE__ s', 's.venue_id = upl.venue_id', 'LEFT'],
        ];

        $result = $this->alias('upl')
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
            'upl.*',
            'u.user_name',
            's.venue_name',
        ];
    }
}

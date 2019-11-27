<?php

namespace app\common\model;

use think\Model;

/**
 * 广告模型类
 * Class Ad
 * @package app\common\model
 */
class Ad extends Base
{
    /**
     * 获取广告列表数据（基于paginate()自动化分页）
     * @param array $map
     * @param int $size
     * @return \think\Paginator
     */
    public function getAd($map = [], $size = 5)
    {
        if(!isset($map['a.is_delete'])) {
            $map['a.is_delete'] = ['neq', config('code.is_delete')];
        }

        $order = ['a.ad_id' => 'asc'];

        $result = $this->alias('a')
            ->field('a.*, ap.position_name')
            ->join('__AD_POSITION__ ap', 'a.position_id = ap.position_id', 'LEFT')
            ->where($map)
            ->order($order)
            ->paginate($size);
        return $result;
    }
}

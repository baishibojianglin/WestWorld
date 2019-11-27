<?php

namespace app\common\model;

use think\Model;

/**
 * 广告位置模型类
 * Class AdPosition
 * @package app\common\model
 */
class AdPosition extends Base
{
    /**
     * 获取广告位置列表数据（基于paginate()自动化分页）
     * @param array $map
     * @param int $size
     * @return \think\Paginator
     */
    public function getAdPosition($map = [], $size = 5)
    {
        $order = ['position_id' => ''];

        $result = $this
            ->field('position_style', true)
            ->where($map)
            ->order($order)
            ->paginate($size);
        return $result;
    }
}

<?php

namespace app\common\model;

use think\Model;

/**
 * 用户模型类
 * Class User
 * @package app\common\model
 */
class User extends Base
{
    /**
     * 获取用户列表数据（基于paginate()自动化分页）
     * @param array $map
     * @param int $size
     * @return \think\Paginator
     */
    public function getUser($map = [], $size = 5)
    {
        if(!isset($map['is_delete'])) {
            $map['is_delete'] = ['neq', config('code.is_delete')];
        }

        $order = ['user_id' => 'desc'];

        $result = $this->field('password', true) // 字段排除
            ->where($map)
            ->order($order)
            ->paginate($size);
        return $result;
    }
}

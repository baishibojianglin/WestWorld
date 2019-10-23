<?php
/**
 * Created by PhpStorm.
 * User: 問安冬日
 * Date: 2019/7/29
 * Time: 10:43
 */

namespace app\common\validate;

use think\Validate;

class SessionTemplate extends Validate
{
    protected $rule = [
        'store_id' => 'require',
        'session_template' => 'require',
    ];

    protected $message = [
        'store_id.require' => '店铺不能为空',
        'session_template.require' => '店鋪比赛场次模板不能为空',
    ];

    protected $scene = [
        'update' => ['session_template'], // 表示验证update场景（该场景定义只需要验证session_template字段）
    ];
}
<?php
/**
 * Created by PhpStorm.
 * User: Yan
 * Date: 2019/8/6
 * Time: 17:40
 */

namespace app\common\validate;

use think\Validate;

class Venue extends Validate
{
    protected $rule = [
        'venue_account' => 'require|unique:venue|max:20',
        'password' => 'require|max:20',
    ];

    protected $message = [
        'venue_account.require' => '场馆账号不能为空',
        'venue_account.unique' => '场馆已存在', // 唯一性
        'venue_account.max'     => '场馆账号长度不能超过20',
        'password.require' => '密码不能为空',
        'password.max'     => '密码长度不能超过20',
    ];

    protected $scene = [
        'login' => ['venue_account' => 'require|max:20', 'venue_account.unique' => 'unique:venue, venue_account^venue_id', 'password'], // 忽略唯一(unique)类型字段venue_account对自身数据的唯一性验证
        'update' => ['venue_account' => 'require|max:20', 'venue_account.unique' => 'unique:venue, venue_account^venue_id', 'password'],
    ];
}
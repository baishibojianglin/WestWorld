<?php
/**
 * Created by PhpStorm.
 * User: 問安冬日
 * Date: 2019/8/6
 * Time: 17:40
 */

namespace app\common\validate;

use think\Validate;

class Store extends Validate
{
    protected $rule = [
        'store_account' => 'require|unique:store|max:20',
        'password' => 'require|max:20',
    ];

    protected $message = [
        'store_account.require' => '店铺账号不能为空',
        'store_account.unique' => '店铺已存在', // 唯一性
        'store_account.max'     => '店铺账号长度不能超过20',
        'password.require' => '密码不能为空',
        'password.max'     => '密码长度不能超过20',
    ];

    protected $scene = [
        'login' => ['store_account' => 'require|max:20', 'store_account.unique' => 'unique:store, store_account^store_id', 'password'], // 忽略唯一(unique)类型字段store_account对自身数据的唯一性验证
        'update' => ['store_account' => 'require|max:20', 'store_account.unique' => 'unique:store, store_account^store_id', 'password'],
    ];
}
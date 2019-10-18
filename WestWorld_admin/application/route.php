<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

/*return [
    '__pattern__' => [
        'name' => '\w+',
    ],
    '[hello]'     => [
        ':id'   => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
        ':name' => ['index/hello', ['method' => 'post']],
    ],

];*/


use think\Route;

// 示例路由
Route::get('test', 'api/test/index'); // 读取：显示资源列表
Route::get('test/create', 'api/test/create'); // 读取：显示创建资源表单页
Route::post('test', 'api/test/save'); // 新增：保存新建的资源
Route::get('test/:id', 'api/test/read'); // 读取：显示指定的资源
Route::get('test/:id', 'api/test/edit'); // 读取：显示编辑资源表单页
Route::put('test/:id', 'api/test/update'); // 更新：保存更新的资源
Route::delete('test/:id', 'api/test/delete'); // 删除：删除指定资源
Route::resource('test', 'api/test'); // 资源路由：设置后会自动注册7个路由规则

// 客户端路由
// 首页
Route::get('api/:ver/index', 'api/:ver.index/index');
// 登录
Route::post('api/:ver/login', 'api/:ver.login/save');
// 注册
Route::post('api/:ver/register', 'api/:ver.login/register');
// 退出登录
Route::put('api/:ver/login/:id', 'api/:ver.login/update');
// 用户
Route::resource('api/:ver/user', 'api/:ver.user');
// 图片上传
Route::post('api/:ver/image', 'api/:ver.image/save');
// 比赛场次订单
Route::resource('api/:ver/session_order', 'api/:ver.session_order');
// 店铺
Route::resource('api/:ver/store', 'api/:ver.store');
// 店鋪比赛场次模板
Route::resource('api/:ver/session_template', 'api/:ver.session_template');

/*// 后台管理中心路由
// 显示指定的管理员
Route::resource('admin/admin', 'admin/admin');*/

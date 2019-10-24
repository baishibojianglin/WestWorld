<?php

namespace app\venue\controller;

use app\common\lib\exception\ApiException;
use app\common\lib\IAuth;
use think\Controller;

/**
 * venue模块登录控制器类
 * Class Login
 * @package app\venue\controller
 */
class Login extends Base
{
    /**
     * 初始化
     */
    public function _initialize()
    {
        // 重写并覆盖Base类的_initialize()方法

        // 初始化参数
        $this->module = request()->module();
        $this->session_venue = session(config('venue.session_venue'), '', config('venue.session_venue_scope'));
    }

    /**
     * 登录页
     * @return \think\response\Json|void
     */
    public function index()
    {
        // 如果后台用户已经登录，需要跳转到后台首页
        $isLogin = $this->isLogin(); // isLogin() 方法调用当前 venue/Login 控制器的 $this->session_venue 参数
        if($isLogin) {
            return $this->redirect('Index/index');
        } else {
            return show(config('code.error'), '未登录', ['url' => $this->module . '/Login/index'], 401); //return $this->fetch();
        }
    }

    /**
     * 登录验证
     * @return \think\response\Json
     * @throws ApiException
     */
    public function check()
    {
        if (request()->isPost()) {
            $data = input('post.');
            // 判断验证码
            /*if (!captcha_check($data['captcha'])) {
                return show(config('code.error'), '验证码错误', [], 404);
            }*/

            // validate校验账号、密码是否合法
            $validate = validate('Venue');
            if (!$validate->check($data, $rule = [], $scene = 'login')) {
                return show(config('code.error'), $validate->getError(), [], 403); //$this->error($validate->getError());
            }

            // 先判断账号，再判断密码
            try {
                $venue_data = model('Venue')->get(['venue_account' => $data['venue_account']]);
            } catch (\Exception $e) {
                throw new ApiException($e->getMessage(), 500);
            }
            // 判断账号是否存在或正常
            if (!$venue_data || ($venue_data->status != config('code.status_enable'))) {
                return show(config('code.error'), '场馆不存在', [], 404);
            }
            // 判断密码是否正确
            if (IAuth::encrypt($data['password']) != $venue_data['password']) {
                return show(config('code.error'), '密码错误', [], 404);
            }
            //halt($venue_data);

            // 更新数据库：登录时间、登录IP
            $update_data = [
                'last_login_time' => time(),
                'last_login_ip' => request()->ip(),
            ];
            try {
                model('Venue')->save($update_data, ['venue_id' => $venue_data->venue_id]);
            } catch (\Exception $e) {
                throw new ApiException($e->getMessage(), 500);
            }

            // 存session
            session(config('venue.session_venue'), $venue_data, config('venue.session_venue_scope'));
            return show(config('code.success'), '登录成功', ['url' => $this->module . '/Index/index']); //$this->success('登录成功', 'Index/index');
        } else {
            return show(config('code.error'), '请求不合法', [], 400);
        }
    }

    /**
     * 注销
     * 1.清空session
     * 2.跳转到登录页
     */
    public function logout()
    {
        // 清空session
        session(null, config('venue.session_venue_scope'));

        // 跳转
        $this->redirect('Login/index');
    }
}

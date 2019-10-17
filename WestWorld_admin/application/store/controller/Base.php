<?php

namespace app\store\controller;

use think\Controller;

/**
 * store模块基础控制器类库
 * Class Base
 * @package app\store\controller
 */
class Base extends Controller
{
    /**
     * page 当前页
     * @var string
     */
    public $page = '';

    /**
     * size 每页条数
     * @var string
     */
    public $size = '';

    /**
     * 每页从第几条开始
     * @var int
     */
    public $from = 0;

    /**
     * 模块
     * @var string
     */
    public $module = 'store';

    /**
     * 当前登录店铺session值
     * @var mixed
     */
    public $session_store;

    /**
     * 初始化
     */
    public function _initialize()
    {
        // 初始化参数
        $this->module = request()->module();
        $this->session_store = session(config('store.session_store'), '', config('store.session_store_scope'));

        // 判断是否登录
        $isLogin = $this->isLogin();
        if (!$isLogin) {
            $this->redirect('Login/index');
        }
    }

    /**
     * 判断是否登录
     * @return bool
     */
    public function isLogin()
    {
        /*// 获取session
        $store_data = session(config('store.session_store'), '', config('store.session_store_scope'));*/
        if ($this->session_store && $this->session_store->store_id) {
            return true;
        }
        return false;
    }

    /**
     * 获取分页page、size、from
     * @param $params
     */
    public function getPageAndSize($params)
    {
        $this->page = !empty($params['page']) ? $params['page'] : 1;
        $this->size = !empty($params['size']) ? $params['size'] : config('paginate.list_rows');
        $this->from = ($this->page - 1) * $this->size; // 'limit from,size'
    }
}

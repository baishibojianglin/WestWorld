<?php

namespace app\admin\controller;

use app\common\lib\exception\ApiException;
use app\common\lib\IAuth;
use think\Controller;
use think\Request;

/**
 * 后台店铺管理控制器类
 * Class Store
 * @package app\admin\controller
 */
class Store extends Base
{
    /**
     * 数据表主键id字段
     * @var string
     */
    public $store_id = 'store_id';

    /**
     * 显示店铺资源列表
     * @return \think\response\Json
     */
    public function index()
    {
        // 判断为GET请求
        if (request()->isGet()) {
            return $this->fetch();
        }
    }

    /**
     * 获取店铺资源列表
     * @return \think\response\Json
     */
    public function getStore()
    {
        // 判断为GET请求
        if (request()->isGet()) {
            // 传入的参数
            $param = input('param.');
            $query = http_build_query($param); // 生成 URL-encode 之后的请求字符串 //halt($query);

            // 查询条件
            $map = [];
            if (!empty($param['grade_id'])) {
                $map['grade_id'] = input('param.grade_id', 0, 'intval'); //intval($param['grade_id'])
            }
            if (!empty($param['store_name'])) {
                $map['store_name'] = ['like', '%' . trim($param['store_name']) . '%'];
            }
            if (!empty($param['store_account'])) {
                $map['store_account'] = ['like', '%' . trim($param['store_account']) . '%'];
            }
            if (!empty($param['create_time'])) {
                $map['create_time'] = $param['create_time'];
            }

            /*// 获取分页列表数据 模式一：基于paginate()自动化分页
            $data = model('Store')->getStore();
            $status = config('code.status');
            foreach($data as $key => $value){
                $data[$key]['status_msg'] = $status[$value['status']];
            }
            return show(config('code.success'), 'ok', $data);*/

            // 获取分页列表数据 模式二：page当前页，size每页条数，from每页从第几条开始 => 'limit from,size'
            //$param['size'] = 1; // 定义每页条数
            $this->getPageAndSize($param); // 获取分页page、size
            // 获取列表数据
            $list = model('Store')->getStoreByCondition($map, $this->from, $this->size);
            // 获取列表数据总数
            $total = model('Store')->getStoreCountByCondition($map);
            // 总页数：结合 总数 / size 向上取整 => 有多少页
            $pages = ceil($total / $this->size); // 1.1 => 2

            // 返回处理后的json数据
            $status = config('code.status');
            foreach ($list as $key => $value) {
                $list[$key]['status_msg'] = $status[$value['status']];
            }
            $data = [
                'total' => $total,
                'pages' => $pages,
                'list' => $list,
            ];
            return show(config('code.success'), 'ok', $data); // http://test.battle.com/admin_store?page=2&size=1
            //return $this->fetch('', ['data' => $data]);
        }
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        // 判断为GET请求
        if (request()->isGet()) {
            return $this->fetch();
        }
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        // 判断为POST请求
        if(request()->isPost()){
            $data = input('post.');
            // 数据需要校验：参照validate机制
            // store_name唯一性,store_account自动生成且唯一性…

            // 入库操作
            try {
                $id = model('Store')->add($data, $this->store_id);
            } catch (\Exception $e) {
                return show(0, '新增失败 ' . $e->getMessage(), [], 400);
                //return $this->result('', 0, '新增失败');
            }
            if ($id) {
                return show(1, 'ok', ['jump_url' => url('Store/index')], 200);
                //return $this->result(['jump_url' => url('Store/index')], 1, 'ok');
            } else {
                return show(0, '新增失败', [], 400);
                //return $this->result('', 0, '新增失败');
            }
        }
    }

    /**
     * 显示指定的店铺资源
     * @param int $id
     * @return \think\response\Json
     * @throws ApiException
     */
    public function read($id)
    {
        // 判断为GET请求
        if (request()->isGet()) {
            try {
                $data = model('Store')->find($id);
            } catch (\Exception $e) {
                throw new ApiException($e->getMessage(), 500, config('code.error'));
            }

            if ($data) {
                // 处理数据
                // 定义status_msg
                $status = config('code.status');
                $data['status_msg'] = $status[$data['status']];

                // 最近登录时间
                $data['last_login_time'] = date('Y-m-d H:i:s', $data['last_login_time']);

                return show(config('code.success'), 'ok', $data);
            } else {
                return show(config('code.error'), 'Not Found', $data, 404);
            }
        }
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 保存更新的店铺资源
     * @param Request $request
     * @param int $id
     * @return \think\response\Json
     * @throws ApiException
     */
    public function update(Request $request, $id)
    {
        // 传入的数据
        $param = input('param.');

        // validate验证
        $validate = validate('Store');
        if (!$validate->check($param, [], 'update')) {
            return show(config('code.error'), $validate->getError(), [], 403);
        }

        // 判断数据是否存在
        $data = [];
        if (!empty($param['store_name'])) { // 店铺名称
            $data['store_name'] = trim($param['store_name']);
        }
        if (!empty($param['store_account'])) { // 店铺账号
            $data['store_account'] = trim($param['store_account']);
        }
        if (!empty($param['password'])) { // 密码
            $data['password'] = IAuth::encrypt($param['password']);
        }
        if (isset($param['grade_id'])) { // 店铺等级id
            $data['grade_id'] = input('param.grade_id', null, 'intval');
        }
        if (!empty($param['store_phone'])) { // 店铺电话
            $data['store_phone'] = trim($param['store_phone']);
        }
        if (!empty($param['address'])) { // 详细地址
            $data['address'] = trim($param['address']);
        }
        if (!empty($param['store_description'])) { // 店铺介绍
            $data['store_description'] = $param['store_description'];
        }
        if (!empty($param['store_manager'])) { // 店长
            $data['store_manager'] = trim($param['store_manager']);
        }
        if (!empty($param['manager_phone'])) { // 店长电话
            $data['manager_phone'] = trim($param['manager_phone']);
        }
        if (isset($param['status'])) { // 状态 //不能用 !empty() ，否则 status = 0 时也判断为空
            $data['status'] = input('param.status', null, 'intval');
        }

        if (empty($data)) {
            return show(config('code.error'), '数据不合法', [], 404);
        }

        // 更新
        try {
            $result = model('Store')->save($data, ['store_id' => $id]); // 更新
        } catch (\Exception $e) {
            throw new ApiException($e->getMessage(), 500, config('code.error'));
        }
        if ($result) {
            return show(config('code.success'), '更新成功', [], 201);
        } else {
            return show(config('code.error'), '更新失败', [], 403);
        }
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }
}

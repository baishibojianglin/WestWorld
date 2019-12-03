<?php

namespace app\admin\controller;

use app\common\lib\exception\ApiException;
use app\common\lib\IAuth;
use think\Controller;

/**
 * 后台用户管理控制器类
 * Class User
 * @package app\admin\controller
 */
class User extends Base
{
    /**
     * 数据表主键id字段
     * @var string
     */
    public $user_id = 'user_id';

    /**
     * 显示用户资源列表
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
     * 获取用户资源列表
     * @return \think\response\Json
     */
    public function getUser()
    {
        // 判断为GET请求
        if (request()->isGet()) {
            // 传入的数据
            $param = input('param.');
            $query = http_build_query($param); // 生成 URL-encode 之后的请求字符串 //halt($query);

            // 查询条件
            $map = [];
            if (!empty($param['user_name'])) {
                $map['user_name'] = ['like', '%' . $param['user_name'] . '%'];
            }
            if (!empty($param['phone'])) {
                $map['phone'] = ['like', '%' . $param['phone'] . '%'];
            }
            if (!empty($param['email'])) {
                $map['email'] = ['like', '%' . $param['email'] . '%'];
            }
            if (!empty($param['create_time'])) {
                $map['create_time'] = $param['create_time'];
            }
            if (isset($param['is_delete'])) { // 是否删除
                $map['is_delete'] = $param['is_delete'];
            }

            // 获取分页page、size
            $this->getPageAndSize($param);

            // 获取分页列表数据 模式一：基于paginate()自动化分页
            $data = model('User')->getUser($map, $this->size);
            $status = config('code.status');
            foreach ($data as $key => $value) {
                // 处理数据
                $data[$key]['status_msg'] = $status[$value['status']]; // 定义status_msg
            }

            return show(config('code.success'), 'ok', $data);
        }
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //
    }

    /**
     * 保存新建的用户资源
     * @return \think\response\Json
     * @throws ApiException
     */
    public function save()
    {
        // 判断为POST请求
        if (request()->isPost()) {
            // 传入的数据
            $data = input('post.');

            // validate验证
            $validate = validate('User');
            if (!$validate->check($data)) {
                return show(config('code.error'), $validate->getError(), [], 403);
            }

            // 处理数据
            $data['password'] = IAuth::encrypt($data['password']); // 密码加密
            $data['status'] = config('code.status_enable');

            // 新增
            // 捕获异常
            try {
                $id = model('User')->add($data, $this->user_id); // 新增
            } catch (\Exception $e) {
                throw new ApiException($e->getMessage(), 500, config('code.error'));
            }
            // 判断是否新增成功：获取id
            if ($id) {
                return show(config('code.success'), $this->user_id . ' = ' . $id . '的用户新增成功', [], 201);
            } else {
                return show(config('code.error'), '用户新增失败', [], 403);
            }
        }
    }

    /**
     * 显示指定的用户资源
     * @param int $id
     * @return \think\response\Json
     * @throws ApiException
     */
    public function read($id)
    {
        // 判断为GET请求
        if (request()->isGet()) {
            try {
                $data = model('User')->find($id);
            } catch (\Exception $e) {
                throw new ApiException($e->getMessage(), 500, config('code.error'));
            }

            if ($data) {
                // 定义status_msg
                $status = config('code.status');
                $data['status_msg'] = $status[$data['status']];

                return show(config('code.success'), 'ok', $data);
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
     * 保存更新的用户资源
     * @param int $id
     * @return \think\response\Json
     * @throws ApiException
     */
    public function update($id)
    {
        // 传入的数据
        $param = input('param.');

        // validate验证
        /**
         * tp5更新数据时，如何忽略唯一(unique)类型字段对自身数据的唯一性验证？
         * 方式1.编写验证场景，如：验证类common/validate/User编写 protected $scene = ['update' => ['phone.unique' => 'unique:user,phone^user_id'],]，控制器admin/controller/User编写 $validate->check($param, [], 'update')
         * 方式2.修改场景的规则不用重新写，重点是编辑界面一定要使用隐藏域传递主键id就可以了（<input type="hidden" name="表中id字段名" value="get方式传过来的id值">（千万注意name要和主键同名）），TP会自动识别是否需要判断唯一性
         */
        $validate = validate('User');
        if (!$validate->check($param, [], 'update')) { // update为验证场景
            return show(config('code.error'), $validate->getError(), [], 403);
        }

        // 判断数据是否存在
        $data = [];
        if (!empty($param['user_name'])) {
            $data['user_name'] = $param['user_name'];
        }
        if (!empty($param['password'])) {
            $data['password'] = IAuth::encrypt($param['password']);
        }
        if (!empty($param['phone'])) {
            $data['phone'] = $param['phone'];
        }
        if (!empty($param['email'])) {
            $data['email'] = $param['email'];
        }
        if (isset($param['status'])) { // 不能用 !empty() ，否则 status = 0 时也判断为空
            $data['status'] = input('param.status', null, 'intval');
        }

        if (empty($data)) {
            return show(config('code.error'), '数据不合法', [], 404);
        }

        // 更新
        try {
            $result = model('User')->save($data, ['user_id' => $id]); // 更新
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
     * 删除指定用户资源
     * @param int $id
     * @return \think\response\Json
     * @throws ApiException
     */
    public function delete($id)
    {
        // 判断为DELETE请求
        if (request()->isDelete()) {
            // 显示指定的用户
            try {
                $data = model('User')->find($id);
                //return show(config('code.success'), 'ok', $data);
            } catch (\Exception $e) {
                throw new ApiException($e->getMessage(), 500, config('code.error'));
            }

            // 判断数据是否存在
            if ($data['user_id'] != $id) {
                return show(config('code.error'), '数据不存在');
            }

            // 判断删除条件：用户是否启用
            if (config('code.status_enable') == $data['status']) {
                return show(config('code.error'), '删除失败：用户已启用', ['url' => 'deleteFalse']/*, 403*/);
            }

            // 软删除
            if ($data['is_delete'] != config('code.is_delete')) {
                // 捕获异常
                try {
                    $result = model('User')->softDelete($this->user_id, $id);
                } catch (\Exception $e) {
                    throw new ApiException($e->getMessage(), 500, config('code.error'));
                }

                if (!$result) {
                    return show(config('code.error'), '移除失败', ['url' => 'parent']/*, 403*/);
                } else {
                    return show(config('code.success'), '移除成功', ['url' => 'delete']);
                }
            }

            // 真删除
            if ($data['is_delete'] == config('code.is_delete')) {
                $result = model('User')->destroy($id);
                if (!$result) {
                    return show(config('code.error'), '删除失败', ['url' => 'parent']/*, 403*/);
                } else {
                    return show(config('code.success'), '删除成功', ['url' => 'delete']);
                }
            }
        } else {
            return show(config('code.error'), '请求不合法', [], 400);
        }
    }
}

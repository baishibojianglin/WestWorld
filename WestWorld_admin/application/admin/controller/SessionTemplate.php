<?php

namespace app\admin\controller;

use app\common\lib\exception\ApiException;
use think\Controller;

/**
 * 后台店鋪比赛场次模板控制器类
 * Class SessionTemplate
 * 注：URL大小写（https://www.kancloud.cn/manual/thinkphp5/118012，如控制器名BlogTest）：默认情况下，URL是不区分大小写的，如果要访问驼峰法的控制器类，则需要使用：http://serverName/index.php/admin/blog_test/read；如果希望URL访问严格区分大小写，可以在应用配置文件中设置：'url_convert' => false,
 * @package app\admin\controller
 */
class SessionTemplate extends Base
{
    /**
     * 数据表主键id字段
     * @var string
     */
    public $session_template_id = 'session_template_id';

    /**
     * 显示店鋪比赛场次模板资源列表
     * @return \think\response\Json
     */
    public function index()
    {
        // 判断为GET请求
        if (request()->isGet()) {
            // 传入的数据
            $param = input('param.');
            $query = http_build_query($param); // 生成 URL-encode 之后的请求字符串

            // 查询条件
            $map = [];
            if (!empty($param['venue_name'])) {
                $map['s.venue_name'] = ['like', '%' . $param['venue_name'] . '%'];
            }
            if (!empty($param['create_time'])) {
                $map['st.create_time'] = $param['create_time'];
            }

            // 获取分页page、size
            $this->getPageAndSize($param);

            // 获取分页列表数据 模式一：基于paginate()自动化分页
            $data = model('SessionTemplate')->getSessionTemplate($map, $this->size);

            // 处理数据
            $status = config('code.status');
            foreach ($data as $key => $value) {
                $data[$key]['status_msg'] = $status[$value['status']]; // 定义status_msg
                //$data[$key]['venue_info'] = model('Venue')->find($value['venue_id']); // 获取店鋪信息
            }

            return show(config('code.success'), 'OK', $data);
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
     * 保存新建的店鋪比赛场次模板资源
     * @return \think\response\Json
     * @throws ApiException
     */
    public function save()
    {
        // 判断为POST请求
        if (request()->isPost()) {
            // 传入的数据
            $data = input('post.');

            // validate
            $validate = validate('SessionTemplate');
            if (!$validate->check($data)) {
                return show(config('code.error'), $validate->getError(), [], 403);
            }

            // 处理数据
            $data['session_template'] = [ // 测试数据
                ['template_id'=> 0, 'name' => 'anan', 'time' => 1233221],
                ['template_id'=> 1, 'name' => 'anan2', 'time' => 21233221]
            ];
            $data['session_template'] = json_encode($data['session_template']);
            $data['status'] = config('code.status_enable');

            // 新增
            // 捕获异常
            try {
                $id = model('SessionTemplate')->add($data, $this->session_template_id); // 新增
            } catch (\Exception $e) {
                throw new ApiException($e->getMessage(), 500, config('code.error'));
            }
            // 判断是否新增成功：获取id
            if ($id) {
                return show(config('code.success'), $this->session_template_id . ' = ' . $id . '的模板新增成功', [], 201);
            } else {
                return show(config('code.error'), '模板新增失败', [], 403);
            }
        }
    }

    /**
     * 显示指定的店鋪比赛场次模板资源
     * @param int $id
     * @return \think\response\Json
     * @throws ApiException
     */
    public function read($id)
    {
        // 判断为GET请求
        if (request()->isGet()) {
            try {
                $data = model('SessionTemplate')->getSessionTemplateById([], $id);
            } catch (\Exception $e) {
                throw new ApiException($e->getMessage(), 500, config('code.error'));
            }

            if ($data) {
                // 处理数据
                // 定义status_msg
                $status = config('code.status');
                $data['status_msg'] = $status[$data['status']];
                // 将session_template的json数据转数组
                $data['session_template'] = json_decode($data['session_template']);

                return show(config('code.success'), 'OK', $data);
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
     * 保存更新的店鋪比赛场次模板资源
     * @param int $id
     * @return \think\response\Json
     * @throws ApiException
     */
    public function update($id)
    {
        // 判断为PUT请求
        if (request()->isPut()) {
            // 传入的数据
            $param = input('param.');

            // validate验证
            $validate = validate('SessionTemplate');
            if (!$validate->check($param, [], 'update')) {
                return show(config('code.error'), $validate->getError(), [], 403);
            }

            // 判断数据是否存在
            $data = [];
            if (!empty($param['session_template'])) {
                $data['session_template'] = json_encode($param['session_template']);
            }
            if (isset($param['status'])) { // 不能用 !empty() ，否则 status = 0 时也判断为空
                $data['status'] = input('param.status', null, 'intval');
            }

            if (empty($data)) {
                return show(config('code.error'), '数据不合法', [], 404);
            }

            // 更新
            try {
                $result = model('SessionTemplate')->save($data, ['session_template_id' => $id]); // 更新
            } catch (\Exception $e) {
                throw new ApiException($e->getMessage(), 500, config('code.error'));
            }
            if ($result) {
                return show(config('code.success'), '更新成功', [], 201);
            } else {
                return show(config('code.error'), '更新失败', [], 403);
            }
        }
    }

    /**
     * 删除指定店鋪比赛场次模板资源
     * @param int $id
     * @return \think\response\Json
     * @throws ApiException
     */
    public function delete($id)
    {
        // 显示指定的店鋪比赛场次模板
        try {
            $data = model('SessionTemplate')->find($id);
            //return show(config('code.success'), 'OK', $data);
        } catch (\Exception $e) {
            throw new ApiException($e->getMessage(), 500, config('code.error'));
        }

        // 判断数据是否存在
        if ($data['session_template_id'] != $id) {
            return show(config('code.error'), '数据不存在');
        }

        // 软删除
        if ($data['is_delete'] != config('code.is_delete')) {
            // 捕获异常
            try {
                $result = model('SessionTemplate')->softDelete($this->session_template_id, $id);
            } catch (\Exception $e) {
                throw new ApiException($e->getMessage(), 500, config('code.error'));
            }

            if (!$result) {
                return show(config('code.error'), '软删除失败');
            } else {
                return show(config('code.success'), '软删除成功');
            }
        }

        // 真删除
        if ($data['is_delete'] == config('code.is_delete')) {
            $result = model('SessionTemplate')->destroy($id);
            if (!$result) {
                return show(config('code.error'), '删除失败');
            } else {
                return show(config('code.success'), '删除成功');
            }
        }
    }
}

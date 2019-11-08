<?php

namespace app\admin\controller;

use app\common\lib\exception\ApiException;
use app\common\lib\IAuth;
use think\Controller;
use think\Request;

/**
 * 后台场馆管理控制器类
 * Class Venue
 * @package app\admin\controller
 */
class Venue extends Base
{
    /**
     * 数据表主键id字段
     * @var string
     */
    public $venue_id = 'venue_id';

    /**
     * 显示场馆资源列表
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
     * 获取场馆资源列表
     * @return \think\response\Json
     */
    public function getVenue()
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
            if (!empty($param['venue_name'])) {
                $map['venue_name'] = ['like', '%' . trim($param['venue_name']) . '%'];
            }
            if (!empty($param['venue_account'])) {
                $map['venue_account'] = ['like', '%' . trim($param['venue_account']) . '%'];
            }
            if (!empty($param['create_time'])) {
                $map['create_time'] = $param['create_time'];
            }

            /*// 获取分页列表数据 模式一：基于paginate()自动化分页
            $data = model('Venue')->getVenue();
            $status = config('code.status');
            foreach($data as $key => $value){
                $data[$key]['status_msg'] = $status[$value['status']];
            }
            return show(config('code.success'), 'ok', $data);*/

            // 获取分页列表数据 模式二：page当前页，size每页条数，from每页从第几条开始 => 'limit from,size'
            //$param['size'] = 1; // 定义每页条数
            $this->getPageAndSize($param); // 获取分页page、size
            // 获取列表数据
            $list = model('Venue')->getVenueByCondition($map, $this->from, $this->size);
            // 获取列表数据总数
            $total = model('Venue')->getVenueCountByCondition($map);
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
            return show(config('code.success'), 'ok', $data); // http://test.battle.com/admin_venue?page=2&size=1
            //return $this->fetch('', ['data' => $data]);
        }
    }

    /**
     * 获取场馆资源列表（静态方法）
     * @return false|\PDOStatement|string|\think\Collection
     */
    public static function venueList()
    {
        $data = db('venue')->field('venue_id, venue_name')->where('status', config('code.status_enable'))->select();
        return $data;
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
            // venue_name唯一性,venue_account自动生成且唯一性…

            // 处理数据
            if (!empty($data['password'])) { // 密码
                $data['password'] = IAuth::encrypt($data['password']);
            }

            // 入库操作
            try {
                $id = model('Venue')->add($data, $this->venue_id);
            } catch (\Exception $e) {
                return show(0, '新增失败 ' . $e->getMessage(), [], 400);
                //return $this->result('', 0, '新增失败');
            }
            if ($id) {
                return show(config('code.success'), '新增成功', ['url' => url('Venue/index')], 201);
                //return show(config('code.success'), '新增成功', ['url' => config('app.I_SERVER_NAME') . $this->module . '/Venue/index'], 201);
                //return $this->result(['jump_url' => url('Venue/index')], 1, 'ok');
            } else {
                return show(0, '新增失败', [], 400);
                //return $this->result('', 0, '新增失败');
            }
        }
    }

    /**
     * 显示指定的场馆资源
     * @param int $id
     * @return \think\response\Json
     * @throws ApiException
     */
    public function read($id)
    {
        // 判断为GET请求
        if (request()->isGet()) {
            try {
                $data = model('Venue')->find($id);
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
     * @param int $id
     * @return \think\Response
     */
    public function edit($id)
    {
        // 判断为GET请求
        if (request()->isGet()) {
            return $this->fetch();
        }
    }

    /**
     * 保存更新的场馆资源
     * @param Request $request
     * @param int $id
     * @return \think\response\Json
     * @throws ApiException
     */
    public function update(Request $request, $id)
    {
        // 判断为PUT请求
        if (request()->isPut()) {
            // 传入的数据
            $param = input('param.');

            // 获取更新成功前的场馆缩略图thumb
            $venue = model('Venue')->field('thumb')->find($id);

            // validate验证
            $validate = validate('Venue');
            if (!$validate->check($param, [], 'update')) {
                return show(config('code.error'), $validate->getError(), [], 403);
            }

            // 判断数据是否存在
            $data = [];
            if (!empty($param['venue_name'])) { // 场馆名称
                $data['venue_name'] = trim($param['venue_name']);
            }
            if (!empty($param['venue_account'])) { // 场馆账号
                $data['venue_account'] = trim($param['venue_account']);
            }
            if (!empty($param['password'])) { // 密码
                $data['password'] = IAuth::encrypt($param['password']);
            }
            if (isset($param['grade_id'])) { // 场馆等级id
                $data['grade_id'] = input('param.grade_id', null, 'intval');
            }
            if (!empty($param['venue_phone'])) { // 场馆电话
                $data['venue_phone'] = trim($param['venue_phone']);
            }
            if (!empty($param['address'])) { // 详细地址
                $data['address'] = trim($param['address']);
            }
            if (!empty($param['longitude'])) { // 场馆经度
                $data['longitude'] = trim($param['longitude']);
            }
            if (!empty($param['latitude'])) { // 场馆纬度
                $data['latitude'] = trim($param['latitude']);
            }
            if (!empty($param['thumb'])) { // 场馆缩略图
                $data['thumb'] = trim($param['thumb']);
            }
            if (!empty($param['venue_description'])) { // 场馆介绍
                $data['venue_description'] = $param['venue_description'];
            }
            if (!empty($param['venue_manager'])) { // 店长
                $data['venue_manager'] = trim($param['venue_manager']);
            }
            if (!empty($param['manager_phone'])) { // 店长电话
                $data['manager_phone'] = trim($param['manager_phone']);
            }
            if (isset($param['status'])) { // 状态 //不能用 !empty() ，否则 status = 0 时也判断为空
                $data['status'] = input('param.status', null, 'intval');

                if (0 == $param['status'] && isset($param['venue_close_info'])) { // 场馆关闭原因
                    $data['venue_close_info'] = trim($param['venue_close_info']);
                }
                if (0 != $param['status']) {
                    $data['venue_close_info'] = '';
                }
            }

            if (empty($data)) {
                return show(config('code.error'), '数据不合法', [], 404);
            }

            // 更新
            try {
                $result = model('Venue')->save($data, ['venue_id' => $id]); // 更新
            } catch (\Exception $e) {
                throw new ApiException($e->getMessage(), 500, config('code.error'));
            }
            if (false === $result) {
                return show(config('code.error'), '更新失败', [], 403);
            } else {

                // 当为更新状态时，直接刷新当前页面
                if (array_key_exists('status', $param) && count($param) == 2) { // 传入2个参数，其中一个是 status
                    return $this->redirect('venue/index');
                }

                // 删除更新成功前的文章缩略图thumb文件
                if (!empty($param['thumb']) && trim($param['thumb']) != $venue['thumb']) {
                    // 删除文件
                    @unlink(ROOT_PATH . 'public' . DS . $venue['thumb']);
                }

                return show(config('code.success'), '更新成功', ['url' => 'parent'], 201);
            }
        } else {
            return show(config('code.error'), '请求不合法', [], 400);
        }
    }

    /**
     * 删除指定场馆资源
     *
     * @param int $id
     * @return \think\Response
     * @throws ApiException
     */
    public function delete($id)
    {
        // 判断为DELETE请求
        if (request()->isDelete()) {
            // 获取指定的场馆
            try {
                $data = model('Venue')->find($id);
                //return show(config('code.success'), 'ok', $data);
            } catch (\Exception $e) {
                throw new ApiException($e->getMessage(), 500, config('code.error'));
            }

            // 判断数据是否存在
            if ($data['venue_id'] != $id) {
                return show(config('code.error'), '数据不存在');
            }

            // 判断删除条件：场馆状态
            if (in_array($data['status'], [1, 2])) {
                return show(config('code.error'), '删除失败：场馆待审核、或已启用', ['url' => 'deleteFalse']);
            }

            // 软删除
            if ($data['is_delete'] != config('code.is_delete')) {
                // 捕获异常
                try {
                    $result = model('Venue')->softDelete('venue_id', $id);
                } catch (\Exception $e) {
                    throw new ApiException($e->getMessage(), 500, config('code.error'));
                }

                if (!$result) {
                    return show(config('code.error'), '移除失败', ['url' => 'parent']);
                } else {
                    return show(config('code.success'), '移除成功', ['url' => 'delete']);
                }
            }

            // 真删除
            if ($data['is_delete'] == config('code.is_delete')) {
                $result = model('Venue')->destroy($id);
                if (!$result) {
                    return show(config('code.error'), '删除失败', ['url' => 'parent']);
                } else {
                    // 删除文件
                    @unlink(ROOT_PATH . 'public' . DS . $data['thumb']);

                    return show(config('code.success'), '删除成功', ['url' => 'delete']);
                }
            }
        } else {
            return show(config('code.error'), '请求不合法', [], 400);
        }
    }
}

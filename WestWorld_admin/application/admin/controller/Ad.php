<?php

namespace app\admin\controller;

use app\common\lib\exception\ApiException;
use think\Controller;
use think\Request;

/**
 * 后台广告管理控制器类
 * Class Ad
 * @package app\admin\controller
 */
class Ad extends Base
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        return $this->fetch('', ['adPositionList' => AdPosition::adPositionList()]);
    }

    /**
     * 获取广告资源列表
     * @return \think\response\Json
     */
    public function getAd()
    {
        // 判断为GET请求
        if (request()->isGet()) {
            // 传入的参数
            $param = input('param.');
            $query = http_build_query($param); // 生成 URL-encode 之后的请求字符串 //halt($query);

            // 查询条件
            $map = [];
            $map['ap.status'] = config('code.status_enable'); // 广告位置状态
            if (!empty($param['ad_name'])) { // 广告名称
                $map['a.ad_name'] = ['like', '%' . trim($param['ad_name']) . '%'];
            }
            if (!empty($param['position_id'])) { // 广告位置ID
                $map['a.position_id'] = intval($param['position_id']);
            }
            if (isset($param['is_delete'])) { // 是否删除
                $map['a.is_delete'] = $param['is_delete'];
            }

            // 获取分页page、size
            $this->getPageAndSize($param);

            // 获取分页列表数据 模式一：基于paginate()自动化分页
            $data = model('Ad')->getAd($map, $this->size);

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
        return $this->fetch('', ['adPositionList' => AdPosition::adPositionList()]);
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request $request
     * @return \think\Response
     * @throws ApiException
     */
    public function save(Request $request)
    {
        // 判断为POST请求
        if(request()->isPost()){
            $data = input('post.');

            // 判断数据合法性
            // 判断广告时间
            if ($data['start_time'] < date('Y-m-d')) {
                return show(0, '广告开始时间不能小于当前时间', [], 403);
            }
            if ($data['start_time'] > $data['end_time']) {
                return show(0, '广告结束时间不能小于开始时间', [], 403);
            }

            // 处理数据
            $data['start_time'] = strtotime($data['start_time']);
            $data['end_time'] = strtotime($data['end_time']);

            // 入库操作
            try {
                $id = model('Ad')->add($data, 'ad_id');
            } catch (\Exception $e) {
                throw new ApiException($e->getMessage(), 500, config('code.error'));
            }
            if ($id) {
                return show(config('code.success'), '新增成功', ['url' => url('Ad/index')], 201);
            } else {
                return show(0, '新增失败', [], 403);
            }
        }
    }

    /**
     * 显示指定的资源
     *
     * @param  int $id
     * @return \think\Response
     * @throws ApiException
     */
    public function read($id)
    {
        // 判断为GET请求
        if (request()->isGet()) {
            try {
                $data = model('Ad')->find($id);
            } catch (\Exception $e) {
                throw new ApiException($e->getMessage(), 500, config('code.error'));
            }

            if ($data) {
                // 处理数据
                $data['start_time'] = date('Y-m-d', $data['start_time']);
                $data['end_time'] = date('Y-m-d', $data['end_time']);

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
        return view('', ['adPositionList' => AdPosition::adPositionList()]);
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request $request
     * @param  int $id
     * @return \think\Response
     * @throws ApiException
     */
    public function update(Request $request, $id)
    {
        // 判断为PUT请求
        if (request()->isPut()) {
            // 传入的数据
            $param = input('param.');

            // 获取更新成功前的广告图片ad_pic
            $ad = model('Ad')->field('ad_pic')->find($id);

            // 判断数据是否存在
            $data = [];
            if (!empty($param['ad_name'])) { // 广告名称
                $data['ad_name'] = trim($param['ad_name']);
            }
            if (isset($param['position_id'])) { // 广告位置ID
                $data['position_id'] = input('param.position_id', null, 'intval');
            }
            if (!empty($param['start_time'])) { // 开始时间
                $data['start_time'] = strtotime($param['start_time']);
            }
            if (!empty($param['end_time'])) { // 结束时间
                if (!empty($param['start_time']) && $param['start_time'] > $param['end_time']) {
                    return show(0, '广告结束时间不能小于开始时间', [], 403);
                }

                $data['end_time'] = strtotime($param['end_time']);
            }
            if (!empty($param['ad_link'])) { // 广告链接
                $data['ad_link'] = trim($param['ad_link']);
            }
            if (!empty($param['ad_pic'])) { // 广告图片
                $data['ad_pic'] = trim($param['ad_pic']);
            }
            if (!empty($param['bgcolor'])) { // 背景颜色
                $data['bgcolor'] = $param['bgcolor'];
            }
            if (isset($param['target'])) { // 新窗口
                $data['target'] = input('param.target', null, 'intval');
            }
            if (isset($param['is_show'])) { // 是否显示
                $data['is_show'] = input('param.is_show', null, 'intval');
            }
            if (isset($param['sort'])) { // 排序
                $data['sort'] = input('param.sort', null, 'intval');
            }

            if (empty($data)) {
                return show(config('code.error'), '数据不合法', [], 404);
            }

            // 更新
            try {
                $result = model('Ad')->save($data, ['ad_id' => $id]); // 更新
            } catch (\Exception $e) {
                throw new ApiException($e->getMessage(), 500, config('code.error'));
            }
            if (false === $result) {
                return show(config('code.error'), '更新失败', [], 403);
            } else {

                // 当为更新target或is_show时，直接刷新当前页面
                if ((array_key_exists('target', $param) || array_key_exists('is_show', $param)) && count($param) == 2) { // 传入2个参数，其中一个是 target或is_show
                    return $this->redirect('ad/index');
                }

                // 删除更新成功前的广告图片ad_pic文件
                if (!empty($param['ad_pic']) && trim($param['ad_pic']) != $ad['ad_pic']) {
                    // 删除文件
                    @unlink(ROOT_PATH . 'public' . DS . $ad['ad_pic']);
                }

                return show(config('code.success'), '更新成功', ['url' => 'parent'], 201);
            }
        } else {
            return show(config('code.error'), '请求不合法', [], 400);
        }
    }

    /**
     * 删除指定资源
     *
     * @param  int $id
     * @return \think\Response
     * @throws ApiException
     */
    public function delete($id)
    {
        // 判断为DELETE请求
        if (request()->isDelete()) {
            // 获取指定的广告
            try {
                $data = model('Ad')->find($id);
                //return show(config('code.success'), 'ok', $data);
            } catch (\Exception $e) {
                throw new ApiException($e->getMessage(), 500, config('code.error'));
            }

            // 判断数据是否存在
            if ($data['ad_id'] != $id) {
                return show(config('code.error'), '数据不存在');
            }

            // 判断删除条件：广告是否显示
            if (1 == $data['is_show']) {
                return show(config('code.error'), '删除失败：广告已显示', ['url' => 'deleteFalse']/*, 403*/);
            }

            // 软删除
            if ($data['is_delete'] != config('code.is_delete')) {
                // 捕获异常
                try {
                    $result = model('Ad')->softDelete('ad_id', $id);
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
                $result = model('Ad')->destroy($id);
                if (!$result) {
                    return show(config('code.error'), '删除失败', ['url' => 'parent']/*, 403*/);
                } else {
                    // 删除文件
                    @unlink(ROOT_PATH . 'public' . DS . $data['ad_pic']);

                    return show(config('code.success'), '删除成功', ['url' => 'delete']);
                }
            }
        } else {
            return show(config('code.error'), '请求不合法', [], 400);
        }
    }
}

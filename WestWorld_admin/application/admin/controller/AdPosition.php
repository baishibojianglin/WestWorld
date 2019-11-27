<?php

namespace app\admin\controller;

use app\common\lib\exception\ApiException;
use think\Controller;
use think\Request;

/**
 * 后台广告位置管理控制器类
 * Class AdPosition
 * @package app\admin\controller
 */
class AdPosition extends Base
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        return $this->fetch();
    }

    /**
     * 获取广告位置资源列表
     * @return \think\response\Json
     */
    public function getAdPosition()
    {
        // 判断为GET请求
        if (request()->isGet()) {
            // 传入的参数
            $param = input('param.');
            $query = http_build_query($param); // 生成 URL-encode 之后的请求字符串 //halt($query);

            // 查询条件
            $map = [];
            if (!empty($param['position_name'])) { // 广告位名称
                $map['position_name'] = ['like', '%' . trim($param['position_name']) . '%'];
            }

            // 获取分页page、size
            $this->getPageAndSize($param);

            // 获取分页列表数据 模式一：基于paginate()自动化分页
            $data = model('AdPosition')->getAdPosition($map, $this->size);
            $status = config('code.status');
            foreach ($data as $key => $value) {
                $data[$key]['status_msg'] = $status[$value['status']];
            }

            return show(config('code.success'), 'ok', $data);
        }
    }

    /**
     * 获取广告位置资源列表（静态方法）
     * @return false|\PDOStatement|string|\think\Collection
     */
    public static function adPositionList()
    {
        $map['status'] = config('code.status_enable');
        $data = db('ad_position')->field('position_id, position_name')->where($map)->select();
        return $data;
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        return $this->fetch();
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

            // 入库操作
            try {
                $id = model('AdPosition')->add($data, 'position_id');
            } catch (\Exception $e) {
                throw new ApiException($e->getMessage(), 500, config('code.error'));
            }
            if ($id) {
                return show(config('code.success'), '新增成功', ['url' => url('AdPosition/index')], 201);
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
                $data = model('AdPosition')->find($id);
            } catch (\Exception $e) {
                throw new ApiException($e->getMessage(), 500, config('code.error'));
            }

            if ($data) {
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
        return view();
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

            // 判断数据是否存在
            $data = [];
            if (!empty($param['position_name'])) { // 广告位名称
                $data['position_name'] = trim($param['position_name']);
            }
            if (!empty($param['ad_width'])) { // 广告位宽度
                $data['ad_width'] = trim($param['ad_width']);
            }
            if (!empty($param['ad_height'])) { // 广告位高度
                $data['ad_height'] = trim($param['ad_height']);
            }
            if (!empty($param['position_desc'])) { // 广告位描述
                $data['position_desc'] = trim($param['position_desc']);
            }
            if (isset($param['status'])) { // 状态
                // 获取广告位对应广告列表
                $adList = model('Ad')->where(['position_id' => $id, 'is_show' => 1, 'is_delete' => config('code.not_delete')])->select();
                if (config('code.status_disable') == $param['status'] && !empty($adList)) {
                    return show(config('code.success'), '广告列表不为空，禁止关闭状态', [], 403);
                }

                $data['status'] = input('param.status', null, 'intval');
            }

            if (empty($data)) {
                return show(config('code.error'), '数据不合法', [], 404);
            }

            // 更新
            try {
                $result = model('AdPosition')->save($data, ['position_id' => $id]); // 更新
            } catch (\Exception $e) {
                throw new ApiException($e->getMessage(), 500, config('code.error'));
            }
            if (false === $result) {
                return show(config('code.error'), '更新失败', [], 403);
            } else {

                // 当只更新状态status时，直接刷新当前页面
                if (array_key_exists('status', $param) && count($param) == 2) { // 传入2个参数，其中一个是 status
                    return $this->redirect('adPosition/index');
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
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        // 判断为DELETE请求
        if (request()->isDelete()) {
            // 获取指定的广告位
            try {
                $data = model('AdPosition')->find($id);
                //return show(config('code.success'), 'ok', $data);
            } catch (\Exception $e) {
                throw new ApiException($e->getMessage(), 500, config('code.error'));
            }

            // 判断数据是否存在
            if ($data['position_id'] != $id) {
                return show(config('code.error'), '数据不存在');
            }

            // 判断删除条件
            // 广告位状态
            if (config('code.status_enable') == $data['status']) {
                return show(config('code.error'), '删除失败：广告位已开启', ['url' => 'deleteFalse']/*, 403*/);
            }
            // 判断广告位对应广告列表是否为空
            $adList = model('Ad')->where(['position_id' => $id])->select();
            if (!empty($adList)) {
                return show(config('code.error'), '删除失败：广告列表不为空', ['url' => 'deleteFalse']/*, 403*/);
            }

            // 真删除
            if (config('code.status_disable') == $data['status']) {
                $result = model('AdPosition')->destroy($id);
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

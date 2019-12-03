<?php

namespace app\admin\controller;

use app\common\lib\exception\ApiException;
use think\Controller;
use think\Request;

/**
 * 后台装备管理控制器类
 * Class Equipment
 * @package app\admin\controller
 */
class Equipment extends Base
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        // 判断为GET请求
        if (request()->isGet()) {
            return $this->fetch('', ['venueList' => Venue::venueList(), 'sceneList' => Scene::sceneList()]);
        }
    }

    /**
     * 获取装备资源列表
     * @return \think\response\Json
     */
    public function getEquipment()
    {
        // 判断为GET请求
        if (request()->isGet()) {
            // 传入的参数
            $param = input('param.');
            $query = http_build_query($param); // 生成 URL-encode 之后的请求字符串 //halt($query);

            // 查询条件
            $map = [];
            if (!empty($param['equipment_name'])) {
                $map['e.equipment_name'] = ['like', '%' . trim($param['equipment_name']) . '%'];
            }
            if (!empty($param['venue_id'])) { // 场馆ID
                $map['e.venue_id'] = intval($param['venue_id']);
            }
            if (!empty($param['scene_id'])) { // 场景ID
                $map['e.scene_id'] = intval($param['scene_id']);
            }
            if (isset($param['equipment_type']) && ($param['equipment_type'] != null)) { // 装备类型
                $map['e.equipment_type'] = intval($param['equipment_type']);
            }

            // 获取分页page、size
            $this->getPageAndSize($param);

            // 获取分页列表数据 模式一：基于paginate()自动化分页
            $data = model('Equipment')->getEquipment($map, $this->size);

            return show(config('code.success'), 'ok', $data);
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
                $data = model('Equipment')->alias('e')
                    ->field('e.*, v.venue_name, sc.scene_name')
                    ->join([
                        ['__VENUE__ v', 'e.venue_id = v.venue_id', 'LEFT'],
                        ['__SCENE__ sc', 'e.scene_id = sc.scene_id', 'LEFT']
                    ])
                    ->find($id);
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
     * 显示资源详情页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function detail($id)
    {
        // 判断为GET请求
        if (request()->isGet()) {
            return view();
        }
    }
}

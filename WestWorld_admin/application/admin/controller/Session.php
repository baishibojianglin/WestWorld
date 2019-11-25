<?php

namespace app\admin\controller;

use app\common\lib\exception\ApiException;
use think\Controller;
use think\Request;

/**
 * 后台场次管理控制器类
 * Class Session
 * @package app\admin\controller
 */
class Session extends Base
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
     * 获取场次资源列表
     * @return \think\response\Json
     */
    public function getSession()
    {
        // 判断为GET请求
        if (request()->isGet()) {
            // 传入的参数
            $param = input('param.');
            $query = http_build_query($param); // 生成 URL-encode 之后的请求字符串 //halt($query);

            // 查询条件
            $map = [];
            if (!empty($param['venue_id'])) { // 场馆ID
                $map['se.venue_id'] = intval($param['venue_id']);
            }
            if (!empty($param['scene_id'])) { // 场景ID
                $map['se.scene_id'] = intval($param['scene_id']);
            }

            // 获取分页page、size
            $this->getPageAndSize($param);

            // 获取分页列表数据 模式一：基于paginate()自动化分页
            $data = model('Session')->getSession($map, $this->size);
            $status = config('code.status');
            foreach($data as $key => $value){
                $data[$key]['status_msg'] = $status[$value['status']];
            }

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
                $data = model('Session')->alias('se')
                    ->field('se.*, v.venue_name, sc.scene_name')
                    ->join([
                        ['__VENUE__ v', 'se.venue_id = v.venue_id', 'LEFT'],
                        ['__SCENE__ sc', 'se.scene_id = sc.scene_id', 'LEFT']
                    ])
                    ->find($id);
            } catch (\Exception $e) {
                throw new ApiException($e->getMessage(), 500, config('code.error'));
            }

            if ($data) {
                // 处理数据
                // 定义status_msg
                $status = config('code.status');
                $data['status_msg'] = $status[$data['status']];

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

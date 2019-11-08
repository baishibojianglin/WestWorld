<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;

/**
 * 后台场景管理控制器类
 * Class Scene
 * @package app\admin\controller
 */
class Scene extends Base
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
            return $this->fetch('', ['venueList' => Venue::venueList()]);
        }
    }

    /**
     * 获取场景资源列表
     * @return \think\response\Json
     */
    public function getScene()
    {
        // 判断为GET请求
        if (request()->isGet()) {
            // 传入的参数
            $param = input('param.');
            $query = http_build_query($param); // 生成 URL-encode 之后的请求字符串 //halt($query);

            // 查询条件
            $map = [];
            if (!empty($param['scene_name'])) {
                $map['s.scene_name'] = ['like', '%' . trim($param['scene_name']) . '%'];
            }
            if (!empty($param['venue_id'])) {
                $map['s.venue_id'] = input('param.venue_id', 0, 'intval'); //intval($param['venue_id'])
            }

            // 获取分页page、size
            $this->getPageAndSize($param);

            // 获取分页列表数据 模式一：基于paginate()自动化分页
            $data = model('Scene')->getScene($map, $this->size);
            $status = config('code.status');
            foreach($data as $key => $value){
                $data[$key]['status_msg'] = $status[$value['status']];
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
        // 判断为GET请求
        if (request()->isGet()) {
            return view('', ['venueList' => Venue::venueList()]);
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

            // 入库操作
            try {
                $id = model('Scene')->add($data, 'scene_id');
            } catch (\Exception $e) {
                return show(0, '新增失败 ' . $e->getMessage(), [], 400);
            }
            if ($id) {
                return show(config('code.success'), '新增成功', ['url' => url('Scene/index')], 201);
            } else {
                return show(0, '新增失败', [], 400);
            }
        }
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
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
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //
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

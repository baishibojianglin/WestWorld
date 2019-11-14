<?php

namespace app\venue\controller;

use app\common\lib\exception\ApiException;

/**
 * venue模块比赛场次组队管理控制器类
 * Class SessionTeams
 * @package app\venue\controller
 */
class SessionTeams extends Base
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
            return $this->fetch('', [
                'sceneList' => Scene::sceneList(), // 场景列表
                'sceneRoomList' => SceneRoom::sceneRoomList() // 房间列表
            ]);
        }
    }

    /**
     * 获取比赛场次组队资源列表
     * @return \think\response\Json
     */
    public function getSessionTeams()
    {
        // 判断为GET请求
        if (request()->isGet()) {
            // 传入的参数
            $param = input('param.');
            $query = http_build_query($param); // 生成 URL-encode 之后的请求字符串 //halt($query);

            // 查询条件
            $map = [];
            $map['st.venue_id'] = $this->session_venue->venue_id; // 场馆ID
            if (!empty($param['scene_id'])) { // 场景
                $map['st.scene_id'] = intval($param['scene_id']);
            }
            if (!empty($param['room_id'])) { // 房间
                $map['st.room_id'] = intval($param['room_id']);
            }
            if (isset($param['session_date'])) { // 比赛场次日期
                $map['st.session_date'] = $param['session_date'];
            }

            // 获取分页page、size
            $this->getPageAndSize($param);

            // 获取分页列表数据 模式一：基于paginate()自动化分页
            $data = model('SessionTeams')->getSessionTeams($map, $this->size);

            return show(config('code.success'), 'ok', $data);
        }
    }

    /**
     * 显示指定的比赛场次组队资源
     * @param int $id
     * @return \think\response\Json
     * @throws ApiException
     */
    public function read($id)
    {
        // 判断为GET请求
        if (request()->isGet()) {
            try {
                $data = model('SessionTeams')->getSessionTeamsDetail($id);
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
     * 显示比赛场次组队资源详情页
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function detail($id)
    {
        // 判断为GET请求
        if (request()->isGet()) {
            return $this->fetch();
        }
    }
}
<?php

namespace app\api\controller\v1;

use app\api\controller\Common;
use app\common\lib\exception\ApiException;
use think\Controller;
use think\Request;

/**
 * api模块客户端比赛场次组队控制器类
 * Class SessionTeams
 * @package app\admin\controller
 */
class SessionTeams extends Common
{
    /**
     * 获取比赛场次组队资源列表
     * @return \think\response\Json
     */
    public function index()
    {
        // 判断为GET请求
        if (request()->isGet()) {
            // 传入的参数
            $param = input('param.');

            // 查询条件
            $map = [];
            if (!empty($param['venue_id'])) { // 场馆ID
                $map['venue_id'] = intval($param['venue_id']);
            }
            if (!empty($param['scene_id'])) { // 场景ID
                $map['scene_id'] = intval($param['scene_id']);
            }
            if (!empty($param['session_date'])) { // 比赛场次日期
                $map['session_date'] = trim($param['session_date']);
            }
            if (!empty($param['session_id'])) { // 场次ID
                $map['session_id'] = intval($param['session_id']);
            }
            if (!empty($param['room_id'])) { // 房间ID
                $map['room_id'] = intval($param['room_id']);
            }

            // 获取分页page、size
            //$this->getPageAndSize($param);

            // 获取分页列表数据 模式一：基于paginate()自动化分页
            //$data = model('SessionTeams')->getSessionTeams($map, $this->size);
            $data = model('SessionTeams')->where($map)->find();

            return show(config('code.success'), 'ok', $data);
        }
    }

    /**
     * 保存新建的资源
     *
     * 创建新的比赛团队，存在两种情况：
     * 1.（通过 input('post.') 查询）数据库已存在该场比赛，进行更新操作；
     * 2.数据库不存在该场比赛，进行新增操作。
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        // 判断为POST请求
        if(request()->isPost()){
            // 传入的参数
            $data = input('post.');

            // validate验证
            $validate = validate('SessionTeams');
            if (!$validate->check($data)) {
                return show(config('code.error'), $validate->getError(), [], 403);
            }

            // 验证不需要validate验证的其他参数
            if (empty($data['user_id'])) { // 用户ID
                return show(config('code.error'), '用户不能为空', [], 403);
            }
            if (empty($data['number'])) { // 人数
                return show(config('code.error'), '人数不能为空', [], 403);
            }

            // 查询条件（上述已经判断数据存在）
            $map = [];
            $map['venue_id'] = intval($data['venue_id']); // 场馆ID
            $map['scene_id'] = intval($data['scene_id']); // 场景ID
            $map['session_date'] = trim($data['session_date']); // 比赛场次日期
            $map['session_id'] = intval($data['session_id']); // 场次ID
            $map['room_id'] = intval($data['room_id']); // 房间ID

            // 查询数据
            $sessionTeams = model('SessionTeams')->where($map)->find();

            // 判断是否存在该场比赛
            if ($sessionTeams) { // 更新数据
                // TODO：处理数据并进行更新操作
                $id = 1;
            } else { // 新增数据
                // TODO：处理数据
                try {
                    $id = model('SessionTeams')->add($data, 'session_teams_id'); // 新增
                } catch (\Exception $e) {
                    return show(0, '创建比赛团队失败 ' . $e->getMessage(), [], 403);
                }
            }
            if ($id) {
                return show(config('code.success'), '创建比赛团队成功', $id, 201);
            } else {
                return show(0, '创建比赛团队失败', [], 403);
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
                $data = model('SessionTeams')->find($id);
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
     * 保存更新的资源（加入该场比赛已存在的团队）
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
            // 传入的参数
            $param = input('param.');

            // 判断数据是否存在
            $data = [];
            if (!empty($param['team_id']) && !empty($param['user_id']) && !empty($param['number'])) { // 组队ID、用户ID、加入人数
                // 获取比赛场次组队详情
                $sessionTeams = model('SessionTeams')->find($id);
                $sessionTeamsDetail = $sessionTeams['session_teams_detail'] = json_decode($sessionTeams['session_teams_detail'], true);
                $teamIds = []; // 定义该场比赛已经存在的团队
                $players = []; // 定义该场比赛所有团队人员的集合
                foreach ($sessionTeamsDetail as $key => $value) {
                    $teamIds[] = $value['team_id'];
                    $players[] = $value['players'];
                }
                $players = array_reduce($players, 'array_merge', array());
                // array_walk_recursive()可以把任意维度的数组转换成一维数组
                //$result = []; array_walk_recursive($players, function($value) use (&$result) { array_push($result, $value); });

                // 判断团队ID是否已存在于该场比赛
                if (!in_array($param['team_id'], $teamIds)) {
                    return show(config('code.error'), '团队不存在', [], 403);
                }

                // 判断用户是否加入该场比赛
                if (in_array($param['user_id'], $players)) {
                    return show(config('code.error'), '已加入比赛团队', [$sessionTeams, 'team_id' => $param['team_id']], 403);
                } else {
                    $sumPlayersNumber = 0; // 定义该场比赛总人数
                    foreach ($sessionTeamsDetail as $key => $value) {
                        // 处理数据
                        if ($value['team_id'] == $param['team_id']) {
                            $sessionTeamsDetail[$key]['players'][] = $param['user_id']; // 将传入的user_id添加到players数组中
                            sort($sessionTeamsDetail[$key]['players']); // 升序排序

                            $sessionTeamsDetail[$key]['players_number'] = $value['players_number'] + $param['number']; // 人数
                        }

                        $sumPlayersNumber += (int)$value['players_number']; // 该场比赛人数求和
                    }

                    $data['players_number'] = $sumPlayersNumber + $param['number']; // 该场比赛总人数
                    $data['session_teams_detail'] = json_encode($sessionTeamsDetail); // 比赛场次组队详情

                    // 比较 players_number 与房间容纳人数
                    // 获取场景房间可容纳人数
                    $sceneRoom = model('SceneRoom')->field('available_number')->find($sessionTeams['room_id']);
                    if ($data['players_number'] > $sceneRoom['available_number']) {
                        return show(config('code.error'), '已超出房间可容纳人数', [], 403);
                    }
                }
            }

            if (empty($data)) {
                return show(config('code.error'), '数据不合法', [], 404);
            }

            // 更新
            try {
                $result = model('SessionTeams')->save($data, ['session_teams_id' => $id]); // 更新
            } catch (\Exception $e) {
                throw new ApiException($e->getMessage(), 500, config('code.error'));
            }
            if (false === $result) {
                return show(config('code.error'), '加入比赛团队失败', [], 403);
            } else {
                return show(config('code.success'), '加入比赛团队成功', $sessionTeams, 201);
            }
        }
    }
}

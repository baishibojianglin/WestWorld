<?php
/**
 * Created by PhpStorm.
 * User: Yan
 * Date: 2019/12/12
 * Time: 12:12
 */

namespace app\common\validate;

use think\Validate;

class SessionOrder extends Validate
{
    protected $rule = [
        'order_sn|订单编号' => 'require|unique:session_order',
        'user_id|用户' => 'require',
        'session_teams_id' => 'require|unique:session_order,user_id^session_teams_id', // 验证联合唯一性
        'team_id|比赛场次组队' => 'require|unique:session_order,user_id^session_teams_id^team_id',
        'venue_id|场馆' => 'require',
        'scene_id|场景' => 'require',
        'session_date|比赛场次日期' => 'require',
        'session_id|比赛场次' => 'require',
        'start_time|比赛场次开始时间' => 'require',
        'end_time|比赛场次结束时间' => 'require',
        'session_price|场次费' => 'require',
        'room_id|房间' => 'require',
        'room_price|房间价格' => 'require',
        'order_price|订单价格' => 'require', // 应付款金额
    ];

    protected $message = [
        'order_sn.unique' => '订单已存在', // 订单唯一性
        'session_teams_id.unique' => '已加入该场比赛'
    ];

    protected $scene = [

    ];
}
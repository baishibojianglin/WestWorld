<?php
/**
 * Created by PhpStorm.
 * User: Yan
 * Date: 2019/11/21
 * Time: 23:40
 */

namespace app\common\validate;

use think\Validate;

class SessionTeams extends Validate
{
    protected $rule = [
        'venue_id|场馆' => 'require',
        'scene_id|场景' => 'require',
        'session_date|比赛场次日期' => 'require',
        'session_id|比赛场次' => 'require',
        'room_id|场景房间' => 'require',
    ];

    protected $message = [
        //'venue_id.require' => '场馆不能为空',
    ];

    protected $scene = [

    ];
}
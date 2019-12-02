<?php

namespace app\api\controller\v1;

use app\api\controller\Common;
use think\Controller;

/**
 * api模块客户端广告控制器类
 * Class Ad
 * @package app\api\controller\v1
 */
class Ad extends Common
{
    /**
     * 获取广告资源列表
     * @return \think\response\Json
     */
    public function index()
    {
        // 判断为GET请求
        if (request()->isGet()) {
            // 传入的参数
            $param = input('param.');
            $query = http_build_query($param); // 生成 URL-encode 之后的请求字符串 //halt($query);

            // 查询条件
            $map = [];
            $map['a.is_show'] = 1; // 广告是否显示：显示
            $map['a.start_time'] = ['elt', time()]; // 广告开始时间
            $map['a.end_time'] = ['egt', time()]; // 广告结束时间
            $map['ap.status'] = config('code.status_enable'); // 广告位置状态：开启
            if (!empty($param['position_id'])) { // 广告位置ID
                $map['a.position_id'] = intval($param['position_id']);
            }

            // 获取分页page、size
            $this->getPageAndSize($param);

            // 获取分页列表数据 模式一：基于paginate()自动化分页
            $data = model('Ad')->getAd($map, 'sort', $this->size);

            return show(config('code.success'), 'ok', $data);
        }
    }
}

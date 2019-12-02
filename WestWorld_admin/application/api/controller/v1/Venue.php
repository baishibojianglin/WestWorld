<?php

namespace app\api\controller\v1;

use app\api\controller\Common;
use app\common\lib\exception\ApiException;
use think\Controller;

/**
 * api模块客户端场馆控制器类
 * Class Venue
 * @package app\api\controller\v1
 */
class Venue extends Common
{
    /**
     * 显示场馆资源列表
     *
     * @return \think\Response
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
            $map['status'] = config('code.status_enable'); // 启用状态
            if (!empty($param['grade_id'])) { // 场馆等级
                $map['grade_id'] = input('param.grade_id', 0, 'intval'); //intval($param['grade_id'])
            }
            if (!empty($param['venue_name'])) { // 场馆名称
                $map['venue_name'] = ['like', '%' . $param['venue_name'] . '%'];
            }
            if (!empty($param['create_time'])) { // 创建时间
                $map['create_time'] = $param['create_time'];
            }

            /*// 获取分页列表数据 模式一
            $data = model('Venue')->getVenue();
            $status = config('code.status');
            foreach($data as $key => $value){
                $data[$key]['status_msg'] = $status[$value['status']];
            }
            return show(config('code.success'), 'ok', $data);*/

            // 获取分页列表数据 模式二：page当前页，size每页条数，from每页从第几条开始 => 'limit from,size'
            //$param['size'] = 1; // 定义每页条数
            $this->getPageAndSize($param); // 获取分页page、size

            // 判断列表的标识 listFlag （0全部场馆，1附近）
            if (1 == $param['listFlag']) { // 附近场馆
                // 判断经纬度存在
                if (isset($param['longitude']) && isset($param['latitude'])) {
                    // 获取全部（注意是全部）列表数据
                    $list = model('Venue')->where($map)->select();

                    // 定义经纬度集合
                    $mapLongitude = [];
                    $mapLatitude = [];

                    foreach ($list as $key => $value) {
                        // 根据两点的经纬度计算距离，此处用于获取指定距离的经纬度集合
                        $list[$key]['distance'] = round(distance($param['latitude'], $param['longitude'], $value['latitude'], $value['longitude']), 3);

                        // 获取指定距离（如距定位 2km 内）的经纬度集合
                        if ($list[$key]['distance'] <= 2) {
                            $mapLongitude[] = $value['longitude'];
                            $mapLatitude[] = $value['latitude'];
                        }
                    }
                    $map['longitude'] = ['in', $mapLongitude]; // 经度集合
                    $map['latitude'] = ['in', $mapLatitude]; // 纬度集合
                }
            }

            // 根据传入的场馆ID加载更多（此处判断对于全部或附近场馆通用）
            if (!empty($param['minId'])) {
                // 获取场馆最大ID（除 $map['venue_id'] 外的其他 $map 条件必须带上，注意区分 listFlag 为1或0的 $map 不同）
                $maxVenueId = model('Venue')->getMax($map, 'venue_id');

                // 判断传入的场馆ID是否为最大ID，即数据是否全部加载
                if ($param['minId'] == $maxVenueId) {
                    return show(config('code.error'), '加载完成', ['maxId' => $maxVenueId], 404);
                }

                // 大于传入的场馆ID的集合
                $map['venue_id'] = ['gt', $param['minId']];
            }

            // 获取（指定距离）列表数据
            $list = model('Venue')->getVenueByCondition($map, $this->from, $this->size);
            // 获取（指定距离）列表数据总数
            $total = model('Venue')->getVenueCountByCondition($map);
            // 总页数：结合 总数 + size => 有多少页
            $pages = ceil($total / $this->size); // 1.1 => 2

            // 返回处理后的json数据
            $status = config('code.status');
            foreach ($list as $key => $value) {
                $list[$key]['status_msg'] = $status[$value['status']];

                // 根据两点的经纬度计算距离
                if (isset($param['longitude']) && isset($param['latitude'])) {
                    $list[$key]['distance'] = round(distance($param['latitude'], $param['longitude'], $value['latitude'], $value['longitude']), 3);
                }
            }

            $data = ['total' => $total, 'pages' => $pages, 'list' => $list];
            return show(config('code.success'), 'ok', $data); // http://serverName/admin_venue?page=2&size=1
        }
    }

    /**
     * 显示指定的场馆资源
     * @param int $id
     * @return \think\response\Json
     * @throws ApiException
     */
    public function read($id)
    {
        // 判断为GET请求
        if (request()->isGet()) {
            // 查询条件
            $map = [];
            $map['is_delete'] = ['neq', config('code.is_delete')];

            try {
                $data = model('Venue')->where($map)->find($id);
            } catch (\Exception $e) {
                throw new ApiException($e->getMessage(), 500, config('code.error'));
            }

            if ($data) {
                // 处理数据
                // 定义status_msg
                $status = config('code.status');
                $data['status_msg'] = $status[$data['status']];

                // 最近登录时间
                $data['last_login_time'] = date('Y-m-d H:i:s', $data['last_login_time']);

                return show(config('code.success'), 'ok', $data);
            } else {
                return show(config('code.error'), 'Not Found', $data, 404);
            }
        }
    }
}

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
            if (!empty($param['grade_id'])) {
                $map['grade_id'] = input('param.grade_id', 0, 'intval'); //intval($param['grade_id'])
            }
            if (!empty($param['venue_name'])) {
                $map['venue_name'] = ['like', '%' . $param['venue_name'] . '%'];
            }
            if (!empty($param['create_time'])) {
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
            // 获取列表数据
            $list = model('Venue')->getVenueByCondition($map, $this->from, $this->size);
            // 获取列表数据总数
            $total = model('Venue')->getVenueCountByCondition($map);
            // 总页数：结合 总数 + size => 有多少页
            $pages = ceil($total / $this->size); // 1.1 => 2

            // 返回处理后的json数据
            $status = config('code.status');
            foreach ($list as $key => $value) {
                $list[$key]['status_msg'] = $status[$value['status']];
            }
            $data = [
                'total' => $total,
                'pages' => $pages,
                'list' => $list,
            ];
            return show(config('code.success'), 'ok', $data); // http://test.battle.com/admin_venue?page=2&size=1
            //return $this->fetch('', ['data' => $data]);
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

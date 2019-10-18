<?php

namespace app\api\controller;

use app\common\lib\Aes;
use app\common\lib\exception\ApiException;
use app\common\lib\IAuth;
use app\common\lib\Time;
use think\Cache;
use think\Controller;

/**
 * api模块公共控制器类
 * Class Common
 * @package app\api\controller
 */
class Common extends Controller
{
    /**
     * headers信息
     * @var string
     */
    public $headers = '';

    /**
     * page 当前页
     * @var string
     */
    public $page = '';

    /**
     * size 每页条数
     * @var string
     */
    public $size = '';

    /**
     * 每页从第几条开始
     * @var int
     */
    public $from = 0;

    /**
     * 初始化
     */
    public function _initialize()
    {
        $this->checkRequestAuth(); // TODO：生产环境必须检查数据的合法性
    }

    /**
     * 检查每次app请求的数据是否合法
     */
    public function checkRequestAuth()
    {
        // 首先需要获取headers
        $headers = request()->header(); //halt($headers);

        /* TODO
        sign 加密：客户端工程师，解密：服务端工程师
        1.headers、body仿照sign形式做参数的加解密：如对 headers 的 version、apptype、did、model 加密放在一个参数 headers_params 里，对 headers_params 解密时则生成字符串 'version=1&apptype=android&did=12345dg&model=mix2s'
        2.IAuth()->setSign()的算法步骤需要客户端与服务端工程师约定，但最终算法是AES */

        // 校验基础参数
        if(empty($headers['sign'])){
            throw new ApiException('sign不存在', 400);
        }
        if(!in_array($headers['apptype'], config('app.apptypes'))){
            throw new ApiException('apptype不合法', 400);
        }

        // 校验sign的合法性
        if(!IAuth::checkSignPass($headers)){
            throw new ApiException('授权码sign校验失败', 401);
        }

        // 当sign校验成功时，存入缓存
        // sign唯一性请求处理：1.文件缓存，第一次请求后写标识（一台服务器） 2.写入mysql（分布式） 3.写入redis（分布式、数据量大）
        Cache::set($headers['sign'], 1, config('app.app_sign_cache_time'));

        $this->headers = $headers; // headers信息校验成功后，便以其他继承该类的子类使用headers数据
    }

    /**
     * 测试AES加密与解密
     */
    public function testAes()
    {
        /*// AES加密
        $str = 'id=1&ms=45&username=singwa';
        echo (new Aes())->encrypt($str);exit;
        // AES解密
        $aes_str = '6dDiaoQrSC2tPepBYWGFh8ri8FNeKXBwRFKbn3hv8qA=';
        echo (new Aes())->decrypt($aes_str);exit;*/

        // 加密
        $data = [
            'did' => '12345dg',
            'version' => 1,
            //'time' => Time::get13Timestamp(),
        ];
        $data = json_encode($data);dump($data); // "{"did":"12345dg","version":1}"
        //echo IAuth::setSign($data);exit;
        dump(Aes::opensslEncrypt($data)); // "LhRYeMStzMpOi5t9TC1uot0+fX9jhnJQqQG/DeiGdH8="
        // 解密
        $str = 'LhRYeMStzMpOi5t9TC1uot0+fX9jhnJQqQG/DeiGdH8=';
        //echo (new Aes())->decrypt($str);exit;
        dump(Aes::opensslDecrypt($str)); // "{"did":"12345dg","version":1}"
    }

    /**
     * 获取分页page、size、from
     * @param $params
     */
    public function getPageAndSize($params)
    {
        $this->page = !empty($params['page']) ? $params['page'] : 1;
        $this->size = !empty($params['size']) ? $params['size'] : config('paginate.list_rows');
        $this->from = ($this->page - 1) * $this->size; // 'limit from,size'
    }
}

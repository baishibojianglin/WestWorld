<?php
/**
 * Created by PhpStorm.
 * User: baidu
 * Date: 17/7/28
 * Time: 上午12:27
 */

namespace app\common\lib;

use app\common\lib\Aes;
use think\Cache;

/**
 * IAuth相关
 * Class IAuth
 * @package app\common\lib
 */
class IAuth
{
    /**
     * 密码加密
     * @param string $data
     * @return string
     */
    public static function encrypt($data)
    {
        return md5(config('app.password_pre_salt') . $data);
    }

    /**
     * 生成每次请求的sign验签算法
     * @param array $data
     * @return string
     */
    public static function setSign($data = [])
    {
        // 1.按字典排序
        ksort($data); // ksort() 函数对关联数组按照键名进行升序排序。
        // 2.以&符号拼接字符串数据
        $string = http_build_query($data); // 生成 URL-encode 之后的请求字符串
        // 3.通过AES来加密
        $string = Aes::opensslEncrypt($string); //$string = (new Aes())->encrypt($string);
        /*// 4.所有字符转化大写（该步骤不要，因为不能转大写，否则解密时不知道那些字符是小写）
        $string = strtoupper($string);*/

        return $string;
    }

    /**
     * 检查sign是否正常
     * @param array $data
     * @return bool
     */
    public static function checkSignPass($data)
    {
        // AES解密
        $str = Aes::opensslDecrypt($data['sign']); //$str = (new Aes())->decrypt($data['sign']);
        if(empty($str)) {
            return false;
        }

        // did=xx&app_type=3
        parse_str($str, $arr); // parse_str() 函数把查询字符串解析到变量中 //halt($arr);
        if(!is_array($arr) || empty($arr['did']) || $arr['did'] != $data['did']) { // 其他headers信息如version、apptype、model等都要做类似判断
            return false;
        }
        if(!config('app_debug')) { // TODO：生产环境关闭应用调试模式
            // sign有效时间判定
            if ((time() - ceil($arr['time'] / 1000)) > config('app.app_sign_time')) {
                return false;
            }

            // sign唯一性判定
            //echo Cache::get($data['sign']);exit;
            if (Cache::get($data['sign'])) {
                return false; // 表示sign只能请求一次
            }
        }
        return true;
    }

    /**
     * 设置登录的token（唯一性的）
     * @param string $phone
     * @return string
     */
    public static function setAppLoginToken($phone = '')
    {
        $str = md5(uniqid(md5(microtime(true)), true)); // uniqid() 获取一个带前缀、基于当前时间微秒数的唯一ID
        $str = sha1($str . $phone);
        return $str;
    }
}
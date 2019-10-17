<?php

namespace app\common\lib;

/**
 * AES加密与解密类库
 * Class Aes
 * @package app\common\lib
 */
class Aes
{
    /**
     * 密钥
     * @var string
     */
    private $key = null;

    /**
     * iv是初始化向量：超过16字节或者不足16字节都会被补足16字节或者截断到16字节。由于AES是块加密，铭文被分割成固定长度的块（一般是16字节长度），所以iv也是16字节
     * @var string
     */
    static $iv = '1234567812345678';

    /**
     * 构造函数
     */
    public function __construct()
    {
        // 密钥：string，需要在配置文件app.php中定义aeskey
        $this->key = config('app.aeskey');
    }

    /**
     * AES加密
     * @param string $input 加密的字符串
     * @param string key 解密的key
     * @return HexString
     */
    public function encrypt($input = '')
    {
        $size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
        $input = $this->pkcs5_pad($input, $size);
        $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_ECB, '');
        $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        mcrypt_generic_init($td, $this->key, $iv);

        $data = mcrypt_generic($td, $input);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        $data = base64_encode($data);

        return $data;
    }

    /**
     * 填充方式 pkcs5
     * @param string $text 原始字符串
     * @param string $blocksize 加密长度
     * @return string
     */
    private function pkcs5_pad($text, $blocksize)
    {
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }

    /**
     * AES解密
     * @param string $sStr 解密的字符串
     * @param string key 解密的key
     * @return string
     */
    public function decrypt($sStr)
    {
        $decrypted= mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $this->key, base64_decode($sStr), MCRYPT_MODE_ECB);
        $dec_s = strlen($decrypted);
        $padding = ord($decrypted[$dec_s-1]);
        $decrypted = substr($decrypted, 0, -$padding);

        return $decrypted;
    }


    /* PHP 7+ 使用openssl加解密 start */
    /**
     * AES加密
     * @param string $string 需要加密的字符串
     * @return string
     * @internal param string $key 密钥
     * @internal param string $iv 初始化向量
     */
    public static function opensslEncrypt($string)
    {
        // openssl_encrypt 加密不同于 Mcrypt，对秘钥长度要求，超出16加密结果不变
        $data = openssl_encrypt($string, 'AES-256-CBC', $key = config('app.aeskey'), 0, $iv = self::$iv);

        //$data = strtolower(bin2hex($data));

        return $data;
    }

    /**
     * AES解密
     * @param string $string 需要解密的字符串
     * @return string
     * @internal param string $key 密钥
     * @internal param string $iv 初始化向量
     */
    public static function opensslDecrypt($string)
    {
        $decrypted = openssl_decrypt($string, 'AES-256-CBC', $key = config('app.aeskey'), 0, $iv = self::$iv);

        return $decrypted;
    }
    /* PHP 7+ 使用openssl加解密 end */
}
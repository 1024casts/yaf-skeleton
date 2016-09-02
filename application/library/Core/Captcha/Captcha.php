<?php

namespace Core\Captcha;

/**
 * Class Captcha
 * @package Core\Captcha
 */
class Captcha
{
    /**
     * @var string
     */
    protected $charset = 'abcdefghkmnprstuvwxyzABCDEFGHKMNPRSTUVWXYZ23456789';

    /**
     * 验证码
     *
     * @var string
     */
    protected $code;

    /**
     * 图形资源句柄
     *
     * @var resource
     */
    protected $img;

    /**
     * 验证码长度
     *
     * @var int
     */
    protected $len;

    /**
     * 图片宽度
     *
     * @var int
     */
    protected $width;

    /**
     * 图片高度
     *
     * @var int
     */
    protected $height;

    /**
     * 字体
     *
     * @var string
     */
    protected $font = __DIR__ . '/font/ABeeZee.ttf';

    /**
     * Captcha constructor.
     *
     * @param int $len
     * @param int $width
     * @param int $height
     */
    public function __construct($len = 4, $width = 100, $height = 25)
    {
        $this->len = $len;
        $this->width = $width;
        $this->height = $height;

        $this->code = $this->code($len);
    }

    /**
     * 获取验证码字符串
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * 生成验证码字符串并输出
     */
    public function generate()
    {
        $this->img = imagecreatetruecolor($this->width, $this->height);
        $white = imagecolorallocate($this->img, 250, 250, 255);
        imagefill($this->img, 0, 0, $white);

        $xBase = intval($this->width / 8);
        $yBase = intval($this->height / 8);

        // 添加干扰线和文字
        $offset = 0;
        for($i = 0; $i < $this->len; $i++) {
            $color = imagecolorallocate($this->img, mt_rand(0, 100), mt_rand(20, 120), mt_rand(50, 150));

            $offset += mt_rand(12, $xBase * 2);
            // imagestring($this->img, 4, $offset, mt_rand(0, $yBase * 6), $this->code[$i], $color);
            imagettftext($this->img, mt_rand(14, 20), mt_rand(-20, 20), $offset, max(20, mt_rand($yBase * 3, $yBase * 7)), $color, $this->font, $this->code[$i]);

            // 添加同色干扰弧线
            if ($i < 3) {
                // imageline($this->img, mt_rand(0, $xBase), mt_rand(0, $this->height), mt_rand($xBase * 6, $this->width), mt_rand($yBase, $this->height), $color);
                imagearc($this->img, mt_rand($xBase, $this->width), mt_rand(0, $yBase * 4), $this->width, $yBase * 4, mt_rand(0, 45), mt_rand(90, 200), $color);
            }
        }

        ob_clean();
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Pragma: no-cache');
        header('Cache-control: private');
        header('Content-Type: image/png');
        imagepng($this->img);
        imagedestroy($this->img);
    }

    /**
     * 验证码
     *
     * @param $len
     * @return string
     */
    protected function code($len)
    {
        return substr(str_shuffle($this->charset), 0, $len);
    }
}
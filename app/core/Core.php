<?php
namespace App\Core;

use App\Book\Words;
use App\Error\Error;

if (php_sapi_name() !== 'cli') {
    if ((session_status() === PHP_SESSION_ACTIVE ? true : false) === false) {
        session_name("gmC");
        session_start();
    }
}

/**
 * gmCaptcha vers. 0.1.10 - A concept of Captcha
 * -----------------------------------------------
 * Generate Captcha graphic, mathematical or mixed
 * with random text or with use of a dictionary
 * -----------------------------------------------
 * Developed by Gianluigi 'A35G' - © 2013 - 2024
 * https://www.hackworld.it/
 * -----------------------------------------------
 */
class Core
{
    /**
     * The root of gmCaptcha files
     *
     * @var string
     */
    private static $rootDir;

    /**
     * The folder of gmCaptcha App files
     *
     * @var string
     */
    private static $appDir;

    /**
     * The location of the gmCaptcha font directory
     *
     * @var string
     */
    private static $fontDir;

    private static $defaultFont;

    /**
     * The location of the gmCaptcha temp directory
     *
     * @var string
     */
    private static $tempDir;

    public static $config = array();

    private static $e;
    private static $w;

    public function __construct()
    {
        self::$rootDir = realpath(__DIR__."/../../");
        self::$appDir = realpath(__DIR__."/../");
        self::$fontDir = self::$rootDir."/public/font";
        self::$tempDir = self::$rootDir."/public/temp";

        self::$config = self::get_config();

        self::$e = new Error();
        self::$w = new Words();

        if (is_array(self::$config) && !empty(self::$config)) {
            self::initConfigure();
        }
    }

    private function get_config()
    {
        static $config;
        $stop = false;

        if (empty($config)) {
            $file_path = self::$appDir.'/config/config.php';
            $found = false;
            if (file_exists($file_path)) {
                $found = true;
                require $file_path;
            }

            if (!$found) {
                $stop = true;
                $errm = "Configuration file does not exist.";
            }

            if ($stop === false && !is_array($config)) {
                $stop = true;
                $errm = "Your config file does not appear to be formatted ";
                $errm .= "correctly.";
            }

            if ($stop) {
                self::$e->logError($errm);
                exit(0);
            }
        }

        return $config;
    }

    private static function setFont()
    {
        if (array_key_exists("appFont",self::$config) === false) {
            self::$e->logError("No fonts specified in the config file");
            exit(0);
        }

        if (empty(self::$config["appFont"])) {
            self::$e->logError("No fonts specified in the config file");
            exit(0);
        }

        if (is_readable(self::$fontDir."/".self::$config["appFont"]) === false) {
            self::$e->logError("Font file not found or not readable");
            exit(0);
        }

        self::$defaultFont = self::$config["appFont"];
    }

    private function checkTempPathPermission()
    {
        if (!is_readable(self::$tempDir) OR !is_writable(self::$tempDir)) {
            $mx = "Check read and write permissions for the \"temp\" folder";
            self::$e->logError($mx);
            exit(0);
        }
    }

    private function initConfigure()
    {
        self::setFont();
        self::checkTempPathPermission();
    }

    private function randPhrase(
        int $upper = 2,
        int $lower = 2,
        int $numeric = 2,
        int $other = null
    ) {
        $phsWord = '';
        $phsOrder = array();

        if (is_numeric($upper) && intval($upper) > 0) {
            for ($i = 0; $i < intval($upper); ++$i) {
                $phsOrder[] = chr(rand(65, 90));
            }
        }

        if (is_numeric($lower) && intval($lower) > 0) {
            for ($i = 0; $i < intval($lower); ++$i) {
                $phsOrder[] = chr(rand(97, 122));
            }
        }

        if (is_numeric($numeric) && intval($numeric) > 0) {
            for ($i = 0; $i < intval($numeric); ++$i) {
                $phsOrder[] = chr (rand(48, 57));
            }
        }

        if (is_numeric($other) && intval($other) > 0) {
            for ($i = 0; $i < intval($other); ++$i) {
                $phsOrder[] = chr(rand(33, 47));
            }
        }

        if (!empty($phsOrder)) {
            shuffle($phsOrder);

            foreach ($phsOrder as $char) {
                $phsWord .= $char;
            }
        }

        return $phsWord;
    }

    private static function toSession(array $args)
    {
        if (array_key_exists("appSessVar",self::$config) !== false
            && !empty(self::$config["appSessVar"])) {
            if (!empty($args)) {
                $lbSess = htmlspecialchars(self::$config["appSessVar"]);
                $_SESSION[$lbSess] = base64_encode($args["c"]);
            }
        }
    }

    public static function checkIsValidJSON(string $data)
    {
        if (!empty($data)) {
            return is_string($data) && 
              is_array(json_decode($data, true)) ? true : false;
        }

        return false;
    }

    public static function createImg(string $code)
    {
        if (get_extension_funcs("gd") === false) {
            self::$e->logError("GD extension not enabled or not installed");
            exit(0);
        }

        $x = 130;
        $y = 37;
        $colors = array();

        $clen = strlen($code);

        $space = ($x / ($clen + 1));

        $img = imagecreatetruecolor($x, $y);
        $bg = imagecolorallocate($img, 255, 255, 255);
        $border = imagecolorallocate($img, 0, 0, 0);

        $colors[] = imagecolorallocate($img, 128, 64, 192);
        $colors[] = imagecolorallocate($img, 192, 64, 128);
        $colors[] = imagecolorallocate($img, 108, 192, 64);

        imagefilledrectangle(
            $img,
            1,
            1,
            (intval($x) - 2),
            (intval($y) - 2),
            $bg
        );

        for ($i = 0; $i < $clen; ++$i) {
            $color = $colors[$i % count($colors)];
            @imagettftext(
                $img,
                (20 + rand(0, 8)),
                (-20 + rand(0, 30)),
                (($i + 0.3) * $space),
                (25 + rand(0, 5)),
                $color,
                self::$fontDir.'/'.self::$defaultFont,
                $code[$i]
            );
        }

        imagepng($img, self::$tempDir.'/captcha.png', 9);

        $imgfile = self::$tempDir."/captcha.png";

        $imgbinary = fread(fopen($imgfile, "r"), filesize($imgfile));

        imagedestroy($img);

        @unlink(self::$tempDir.'/captcha.png');

        self::toSession(array(
            "c" =>  $code
        ));

        return base64_encode($imgbinary);
    }

    private static function getRand(int $enum)
    {
        switch ($enum) {
            case '1':
                $iNum = mt_rand(1, 99);
                break;
            case '2':
                $iNum = mt_rand(1, 10);
                break;
            case '3':
                $iNum = mt_rand(10, 20);
                break;
            case '4':
                $iNum = mt_rand(2, 5);
                break;
            case '5':
                $iNum = mt_rand(1, 4);
                break;
            default:
                $iNum = self::getRand(1);
                break;
        }

        return $iNum;
    }

    private static function checkMath(int $bval)
    {
        if (array_key_exists("appOperation",self::$config) === false) {
            $selOpr = 1;
        } elseif (is_numeric(self::$config["appOperation"]) === false) {
            $selOpr = 1;
        } elseif (intval(self::$config["appOperation"]) === 0) {
            $selOpr = 1;
        } elseif (intval(self::$config["appOperation"]) > 5) {
            $selOpr = 1;
        } else {
            $selOpr = intval(self::$config["appOperation"]);
        }

        $opr = (intval($bval) > 0 
            && intval($bval) <= 5) ? intval($bval) : $selOpr;

        $f_num = $s_num = $res_opr = 0;
        switch ($opr) {
            case '1':
                $f_num = self::getRand(2);
                $s_num = self::getRand(2);
                $op_sign = '+';

                $res_opr = (intval($f_num) + intval($s_num));
                break;
            case '2':
                $f_num = self::getRand(3);
                $s_num = self::getRand(2);
                $op_sign = '-';

                $res_opr = (intval($f_num) - intval($s_num));
                break;
            case '3':
                $f_num = self::getRand(2);
                $s_num = self::getRand(2);
                $op_sign = 'x';

                $res_opr = (intval($f_num) * intval($s_num));
                break;
            case '4':
                $s_num = self::getRand(2);
                $f_num = (intval($s_num) * self::getRand(4));
                $op_sign = ':';

                $res_opr = (intval($f_num) / intval($s_num));
                break;
            case '5':
                return self::checkMath(self::getRand(5));
                break;
            default:
                $self::checkMath();
                break;
        }

        self::toSession(array(
            "c" =>  $res_opr
        ));

        return sprintf("%d %s %d",intval($f_num), $op_sign, intval($s_num));
    }

    private static function makeGraphic(string $type = 'T', int $sign = 1)
    {
        switch ($type) {
            case "T":
                $code = self::randPhrase(2,2,2);
                $dataImg = self::createImg($code);
                break;
            case "M":
                $code = self::checkMath($sign);
                $dataImg = self::createImg($code);
                break;
        }

        return $dataImg;
    }

    public static function makeMath(int $sign = 1)
    {
        return self::checkMath($sign);
    }

    private static function makeFromDictionary()
    {
        return self::$w->makeFromDictionary();
    }

    public static function makeFull(string $options)
    {
        if (!empty($options)) {
            $j = json_decode($options,true);
            if (!empty($j)) {
                if ($j["style"] === "text") {
                    $dataImg = (self::$config["appUseDictionary"]) 
                    ? self::makeFromDictionary() 
                    : self::makeGraphic("T");
                }

                if ($j["style"] === "math") {
                    $inm = (array_key_exists("custom",$j) 
                        && is_numeric($j["custom"]) 
                        && ((1 <= intval($j["custom"])) 
                            && (intval($j["custom"]) <= 5))) 
                    ? intval($j["custom"]) 
                    : intval(self::$config["appOperation"]);

                    $dataImg = self::makeGraphic("M",$inm);
                }
            }
        }

        return $dataImg;
    }

    public static function callDataSound()
    {
        $lbSess = htmlspecialchars(self::$config["appSessVar"]);
        return $_SESSION[$lbSess];
    }
}

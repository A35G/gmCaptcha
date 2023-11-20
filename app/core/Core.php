<?php

namespace App\Core;

use App\Book\Words;
use App\Error\Error;

/**
 * gmCaptcha vers. 0.1.8 - A concept of Captcha
 * -----------------------------------------------
 * Generate Captcha graphic, mathematical or mixed
 * with random text or with use of a dictionary
 * -----------------------------------------------
 * Developed by Gianluigi 'A35G' - © 2013 - 2023
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
    private $rootDir;

    /**
     * The folder of gmCaptcha App files
     *
     * @var string
     */
    private $appDir;

    /**
     * The location of the gmCaptcha font directory
     *
     * @var string
     */
    private $fontDir;

    private $defaultFont;

    /**
     * The location of the gmCaptcha temp directory
     *
     * @var string
     */
    private $tempDir;

    public $config = array();

    public function __construct()
    {
        $rootDir = realpath(__DIR__ . "/../../");
        $this->setRootDir($rootDir);

        $appDir = realpath(__DIR__ . "/../");
        $this->setAppDir($appDir);

        $this->setFontDir($rootDir . "/public/font");
        $this->setTempDir($rootDir . "/temp");

        $this->config = $this->get_config();

        $this->initConfigure();
    }

    /**
     * @param string $rootDir
     */
    public function setRootDir(string $rootDir): void
    {
        $this->rootDir = $rootDir;
    }

    /**
     * @param string $appDir
     */
    public function setAppDir(string $appDir): void
    {
        $this->appDir = $appDir;
    }

    /**
     * @param string $fontDir
     */
    public function setFontDir(string $fontDir): void
    {
        $this->fontDir = $fontDir;
    }

    /**
     * @param string $tempDir
     */
    public function setTempDir(string $tempDir): void
    {
        $this->tempDir = $tempDir;
    }

    private function get_config()
    {
        static $config;

        if (empty($config)) {
            $file_path = $this->appDir . '/config/config.php';
            $found = false;
            if (file_exists($file_path)) {
                $found = true;
                require $file_path;
            }

            if ( ! $found) {
                $err = new Error();
                $err->logError("Configuration file does not exist.");
                exit(0);
            }

            if ( ! isset($config) OR ! is_array($config)) {
                $err = new Error();
                $err->logError("Your config file does not appear to be formatted
                 correctly.");
                exit(0);
            }
        }

        return $config;
    }

    private function setFont(): void
    {
        if (array_key_exists("appFont",$this->config) === false) {
            $err = new Error();
            $err->logError("No fonts specified in the config file");
            exit(0);
        }

        if (empty($this->config["appFont"])) {
            $err = new Error();
            $err->logError("No fonts specified in the config file");
            exit(0);
        }

        if (is_readable($this->fontDir . "/" . $this->config["appFont"]) === FALSE) {
            $err = new Error();
            $err->logError("Font file not found or not readable");
            exit(0);
        }

        $this->defaultFont = $this->config["appFont"];
    }

    private function checkTempPathPermission()
    {
        if (is_readable($this->tempDir) !== TRUE 
            OR is_writable($this->tempDir) !== TRUE) {
            $err = new Error();
            $err->logError("Check read and write permissions for the \"temp\" folder");
            exit(0);
        }
    }

    private function initConfigure()
    {
        $this->setFont();
        $this->checkTempPathPermission();
        $this->newCode();
    }

    private function checkSession(): bool
    {
        if (php_sapi_name() !== 'cli') {
            if (version_compare(phpversion(), '5.4.0', '>=')) {
                return session_status() === PHP_SESSION_ACTIVE ? TRUE : FALSE;
            } else {
                return session_id() === '' ? FALSE : TRUE;
            }
        }

        return FALSE;
    }

    private function newCode(): void
    {
        if (array_key_exists("appSessionVariable",$this->config) === FALSE) {
            $err = new Error();
            $err->logError("No name specified in config file for session variable");
            exit(0);
        }

        if (empty($this->config["appSessionVariable"])) {
            $err = new Error();
            $err->logError("No name specified in config file for session variable");
            exit(0);
        }
        
        if ($this->checkSession() === FALSE) {
            session_name("gmC");
            session_start();
        }

        if (isset($_SESSION[$this->config["appSessionVariable"]])
            && ! empty($_SESSION[$this->config["appSessionVariable"]])) {
            unset($_SESSION[$this->config["appSessionVariable"]]);
        }
    }

    private function randPhrase(
        int $upper = 2,
        int $lower = 2,
        int $numeric = 2,
        int $other = NULL
    ): string {

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

    private function toSession(string $args): void
    {
        if (array_key_exists("appSessionVariable",$this->config) !== false
            && ! empty($this->config["appSessionVariable"])) {
            if (!empty($args)) {
                $lbSess = htmlspecialchars($this->config["appSessionVariable"]);
                $_SESSION[$lbSess] = base64_encode($args);
            }
        }
    }

    public function createImg(string $code): string
    {
        if (get_extension_funcs("gd") === false) {
            $err = new Error();
            $err->logError("GD extension not enabled or not installed");
            exit(0);
        }

        $x = 130;
        $y = 37;
        $colors = array();

        $space = ($x / (strlen($code) + 1));

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

        for ($i = 0; $i < strlen($code); ++$i) {
            $color = $colors[$i % count($colors)];
            imagettftext(
                $img,
                (20 + rand(0, 8)),
                (-20 + rand(0, 30)),
                (($i + 0.3) * $space),
                (25 + rand(0, 5)),
                $color,
                $this->fontDir . '/' . $this->defaultFont,
                $code[$i]
            );
        }

        imagepng($img, $this->tempDir . '/captcha.png', 9);

        $imgfile = $this->tempDir . "/captcha.png";

        $imgbinary = fread(fopen($imgfile, "r"), filesize($imgfile));

        imagedestroy($img);

        @unlink($this->tempDir . '/captcha.png');

        $this->toSession($code);

        return base64_encode($imgbinary);
    }

    private function getRand(int $enum): int
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

    private function checkMath(int $bval): string
    {
        if (array_key_exists("appOperation",$this->config) === false) {
            $selOpr = 1;
        } elseif (is_numeric($this->config["appOperation"]) === false) {
            $selOpr = 1;
        } elseif (intval($this->config["appOperation"]) === 0) {
            $selOpr = 1;
        } elseif (intval($this->config["appOperation"]) > 5) {
            $selOpr = 1;
        } else {
            $selOpr = intval($this->config["appOperation"]);
        }

        $opr = (intval($bval) > 0 && intval($bval) <= 5) ? intval($bval) : $selOpr;

        $f_num = $s_num = $res_opr = 0;
        switch ($opr) {
            case '1':
                $f_num = $this->getRand(2);
                $s_num = $this->getRand(2);
                $op_sign = '+';

                $res_opr = (intval($f_num) + intval($s_num));
                break;
            case '2':
                $f_num = $this->getRand(3);
                $s_num = $this->getRand(2);
                $op_sign = '-';

                $res_opr = (intval($f_num) - intval($s_num));
                break;
            case '3':
                $f_num = $this->getRand(2);
                $s_num = $this->getRand(2);
                $op_sign = 'x';

                $res_opr = (intval($f_num) * intval($s_num));
                break;
            case '4':
                $s_num = $this->getRand(2);
                $f_num = (intval($s_num) * $this->getRand(4));
                $op_sign = ':';

                $res_opr = (intval($f_num) / intval($s_num));
                break;
            case '5':
                return self::checkMath($this->getRand(5));
                break;
            default:
                $self::checkMath();
                break;
        }

        $this->toSession($res_opr);

        return sprintf("%d %s %d",intval($f_num), $op_sign, intval($s_num));
    }

    public function makeGraphic(string $type = 'T', int $sign = 1)
    {
        switch ($type) {
            case "T":
                $code = $this->randPhrase(2,2,2);
                $dataImg = $this->createImg($code);
                break;
            case "M":
                $code = $this->checkMath($sign);
                $dataImg = $this->createImg($code);
                break;
        }

        if (isset($dataImg) && !empty($dataImg)) {
            include $this->rootDir . "/public/view/captchaGraphic.php";
        }
    }

    public function makeMath(int $sign = 1)
    {
        $dataString = $this->checkMath($sign);
        if (isset($dataString) && !empty($dataString)) {
            include $this->rootDir . "/public/view/captchaText.php";
        }
    }

    public function makeFromDictionary()
    {
        $dc = new Words;
        $dc->makeFromDictionary();
    }

    public function makeAdvanced()
    {
        $code = $this->randPhrase(0,3,3);
        $dataImg = $this->createImg($code);
        if (isset($dataImg) && !empty($dataImg)) {
            include $this->rootDir . "/public/view/captchaFull.php";
        }
    }
}

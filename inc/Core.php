<?php
namespace gmCaptcha;

/**
 * gmCaptcha vers. 0.1.5 - A concept of Captcha
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
     * The location of the gmCaptcha font directory
     *
     * @var string
     */
    private $fontDir;

    /**
     * The location of the gmCaptcha image directory
     *
     * @var string
     */
    private $imageDir;

    private $defaultFont = "cheapink.ttf";

    /**
     * Code for Mathematical operations
     * 1: Addition;
     * 2: Subtraction;
     * 3: Multiplication;
     * 4: Division;
     * 5: Random.
     * 
     * @var integer
     */
    private $defaultOperation = 1;

    private $useDictionary = TRUE;

    /**
     * The root of gmCaptcha dictionary files
     *
     * @var string
     */
    private $dictionaryDir;

    /**
     * Settings to use dictionary words
     * 
     * @var array
     */
    private $dictSettings = array(
        "minWordLength" =>  4,
        "maxWordLength" =>  6
    );

    private $dictionaryFile = "1.1million word list.txt";

    /**
     * List of dictionary words
     * 
     * @var array
     */
    private $dictWords = array();

    public function __construct()
    {
        $rootDir = realpath(__DIR__ . "/../");
        $this->setRootDir($rootDir);
        $this->setFontDir($rootDir . "/font");
        $this->setImgDir($rootDir . "/public");
        $this->setDictDir($rootDir . "/dictionary");

        if ($this->useDictionary !== FALSE && ! empty($this->dictionaryFile)):
            $this->loadDictionary($this->dictionaryFile);
        endif;

        $this->newCode();
    }

    /**
     * @param string $fontDir
     */
    public function setFontDir(string $fontDir): void
    {
        $this->fontDir = $fontDir;
    }

    /**
     * @param string $imgDir
     */
    public function setImgDir(string $imgDir): void
    {
        $this->imageDir = $imgDir;
    }

    /**
     * @param string $rootDir
     */
    public function setRootDir(string $rootDir): void
    {
        $this->rootDir = $rootDir;
    }

    /**
     * @param string $dictDir
     */
    public function setDictDir(string $dictDir): void
    {
        $this->dictionaryDir = $dictDir;
    }

    private function checkSession(): bool
    {
        if (php_sapi_name() !== 'cli'):
            if (version_compare(phpversion(), '5.4.0', '>=')):
                return session_status() === PHP_SESSION_ACTIVE ? TRUE : FALSE;
            else:
                return session_id() === '' ? FALSE : TRUE;
            endif;
        endif;
        return FALSE;
    }

    private function newCode(): void
    {
        if ($this->checkSession() === FALSE):
            session_name("gmC");
            session_start();
        endif;

        if (isset($_SESSION['in_captcha']) && ! empty($_SESSION['in_captcha'])):
            unset($_SESSION['in_captcha']);
        endif;
    }

    private function randPhrase(int $upper = 2, int $lower = 2, int $numeric = 2, int $other = NULL): string
    {
        $phsWord = '';
        $phsOrder = array();

        if (is_numeric($upper) && intval($upper) > 0):
            for ($i = 0; $i < intval($upper); ++$i):
                $phsOrder[] = chr(rand(65, 90));
            endfor;
        endif;

        if (is_numeric($lower) && intval($lower) > 0):
            for ($i = 0; $i < intval($lower); ++$i):
                $phsOrder[] = chr(rand(97, 122));
            endfor;
        endif;

        if (is_numeric($numeric) && intval($numeric) > 0):
            for ($i = 0; $i < intval($numeric); ++$i):
                $phsOrder[] = chr (rand(48, 57));
            endfor;
        endif;

        if (is_numeric($other) && intval($other) > 0):
            for ($i = 0; $i < intval($other); ++$i):
                $phsOrder[] = chr(rand(33, 47));
            endfor;
        endif;

        if ( ! empty($phsOrder)):
            shuffle($phsOrder);

            foreach ($phsOrder as $char):
                $phsWord .= $char;
            endforeach;
        endif;

        return $phsWord;
    }

    private function createImg(string $code): string
    {
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

        imagefilledrectangle($img, 1, 1, (intval($x) - 2), (intval($y) - 2), $bg);

        for ($i = 0; $i < strlen($code); ++$i):
            $color = $colors[$i % count($colors)];
            imagettftext($img, 20 + rand(0, 8), -20 + rand(0, 30), ($i + 0.3) * $space, 25 + rand(0, 5), $color, $this->fontDir . '/' . $this->defaultFont, $code{$i});
        endfor;

        imagepng($img, $this->imageDir . '/captcha.png', 9);

        $imgfile = $this->imageDir . "/captcha.png";

        $imgbinary = fread(fopen($imgfile, "r"), filesize($imgfile));

        imagedestroy($img);

        @unlink($this->imageDir . '/captcha.png');

        $this->toSession($code);

        return base64_encode($imgbinary);
    }

    private function toSession(string $args = NULL): void
    {
        if (NULL !== $args && ! empty($args)):
            $_SESSION['in_captcha'] = base64_encode($args);
        endif;
    }

    private function getRand(int $enum = NULL): int
    {
        if (is_numeric($enum) && intval($enum) > 0):
            switch ($enum):
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
            endswitch;
            return $iNum;
        endif;

        return FALSE;
    }

    private function checkMath(int $bval = NULL): string
    {
        $selOpr = ( ! isset($this->defaultOperation) OR empty($this->defaultOperation) OR ! is_numeric($this->defaultOperation)) ? 1 : intval($this->defaultOperation);
        $opr = ( ! isset($bval) OR empty($bval) OR NULL === $bval OR ! is_numeric($bval)) ? intval($selOpr) : intval($bval);

        $f_num = $s_num = $res_opr = 0;
        switch ($opr):
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
        endswitch;

        $this->toSession($res_opr);

        return sprintf("%d %s %d",intval($f_num), $op_sign, intval($s_num));
    }

    public function makeGraphic(string $type = 'T', int $sign = 1): string
    {
        switch ($type):
            case "T":
                $code = $this->randPhrase(2,2,2);
                $ibs = $this->createImg($code);
                break;
            case "M":
                $code = $this->checkMath($sign);
                $ibs = $this->createImg($code);
                break;
        endswitch;

        return $ibs;
    }

    public function makeMath(int $sign = 1): string
    {
        return $this->checkMath($sign);
    }

    private function checkEmptyFile(string $pathFile)
    {
        clearstatcache();
        return filesize($pathFile);
    }

    private function readDictionaryFile(string $filec, $delimiter = "\n"): bool
    {
        $fp = fopen($filec, "r");
        if ($fp):
            while (($line = fgets($fp)) !== FALSE):
                $line = trim($line);

                if (strlen($line) >= $this->dictSettings["minWordLength"] && strlen($line) <= $this->dictSettings["maxWordLength"]):
                    $this->dictWords[] = $line;
                endif;
            endwhile;
        endif;

        return (count($this->dictWords) > 0) ? TRUE : FALSE;
    }

    public function loadDictionary(string $dfile = NULL): ?string
    {
        if (NULL !== $dfile && ! empty($dfile)):
            if (file_exists($this->dictionaryDir . "/" . $dfile) !== FALSE && is_readable($this->dictionaryDir . "/" . $dfile) !== FALSE):
                if ($this->checkEmptyFile($this->dictionaryDir . "/" . $dfile) !== FALSE):
                    $dab = $this->readDictionaryFile($this->dictionaryDir . "/" . $dfile);
                    if ($dab !== FALSE):
                        return NULL;
                    else:
                        return "No words in the dictionary met the criteria set";
                    endif;
                else:
                    return "Dictionary file is empty";
                endif;
            else:
                return "Dictionary file not found or not readable";
            endif;
        else:
            return "No file specified as Dictionary";
        endif;
    }

    public function makeFromDictionary(): ?string
    {
        if ( ! empty($this->dictWords)):
            $randKey = array_rand($this->dictWords);
            $code = $this->dictWords[$randKey];
            return $ibs = $this->createImg($code);
        else:
            return NULL;
        endif;
    }

}

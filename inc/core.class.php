<?php
/**
 *  gmCaptcha vers. 0.1
 *  Captcha graphic, mathematical, mixed
 *
 *  © 2014 Gianluigi "A35G"
 *  http://www.hackworld.it/ - http://www.gmcode.it /
 **/

  if (file_exists(dirname(__FILE__).'/config.php'))
    include(dirname(__FILE__).'/config.php');

  class Core {

    var $rpath;

    var $useC = type_cptc;
    var $useOp = math_op;

    private $row_file;

    function __construct() {




    }

    private function countRow($file_dict) {

      if (file_exists($file_dict) && is_readable($file_dict)) {

        clearstatcache();

        $size_file = filesize($file_dict);

        if (!empty($size_file)) {

          $vfile = fopen($file_dict, "r");

          $righe = 0;

          if ($vfile) {

            while (fgets($vfile))
                $righe++;

          }

          fclose($vfile);

          $this->row_file = $righe;

        }

      }

    }

    private function read_line_file($filec, $line_num, $delimiter = "\n") {

      $i = 1;

      $fp = fopen($filec, "r");

      if ($fp) {

        while (!feof($fp)) {

          fseek($fp, ftell($fp));
          $buffer = stream_get_line($fp, 4096, $delimiter);

          if ($i == $line_num)
            return $buffer;

          $i++;
          $buffer = "";

        }

      }

      return false;

    }

    private function getRand($enum) {

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
          $iNum = self::getRand('1');
        break;
      }

      return $iNum;

    }

    private function randPhrase($upper=2, $lower=2, $numeric=2, $other='') {

    	$phsOrder = array();
      $phsWord = '';

    	for ($i = 0; $i < $upper; $i++)
        $phsOrder[] = chr(rand(65, 90));

    	for ($i = 0; $i < $lower; $i++)
        $phsOrder[] = chr(rand(97, 122));

    	for ($i = 0; $i < $numeric; $i++)
        $phsOrder[] = chr (rand(48, 57));

    	for ($i = 0; $i < $other; $i++)
        $phsOrder[] = chr(rand(33, 47));

    	shuffle($phsOrder);

    	foreach ($phsOrder as $char)
        $phsWord .= $char;

    	return $phsWord;

  	}

    private function toSession($args) {
      $_SESSION['in_captcha'] = $args;
    }

    private function checkMath($bval='') {

      if (!isset($this->useOp) || empty($this->useOp) || !is_numeric($this->useOp))
        $selOpr = 1;
      else
        $selOpr = $this->useOp;

      $opr = (!isset($bval) || empty($bval)) ? htmlentities($selOpr) : $bval;

      switch ($opr) {
        case '1':

          $f_num = $this->getRand('2');
          $s_num = $this->getRand('2');
          $op_sign = '+';

          $res_opr = ($f_num + $s_num);

        break;
        case '2':

          $f_num = $this->getRand('3');
          $s_num = $this->getRand('2');
          $op_sign = '-';

          $res_opr = ($f_num - $s_num);

        break;
        case '3':

          $f_num = $this->getRand('2');
          $s_num = $this->getRand('2');
          $op_sign = 'x';

          $res_opr = ($f_num * $s_num);

        break;
        case '4':

          $s_num = $this->getRand('2');
          $f_num = ($s_num * $this->getRand('4'));
          $op_sign = ':';

          $res_opr = ($f_num / $s_num);

        break;
        case '5':

          return $this->checkMath($this->getRand('5'));

        break;
        default:

          $this->checkMath();

        break;
      }

      $this->toSession($res_opr);

      return $f_num.' '.$op_sign.' '.$s_num;

    }

    private function visualImg($code_img) {
      return sprintf("<img src='data:image/png;base64,%s' />", $code_img);
    }

    private function createImg($code) {

      $x = 130;
      $y = 37;

      $space = ($x / (strlen($code) + 1));

      $img = imagecreatetruecolor($x, $y);

      $bg = imagecolorallocate($img, 255, 255, 255);

      $border = imagecolorallocate($img, 0, 0, 0);

      $colors[] = imagecolorallocate($img, 128, 64, 192);
      $colors[] = imagecolorallocate($img, 192, 64, 128);
      $colors[] = imagecolorallocate($img, 108, 192, 64);

      imagefilledrectangle($img, 1, 1, $x-2, $y-2, $bg);

      for ($i = 0; $i < strlen($code); $i++) {

        $color = $colors[$i % count($colors)];

        imagettftext($img, 20 + rand(0, 8), -20 + rand(0, 30), ($i + 0.3) * $space, 25 + rand(0, 5), $color, './font/cheapink.ttf', $code{$i});

      }

      imagepng($img, $this->rpath . 'captcha.png', 9);

      $imgfile = $this->rpath . "captcha.png";

      $imgbinary = fread(fopen($imgfile, "r"), filesize($imgfile));

      imagedestroy($img);

      @unlink($this->rpath . 'captcha.png');

      return /*$code . */$this->visualImg(base64_encode($imgbinary));

    }

    /**
     * Check the type of captcha to show on the site
     * @return Image or String with Captcha created by Script
     */
    private function checkCaptcha() {

      if (!isset($this->useC) || empty($this->useC))
        $this->useC = 2;

      $cpt = htmlentities($this->useC);

      switch($cpt) {
        case '1':
          $code = $this->randPhrase('2', '2', '2', '');
          $ibs = $this->createImg($code);
        break;
        case '2':
          $ibs = $this->checkMath();
        break;
        case '3':
          $code = $this->checkMath();
          $ibs = $this->createImg($code);
        break;
        default:
          $ibs = $this->checkMath();
        break;
      }

      return $ibs;

    }

    private function setVariable() {

      $bsl = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') ? "\\" : "/";

      $path_d = __DIR__;

      $this->rpath = (strlen(__DIR__) != strrpos(__DIR__, $bsl)) ? __DIR__.$bsl : __DIR__;
      $this->rpath = str_replace('inc'.$bsl, '', $this->rpath);

    }

    /**
     * Check if session is started
     * @return [boolean] Status of session in PHP
     */
    private function checkSession() {

      if (php_sapi_name() !== 'cli') {

        if (version_compare(phpversion(), '5.4.0', '>='))
          return session_status() === PHP_SESSION_ACTIVE ? TRUE : FALSE;
        else
          return session_id() === '' ? FALSE : TRUE;

      }

      return FALSE;

    }

    /**
     * Check if session is started otherwise I can start and destroy
     * the session variable for the captcha if it already exists
     */
    private function newCode() {

      if ($this->checkSession() === FALSE) {
        session_name("gmC");
        session_start();
      }

      if (isset($_SESSION['in_captcha']) && !empty($_SESSION['in_captcha']))
        unset($_SESSION['in_captcha']);

    }

    /**
     * Start code to obtain Captcha
     * @return Image or String with Captcha required
     */
    public function initCaptcha() {

      $this->setVariable();
      $this->newCode();

      return $this->checkCaptcha();

    }

  }
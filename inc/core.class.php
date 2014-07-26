<?php
/**
 *  gmCaptcha vers. 0.1
 *  Captcha graphic, mathematical, mixed
 *
 *  © 2014 Gianluigi "A35G"
 *  http://www.hackworld.it/ - http://www.gmcode.it /
 **/

  class Core {

    var $rpath;

    var $useC = type_cptc;
    var $useOp = math_op;

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
          $iNum = mt_rand(1, 99);
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

    private function visMat($arg_f, $arg_s, $arg_t) {
      return sprintf ("%1\$d %3\$s %2\$d", $arg_f, $arg_s, $arg_t);
    }

    private function getAddition() {

      $f_num = $this->getRand('2');
      $s_num = $this->getRand('2');

      $res_opr = ($f_num + $s_num);

      $this->toSession($res_opr);

      return $this->visMat($f_num, $s_num, "+");
    }

    private function getSubtraction() {

      $f_num = $this->getRand('3');
      $s_num = $this->getRand('2');

      $res_opr = ($f_num - $s_num);

      $this->toSession($res_opr);

      return $this->visMat($f_num, $s_num, "-");
    }

    private function getMultiplication() {

      $f_num = $this->getRand('2');
      $s_num = $this->getRand('2');

      $res_opr = ($f_num * $s_num);

      $this->toSession($res_opr);

      return $this->visMat($f_num, $s_num, "x");
    }

    private function getDivision() {

      $s_num = $this->getRand('2');
      $f_num = ($s_num * $this->getRand('4'));

      $res_opr = ($f_num / $s_num);

      $this->toSession($res_opr);

      return $this->visMat($f_num, $s_num, ":");
    }

    private function checkMath() {

      if (!isset($this->useOp) || empty($this->useOp))
        $this->useOp = 1;

      $opr = htmlentities($this->useOp);

      switch ($opr) {
        case '1':
          $visual_captcha = $this->getAddition();
        break;
        case '2':
          $visual_captcha = $this->getSubtraction();
        break;
        case '3':
          $visual_captcha = $this->getMultiplication();
        break;
        case '4':
          $visual_captcha = $this->getDivision();
        break;
        case '5':
          $this->useOp = $this->getRand('5');
          $visual_captcha = self::checkMath();
        break;
        default:
          $visual_captcha = $this->getAddition();
        break;
      }

      return $visual_captcha;

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

        imagettftext($img, 20 + rand(0, 8), -20 + rand(0, 30), ($i + 0.3) * $space, 25 + rand(0, 5), $color, './font/carbon.ttf', $code{$i});

      }

      imagepng($img, $this->rpath . 'captcha.png', 9);

      $imgfile = $this->rpath . "captcha.png";

      $imgbinary = fread(fopen($imgfile, "r"), filesize($imgfile));

      imagedestroy($img);

      @unlink($this->rpath . 'captcha.png');

      return $code . $this->visualImg(base64_encode($imgbinary));

    }

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
          $ibs = "mixed";
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

    public function initCaptcha() {

      $this->setVariable();

      return $this->checkCaptcha();

    }

  }
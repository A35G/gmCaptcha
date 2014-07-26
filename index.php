<?php
session_name("gmC");
session_start();

  if (isset($_SESSION['in_captcha']) && !empty($_SESSION['in_captcha'])) {

  	$_SESSION['in_captcha'] = "";
  	unset($_SESSION['in_captcha']);

  }

  include("./inc/config.php");
  echo $bard->initCaptcha();
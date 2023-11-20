<?php

require_once './vendor/autoload.php';
use App\Core\Core;
$captcha = new Core();

$captcha->makeGraphic("T");

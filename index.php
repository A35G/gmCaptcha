<?php

require_once 'inc/Core.php';
use gmCaptcha\Core;

$gmc = new Core();
$output = $gmc->makeGraphic("T");
echo sprintf("<img src='data:image/png;base64,%s' />", $output);

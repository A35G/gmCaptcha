<?php

require_once 'inc/Core.php';
use gmCaptcha\Core;

$gmc = new Core;

if (NULL !== $output):
	$output = $gmc->makeGraphic("T");
	echo sprintf("<img src='data:image/png;base64,%s' />", $output);
else:
	echo "GD not even installed.";
	exit;
endif;

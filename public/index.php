<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

require_once realpath(__DIR__ . "/../vendor/autoload.php");

use App\Core\Core;
$gmc = new Core();

if (isset($_POST) && array_key_exists("tps",$_POST)) {
    $ug = htmlspecialchars($_POST["tps"]);
    if (!empty(trim($ug))) {
        switch($ug) {
            case "text":
                $us = (is_numeric($_POST['csm']) 
                    && !empty($_POST['csm'])) 
                ? intval($_POST['csm']) 
                : Core::$config['appOperation'];

                echo Core::makeMath($us);
                exit();
                break;
            case "graphic":
                $ds = json_encode(array(
                    "style" =>  "text"
                ));

                if (isset($_POST['csm']) && !empty($_POST['csm'])) {
                    if (Core::checkIsValidJSON($_POST["csm"])) {
                        $ds = $_POST["csm"];
                    }
                }

                echo Core::makeFull($ds);
                exit();
                break;
            case "sound":
                echo Core::callDataSound();
                exit();
                break;
        }
    }
}

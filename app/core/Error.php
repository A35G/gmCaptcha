<?php

namespace App\Error;

class Error
{
    private static $makeErrorFile = false;

    private static $logDir;

    public function __construct() {
        $appDir = realpath(__DIR__ . "/../");
        self::setLogDir($appDir . "/logs");
    }

    private static function setLogDir(string $path): void {
        if (file_exists($path) && is_dir($path)) {
            self::$logDir = $path;
            self::checkPermission();
        }
    }

    private static function checkPermission(): void
    {
        if (is_writable(self::$logDir)) {
            self::$makeErrorFile = true;
        }
    }

    public static function logError(string $textError) {
        if (self::$makeErrorFile) {
            $logFile = "log-" . date("Y-m-d") . ".php";

            $dataError = "[client " . $_SERVER['REMOTE_ADDR'] . "] ERROR: " .
             $textError;

            file_put_contents(self::$logDir . "/" . $logFile, $dataError, 
                FILE_APPEND | LOCK_EX);
        }
    }
}

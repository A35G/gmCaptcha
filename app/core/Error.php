<?php

namespace App\Error;

class Error
{
    private $makeErrorFile = false;

    private $logDir;

    public function __construct() {
        $appDir = realpath(__DIR__ . "/../");
        $this->setLogDir($appDir . "/logs");

        $this->checkPermission();
    }

    private function setLogDir(string $path): void {
        if (file_exists($path) && is_dir($path)) {
            $this->logDir = $path;
        }
    }

    private function checkPermission(): void
    {
        if (is_writable($this->logDir)) {
            $this->makeErrorFile = true;
        }
    }

    public function logError(string $textError) {
        if ($this->makeErrorFile) {
            $logFile = "log-" . date("Y-m-d") . ".php";

            $dataError = "[client " . $_SERVER['REMOTE_ADDR'] . "] ERROR: " .
             $textError;

            file_put_contents($this->logDir . "/" . $logFile, $dataError, 
                FILE_APPEND | LOCK_EX);
        }
    }
}

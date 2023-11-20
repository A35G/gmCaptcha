<?php

namespace App\Book;

use App\Core\Core;
use App\Error\Error;

class Words
{
    /**
     * The root of gmCaptcha files
     *
     * @var string
     */
    private $rootDir;

    /**
     * The root of gmCaptcha dictionary files
     *
     * @var string
     */
    private $dictionaryDir;

    /**
     * List of dictionary words
     * 
     * @var array
     */
    private $dictWords = array();

    public function __construct() {
        $rootDir = realpath(__DIR__ . "/../../");
        $this->setRootDir($rootDir);

        $this->setDictDir($rootDir . "/public/dictionary");

        $this->checkUseDictionary();
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

    private function checkEmptyFile(string $pathFile)
    {
        clearstatcache();
        return filesize($pathFile);
    }

    private function readDictionaryFile(array $dct, $delimiter = "\n"): bool
    {
        $filec = $this->dictionaryDir . "/" . $dct["appDictionaryFile"];
        $minWord = $dct["appDictionarySettings"]["minWordLength"];
        $maxWord = $dct["appDictionarySettings"]["maxWordLength"];

        $fp = fopen($filec, "r");
        if ($fp) {
            while (($line = fgets($fp)) !== false) {
                $line = trim($line);

                if (strlen($line) >= $minWord && strlen($line) <= $maxWord) {
                    $this->dictWords[] = $line;
                }
            }
        }

        return (count($this->dictWords) > 0) ? true : false;
    }

    public function loadDictionary(array $dct): void
    {
        if (is_readable($this->dictionaryDir . "/" . $dct["appDictionaryFile"]) === false) {
            $err = new Error();
            $err->logError("Dictionary file not found or not readable");
            exit(0);
        }
        
        if ($this->checkEmptyFile($this->dictionaryDir . "/" . $dct["appDictionaryFile"]) === false) {
            $err = new Error();
            $err->logError("Dictionary file is empty");
            exit(0);
        }

        $dab = $this->readDictionaryFile($dct);
        if ($dab === false) {
            $err = new Error();
            $err->logError("No words in the dictionary met the criteria set");
            exit(0);
        }
    }

    private function checkUseDictionary()
    {
        $c = new Core();
        $cn = $c->config;
        if (array_key_exists("appUseDictionary", $cn) !== false
            && $cn["appUseDictionary"] === true) {
            if (array_key_exists("appDictionaryFile",$cn) === false) {
                $err = new Error();
                $err->logError("No file specified as Dictionary");
                exit(0);
            }
            
            if (empty($cn["appDictionaryFile"])) {
                $err = new Error();
                $err->logError("No file specified as Dictionary");
                exit(0);
            }
            
            $this->loadDictionary($cn);
        }
    }

    public function makeFromDictionary()
    {
        if (!empty($this->dictWords)) {
            $randKey = array_rand($this->dictWords);
            $code = $this->dictWords[$randKey];

            $c = new Core();
            $dataImg = $c->createImg($code);

            if (isset($dataImg) && !empty($dataImg)) {
                include $this->rootDir . "/public/view/captchaGraphic.php";
            }
        }
    }

}

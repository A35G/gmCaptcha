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
    private static $rootDir;

    /**
     * The root of gmCaptcha dictionary files
     *
     * @var string
     */
    private static $dictionaryDir;

    /**
     * List of dictionary words
     * 
     * @var array
     */
    private static $dictWords = array();

    private static $e;

    public function __construct() {
        $rootDir = realpath(__DIR__ . "/../../");
        self::setRootDir($rootDir);

        self::setDictDir($rootDir . "/public/dictionary");

        self::$e = new Error();

        self::checkUseDictionary();
    }

    /**
     * @param string $rootDir
     */
    private static function setRootDir(string $rootDir): void
    {
        self::$rootDir = $rootDir;
    }

    /**
     * @param string $dictDir
     */
    private static function setDictDir(string $dictDir): void
    {
        self::$dictionaryDir = $dictDir;
    }

    private static function checkEmptyFile(string $pathFile)
    {
        clearstatcache();
        return filesize($pathFile);
    }

    private static function readDictionaryFile(
        array $dct,
        string $delimiter = "\n"
    ): bool {
        $filec = self::$dictionaryDir . "/" . $dct["appDictionaryFile"];
        $minWord = $dct["appDictionarySettings"]["minWordLength"];
        $maxWord = $dct["appDictionarySettings"]["maxWordLength"];

        $fp = fopen($filec, "r");
        if ($fp) {
            while (($line = fgets($fp)) !== false) {
                $line = trim($line);

                if (strlen($line) >= $minWord && strlen($line) <= $maxWord) {
                    self::$dictWords[] = $line;
                }
            }
        }

        return (count(self::$dictWords) > 0) ? true : false;
    }

    private static function loadDictionary(array $dct): void
    {
        $dfile = self::$dictionaryDir . "/" . $dct["appDictionaryFile"];
        if (is_readable($dfile) === false) {
            self::$e->logError("Dictionary file not found or not readable");
            exit(0);
        }
        
        if (self::checkEmptyFile($dfile) === false) {
            self::$e->logError("Dictionary file is empty");
            exit(0);
        }

        $dab = self::readDictionaryFile($dct);
        if ($dab === false) {
            self::$e->logError("No words in the dictionary met the criteria set");
            exit(0);
        }
    }

    private static function checkUseDictionary()
    {
        $cn = Core::$config;
        if (array_key_exists("appUseDictionary", $cn) !== false
            && $cn["appUseDictionary"] === true) {
            if (array_key_exists("appDictionaryFile",$cn) === false) {
                self::$e->logError("No file specified as Dictionary");
                exit(0);
            }
            
            if (empty($cn["appDictionaryFile"])) {
                self::$e->logError("No file specified as Dictionary");
                exit(0);
            }
            
            self::loadDictionary($cn);
        }
    }

    public static function makeFromDictionary()
    {
        if (!empty(self::$dictWords)) {
            $randKey = array_rand(self::$dictWords);
            $code = self::$dictWords[$randKey];

            $dataImg = Core::createImg($code);

            return $dataImg;
        }
    }

}

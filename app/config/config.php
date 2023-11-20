<?php

//  Default Captcha Font
$config["appFont"] = "cheapink.ttf";

//  Default Captcha Mathematical Operation
#
#   Code for Mathematical operations
#   -   1: Addition;
#   -   2: Subtraction;
#   -   3: Multiplication;
#   -   4: Division;
#   -   5: Random.
$config["appOperation"] = 1;

//  Dictionary
$config["appUseDictionary"] = TRUE;
$config["appDictionaryFile"] = "1.1million word list.txt";
$config["appDictionarySettings"]["minWordLength"] = 4;
$config["appDictionarySettings"]["maxWordLength"] = 6;

//  Default Session Variable Name
$config["appSessionVariable"] = "inCaptcha";

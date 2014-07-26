<?php
/**
 *  gmCaptcha vers. 0.1
 *  Captcha graphic, mathematical, mixed
 *
 *  © 2014 Gianluigi "A35G"
 *  http://www.hackworld.it - http://www.gmcode.it
 **/
# Set Type of Captcha
#
# 1: Captcha graphic;
# 2: Captcha mathematical;
# 3: Captha mixed = graphic and mathematical.
#
# If variable not set or empty, Captcha mathematical is default.
  define("type_cptc", "1");
# Set Type of mathematical operation
#
# 1: Addition;
# 2: Subtraction;
# 3: Multiplication;
# 4: Division;
# 5: Random;
#
# If variable not set or empty, the Type of mathematical operation is Addition to default.
  define("math_op", "3");
#
# Init Script
  include("./inc/core.class.php");
  $bard = new Core;
# gmCaptcha

A small and simple Proof-of-Concept of Captcha (graphical and mathematical) for use in a form generic or in contacts form.

# Usage

- Include **Core** Class from *inc* directory;
  ```php
    require_once 'inc/Core.php';
    use gmCaptcha\Core;
  ```

- Make new instance
    ```php
    $gmc = new Core();
    ```

- Call the type of desiderated Captcha:
  
  -  **Graphical Captcha - Random Text**
    ```php
    $output = $gmc->makeGraphic("T");
    echo sprintf("<img src='data:image/png;base64,%s' />", $output);
    ```

    **Example**
  
    <img src="./screenshots/Graphical Captcha - Random Text.png" width="15%"/>

    <br /><br />
  
  -  **Graphical Captcha - Mathematical operation**
    ```php
    $output = $gmc->makeGraphic("M");
    echo sprintf("<img src='data:image/png;base64,%s' />", $output);
    ```

    **Example**
  
    <img src="./screenshots/Graphical Captcha - Mathematical Operation.png" width="15%"/>

    <br /><br />
    
  -  **Graphical Captcha - Mathematical operation with sign specification**
    ```php
    $output = $gmc->makeGraphic("M",3);
    echo sprintf("<img src='data:image/png;base64,%s' />", $output);
    ```

    **Example**
  
    <img src="./screenshots/Graphical Captcha - Mathematical operation with specified.png" width="15%"/>

    <br /><br />
  
  -  **Textual Captcha - Mathematical Operation**
    ```php
    echo $gmc->makeMath();
    ```

    **Example**
  
    <img src="./screenshots/Textual Captcha - Mathematical Operation.png" width="5%"/>

    <br /><br />
  
  -  **Textual Captcha - Mathematical Operation with sign specification**
    ```php
    echo $gmc->makeMath(4);
    ```

    **Example**
  
    <img src="./screenshots/Textual Captcha - Mathematical operation with specified.png" width="5%"/>

    <br /><br />
  
  -  **Graphical Captcha - Text from Dictionary**
    ```php
    $output = $gmc->makeFromDictionary();
    if (NULL !== $output):
      echo sprintf("<img src='data:image/png;base64,%s' />", $output);
    endif;
    ```

    **Example**
  
    <img src="./screenshots/Captcha-Dictionary.png" width="15%"/>

# Features

- **Font customization** for graphical Captchas through the enhancement of the variable `$defaultFont` within the **Core** class;
- Choose whether or not to use a text file as a dictionary and preload its contents via the variables `$useDictionary` and `$dictionaryFile` whithin the **Core** class;
- Specify the type of operation to be used in mathematical Captchas:
  - `1` for Sum;
  - `2` for Subtraction;
  - `3` for Multiplication;
  - `4` for Division;
  - `5` for Random
- Specify the minimum and maximum length of words to be taken from the dictionary:
  - `$dictSettings['minWordLength']`
  - `$dictSettings['maxWordLength']`

- The result of mathematical operations and the text of graphical Captchas, is contained within the session variable `$_SESSION['in_captcha']`

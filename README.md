# gmCaptcha

A small and simple Proof-of-Concept of Captcha (graphical and mathematical) for use in a form generic or in contacts form.

# Usage

- Install [Composer](https://getcomposer.org/ "Composer's Homepage")

- Run the following command at the root level of your project:
  ```
  composer install
  ```
- Include **gmCaptcha.js** (or ***gmCaptcha.min.js***) file from "public/assets/js" directory
  ```html
  <script src="./public/assets/js/gmCaptcha.js" type="text/javascript"></script>
  ```

- Make new instance
    ```html
    <script type="text/javascript">
    new gmCaptcha()
    </script>
    ```

- Call the type of desiderated Captcha:

  Type | Call | Example
  --- | --- | ---
  Graphical Captcha - *Mathematical operation* | `new gmCaptcha({extra: {style: "math"}})` | <img src="./screenshots/Graphical Captcha - Mathematical Operation.png" />
  Graphical Captcha - *Mathematical operation with sign specification* | `new gmCaptcha({extra: {style: "math",custom: 3}})` | <img src="./screenshots/Graphical Captcha - Mathematical operation with specified.png" />
  Textual Captcha - *Mathematical Operation* | `new gmCaptcha({type: "text"})` | <img src="./screenshots/Textual Captcha - Mathematical Operation.png" />
  Textual Captcha - *Mathematical Operation with sign specification* | `new gmCaptcha({type: "text",extra: {custom: 4}})` | <img src="./screenshots/Textual Captcha - Mathematical operation with specified.png" />

# Features

- **Font customization** for graphical Captchas by setting the value of the `appFont` key within the **config** file;
- Specify the type of operation to be used in mathematical Captchas:
  - `1` for Sum;
  - `2` for Subtraction;
  - `3` for Multiplication;
  - `4` for Division;
  - `5` for Random
- Choose whether or not to use a text file as a dictionary and preload its contents by setting the value of the `appUseDictionary` and `appDictionaryFile` keys whithin the **config** file;
- Specify the minimum and maximum length of words to be taken from the dictionary:
  - `['appDictionarySettings']['minWordLength']`
  - `['appDictionarySettings']['maxWordLength']`

- If you choose not to use the Dictionary, the script will generate Captcha with random text.

- The result of mathematical operations and the text of graphical Captchas are contained in a session variable; its name can be customized by enhancing the contents of the `appSessionVariable` key whithin the **config** file.

-	TextToSpeech (*It is only present in the graphical version of the Captcha* and you can customize the language via the following parameter):
    ```html
    <script type="text/javascript">
    new gmCaptcha({spellLang: "en-US"})
    </script>
    ```

-	Refresh graphical captchas (*It is only present in the graphical version of the Captcha*);
-	Error logger.
---
> [!NOTE]
> There are several types of Captcha online, some famous, some less so, all certainly strong and functional.
> 
> Why then write "perhaps" useless code?
> 
> To "find out how it works," to learn new things, to improve, to feed on knowledge.
> 
> Any suggestion, comment or advice, is welcome.

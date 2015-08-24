<?php
/**
 * Created by PhpStorm.
 * User: Papp
 * Date: 2015.05.30.
 * Time: 19:45
 */

namespace System\Helper;



use System\StdLib\MvcEvent\Router;

class Translator extends Singleton {

    private $textdomain;
    private $availableLangs = array( "hu", "en");
    private $defaultLang = "hu";


    public function __construct() {



    }


    public function setLocale($locale) {
        if (in_array($locale, $this->availableLangs)) {
            $this->locale = $locale;
        } else $this->locale = $this->defaultLang;

        putenv('LANG='.$locale);
        setlocale(LC_ALL, $locale);
        $this->init();
    }

    public function init() {
        $config = Config::getInstance()->get('translator');
        $module = Router::getInstance()->getModule();
        $dir = $config['module'][$module]['directory'];

        $content = file_get_contents($dir.$this->locale.".po");

        preg_match_all('$msgid "[^"]*$', $content, $keys);
        foreach ($keys[0] as $key) {
            $Keys[] = rtrim(substr($key, 7), "\"");
        }


        // array_shift($Keys); // get the first item from the header

        preg_match_all('$msgstr "[^"]*"$', $content, $values);
        array_shift($Keys);
        array_shift($values[0]);
        array_walk($values[0], function(&$key) {
            $key = rtrim($key,"\"");
            $key = substr($key, strlen("msgstr ")+1, strlen($key));
        });
        $this->textdomain = array_combine($Keys, $values[0]);
    }

    public function translate($key) {
        if (array_key_exists($key, $this->textdomain)) {

            return $this->textdomain[$key] != false ? $this->textdomain[$key] : $key;
        } else return $key;

    }


}
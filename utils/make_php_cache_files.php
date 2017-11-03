<?php

  /*
  * Parses twig templates and converts them into a single file that can be
  * understood by gettext
  * Uses siteconfig with default config file pointing at app/config.php
  *
  * In this example the twig configurations are loaded via a method in twig_cache_configs.php.
  *
  * You can also assign twig configurations directly like this:
  * $twig_configs = array(
  * "extensions" => [
  *   new Twig_Extensions_Extension_I18n() //i18n extension
  *  ],
  * "filters" => [
  *     //translation filter
  *     new Twig_SimpleFilter("translate", function($stdClassObject) {
  *       return null;
  *    })
  *   ]
  * );
  *
  */

  // Autoload
  require __DIR__.'/../vendor/autoload.php';

  require 'TwigConfigLoader.php';

  if (!defined('CONFIG_FILE')) {
      define("CONFIG_FILE", __DIR__ . "/../app/config.php");
  }

  \Fccn\Lib\TwigParser::parse(new TwigConfigLoader());

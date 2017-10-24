<?php

/*
* Provides a set of configuration loaders
*/

namespace Fccn\Lib;

class ConfigurationLoader
{

/*
* Loads the required extensions and filters to use
* the localization utilities with twig
*/
public static function loadTwigTranslationConfigs($twig){
  //i18n extension
  $twig->addExtension(new Twig_Extensions_Extension_I18n());
  $filter = new Twig_SimpleFilter("translate", function($stdClassObject) {
    return null;
  }),
  $twig->addFilter($filter);
}

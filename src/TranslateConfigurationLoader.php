<?php

/*
* Provides a set of configurations to be loaded by external tools.
*
* Twig filters and extensions
*/

namespace Fccn\Lib;

class TranslateConfigurationLoader
{

  /*
  * Loads the required extensions and filters to use
  * the localization utilities with twig
  */
  public static function loadTwigConfigs($twig)
  {
    //i18n extension
    $twig->addExtension(new \Twig_Extensions_Extension_I18n());
    $filter = new \Twig_SimpleFilter("translate", function($stdClassObject) {
      return null;
    });
    $twig->addFilter($filter);
  }

}

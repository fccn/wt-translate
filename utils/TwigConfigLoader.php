<?php
/*
 * Loads filters and extensions to twig. Use when generating cache files
 * for gettext translations.
 *
 * You can add the filters and extensions directly to to twig.
 * To add an extension:
 *  $twig->addExtension(new Twig_Extensions_Extension_I18n());
 * To add a filter:
 *    $filter = new Twig_SimpleFilter("translate", function($stdClassObject) {
 *    return null;
 *  }),
 * $twig->addFilter($filter);
 *
 * You can also call preset configuration loaders like the one defined by
 * \Fccn\Lib\TranslateConfigurationLoader
*/

class TwigConfigLoader
{
    /* invoques twig configuration loaders */
    public function loadConfigs($twig)
    {
        \Fccn\Lib\TranslateConfigurationLoader::loadTwigConfigs($twig);
        //additional loaders below...
    }
}

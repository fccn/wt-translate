<?php
/*
 * Add configurations to $twig variable according to what is configured in the main site.
 *
 *
 * You can add content below according to extensions and filters added to twig.
 *
 * i.e to add extensions do:
 *  $twig->addExtension(new Twig_Extensions_Extension_I18n());
 *  $twig->addExtension(new \Slim\Views\TwigExtension());
 *  $twig->addExtension(new \JSW\Twig\TwigExtension());
 *  $twig->addExtension(new Twig_Extension_Debug());
 *
 * i.e to add filters do:
 * $filter  = new Twig_SimpleFilter("cast_to_array", function($stdClassObject) {
 *   return null;
 * });
 * $twig->addFilter($filter);
*/
//add i18n extension
$twig->addExtension(new Twig_Extensions_Extension_I18n());

//add translation filter
$filter  = new Twig_SimpleFilter("translate", function($stdClassObject) {
  return null;
});
$twig->addFilter($filter);

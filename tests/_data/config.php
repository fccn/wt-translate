<?php

# test config file for webapp_skeleton tools - translate

$c = array(

  'base_path' => "",
#------ logfile configuration
  'logfile_path' => __DIR__."/../_output/logs/test.log",
  'logfile_level' => "DEBUG",
#----- locale configuration

  "defaultLocale"      => "pt_PT",
  "defaultLocaleLabel" => "PT",
  "locale_selection"   => "param",

  "locales"            => array(
                            array("label" => "EN", "locale" => "en_GB", "flag_alt" => "English flag", "language" => "English"),
                            array("label" => "PT", "locale" => "pt_PT", "flag_alt" => "Portuguese flag", "language" => "Português"),
                            # add other languages here....
                          ),

  "locale_textdomain"  => "messages",
  "locale_path"        => __DIR__."/locale",
  "locale_cookie_name" => "locale",
  "locale_cookie_path" => "/",
  "locale_param_name" => "lang",
  "request_attribute_name" => "locale",

  #twig parser locations
  "twig_parser_templates_path" => __DIR__."/templates",
  "twig_parser_cache_path" => __DIR__."/cache"
);

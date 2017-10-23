<?php

$c = array(
#------ logfile configuration
  'logfile_path' => __DIR__."/../_output/logs/test.log",
  'logfile_level' => "DEBUG",
#----- locale configuration
  "defaultLocale"      => "pt_PT",
  "defaultLocaleLabel" => "PT",

  "locales"            => array(
                            array("label" => "GB", "locale" => "en_GB", "flag_alt" => "English flag", "language" => "English"),
                            array("label" => "PT", "locale" => "pt_PT", "flag_alt" => "Portuguese flag", "language" => "Português"),
                            # add other languages here....
                          ),

  "locale_textdomain"  => "messages",
  "locale_path"        => __DIR__."/locale",
  "locale_cookie_name" => "locale",
  "localeCookieName" => "locale",
  "locale_cache_template_path" => __DIR__."/templates",
  "locale_cache_path" => __DIR__."/cache"
);

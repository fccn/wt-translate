<?php

$c = array(
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
));

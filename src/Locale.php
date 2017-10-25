<?php

/*
* Localization utilities
*
*/

namespace Fccn\Lib;

class Locale
{

  public static function getLocaleFromLabel($label) {

    foreach(SiteConfig::getInstance()->get("locales") as $locale) {
      if (strtoupper($label) == strtoupper($locale["label"])) {
        return $locale["locale"];
      }
    }

    return SiteConfig::getInstance()->get("defaultLocale");
  }

  static public function getLabelFromLocale($localeX) {

    foreach(SiteConfig::getInstance()->get("locales") as $locale) {
      if (strtoupper($localeX) == strtoupper($locale["locale"])) {
        return $locale["label"];
      }
    }

    return self::getLabelFromLocale(SiteConfig::getInstance()->get("defaultLocaleLabel"));
  }

  public function __construct(){

  }

  /*
  * Initializes the locale and configures Gettext
  */
  public function init()
  {
    $current_lang = $this->getCurrentLang();
    FileLogger::debug('Locale::init() - current lang is '.$current_lang);
    // Set language to Current Language
    $results = putenv('LANG=' . $current_lang . ".utf8");
    if(!$results)
    {
      FileLogger::error("Locale::init() - putenv failed");
    }
    $results = setlocale(LC_MESSAGES, $current_lang);
    if(!$results)
    {
      FileLogger::error("Locale::init() - setlocale failed: locale function is not available on this platform, or the given local does not exist in this environment");
    }

    // Specify the location of the translation tables
    $results = bindtextdomain(SiteConfig::getInstance()->get("locale_textdomain"), SiteConfig::getInstance()->get("locale_path"));
    FileLogger::debug("Locale::init() - new text domain is set: $results");
    $results = bind_textdomain_codeset(SiteConfig::getInstance()->get("locale_textdomain"), 'UTF-8');
    FileLogger::debug("Locale::init() - new text domain codeset is: $results");

    // Choose domain
    $results = textdomain(SiteConfig::getInstance()->get("locale_textdomain"));
    FileLogger::debug("Locale::init() - current message domain is set: $results");
  }

  public function getCurrentLang()
  {
    $current_lang = "";
    //try getting default language from cookie
    if(isset($_COOKIE[SiteConfig::getInstance()->get("locale_cookie_name")])){
      $current_lang = $_COOKIE[SiteConfig::getInstance()->get("locale_cookie_name")];
    }

    if (empty($current_lang)) {
      //try getting default language from browser
      $defaultLocaleLabel = null;
      if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
        $labelFromHTTP = strtoupper(substr(locale_accept_from_http($_SERVER['HTTP_ACCEPT_LANGUAGE']), 0, 2));

        foreach(SiteConfig::getInstance()->get("locales") as $locale) {
          if ($labelFromHTTP == $locale["label"]) {
            $defaultLocaleLabel = $labelFromHTTP;
          }
        }
      }

      if ($defaultLocaleLabel == null) {
        //fallback to default locale on config
        $defaultLocaleLabel = SiteConfig::getInstance()->get("defaultLocaleLabel");
      }
      $current_lang = self::getLocaleFromLabel($defaultLocaleLabel);
    }

    return $current_lang;
  }

  public function existsHtmlContent($id)
  {
    $current_lang = $this->getCurrentLang();

    $filename = SiteConfig::getInstance()->get("locale_path") . "/" . $current_lang . "/html/" . $id . ".html";

    return file_exists($filename);
  }

  public function getHtmlContent($id)
  {
    $current_lang = $this->getCurrentLang();

    $filename = SiteConfig::getInstance()->get("locale_path") . "/" . $current_lang . "/html/" . $id . ".html";

    if (file_exists($filename)) {
      return file_get_contents($filename);
    } else {
      FileLogger::getInstance()->error("Locale::getHtmlContenthtml - file not found: $filename");
      return ""; //_("File not found:") . " <b>" . $filename . "</b>";
    }
  }

  static public function existsFileContent($id)
  {
    $current_lang = self::getCurrentLang();

    $filename = SiteConfig::getInstance()->get("locale_path") . "/" . $current_lang . "/files/" . $id . ".txt";

    return file_exists($filename);
  }

  static public function getFileContent($id)
  {
    $current_lang = self::getCurrentLang();

    $filename = SiteConfig::getInstance()->get("locale_path") . "/" . $current_lang . "/files/" . $id . ".txt";

    if (file_exists($filename)) {
      return file_get_contents($filename);
    } else {
      FileLogger::error("Locale::getFileContent - file not found - $filename");
      return ""; //_("File not found:") . " " . $filename;
    }
  }

  static public function processFile($tag, $replace_by = null)
  {
    $return_file = self::getFileContent($tag);
    if (is_array($replace_by)) {
      $return_file = str_replace(array_keys($replace_by), array_values($replace_by), $return_file);
    }

    return $return_file;
  }

}

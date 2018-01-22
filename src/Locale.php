<?php

/*
* Provides localization utilities
*
* * since v1.0.5
* - enabled options param.
* - support for Slim 3 middleware operation
* - added options
*    - slim_middleware - boolean - if true adds support for Slim 3 middleware
* - getLocaleFromLabel and getLabelFromLocale static functions return false if not found. Fallback to default only if requested.
*
*
*/

namespace Fccn\Lib;

class Locale
{
    /* holds current locale to be changed via middleware */
    private $current_lang;
    private $use_slim_middleware;

    /*
    * return locale from label or false if not found
    * if default=true than returns default if not found
    */
    public static function getLocaleFromLabel($label, $default=false)
    {
        foreach (SiteConfig::getInstance()->get("locales") as $locale) {
            if (strtoupper($label) == strtoupper($locale["label"])) {
                return $locale["locale"];
            }
        }

        if ($default) {
            return self::getDefaultLocale();
        }
        return false;
    }

    /*
    * return label from $localeX or false if not found
    * if default=true than returns default if not found
    */
    public static function getLabelFromLocale($localeX)
    {
        foreach (SiteConfig::getInstance()->get("locales") as $locale) {
            if (strtoupper($localeX) == strtoupper($locale["locale"])) {
                return $locale["label"];
            }
        }
        if ($default) {
            return self::getDefaultLocale();
        }
        return false;
    }

    /*
    * Checks if provided $locale_txt is valid, returns locale if true, false if not
    */
    public static function getLocale($locale_txt)
    {
        foreach (SiteConfig::getInstance()->get("locales") as $locale) {
            if (strtoupper($locale_txt) == strtoupper($locale["locale"])) {
                return $locale["locale"];
            }
        }
        return false;
    }

    public static function getDefaultLocale()
    {
        return SiteConfig::getInstance()->get("defaultLocale");
    }

    /*
    * Creates a new Locale
    */
    public function __construct($options = array())
    {
        $use_slim_middleware = false;
        if (!empty($options) && !empty($options['slim_middleware'])) {
            $this->use_slim_middleware = true;
            //set current lang to default
            $this->current_lang = self::getLocaleFromLabel(SiteConfig::getInstance()->get("defaultLocaleLabel"));
        }
    }

    /*
    * Initializes the locale and configures Gettext
    */
    public function init()
    {
        $current_lang = $this->getCurrentLang();
        $locale = $current_lang.".utf8";
        FileLogger::debug('Locale::init() - current locale is '.$locale);
        // Set language to Current Language
        $results = putenv('LANG=' . $locale);
        if (!$results) {
            FileLogger::error("Locale::init() - putenv failed");
        }
        $results = setlocale(LC_MESSAGES, $current_lang);
        if (!$results) {
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

    /*
    * Tries to obtain current language via cookie
    * if middleware is active then returns stored current language
    * Returns default locale if no current language is not set
    */
    public function getCurrentLang()
    {
        if ($this->use_slim_middleware) {
            if (!empty($this->current_lang)) {
                return $this->current_lang;
            }
            //return default
            return self::getLocaleFromLabel(SiteConfig::getInstance()->get("defaultLocaleLabel"));
        }
        $current_lang = "";
        //try getting default language from cookie
        if (isset($_COOKIE[SiteConfig::getInstance()->get("locale_cookie_name")])) {
            $current_lang = $_COOKIE[SiteConfig::getInstance()->get("locale_cookie_name")];
        }

        if (empty($current_lang)) {
            //try getting default language from browser
            $defaultLocaleLabel = null;
            if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
                $labelFromHTTP = strtoupper(substr(locale_accept_from_http($_SERVER['HTTP_ACCEPT_LANGUAGE']), 0, 2));
                foreach (SiteConfig::getInstance()->get("locales") as $locale) {
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

    /*
    * sets the current language parameter used when slim middleware support is active
    * verifies if param is on the list of current languages
    */
    public function setCurrentLang($current_lang)
    {
        if (!empty($current_lang)) {
            foreach (SiteConfig::getInstance()->get("locales") as $locale) {
                if (strtoupper($current_lang) == strtoupper($locale["locale"])) {
                    $this->current_lang = $locale["locale"];
                    break;
                }
            }
        }
        if (empty($this->current_lang)) {
            $this->current_lang = self::getLocaleFromLabel(SiteConfig::getInstance()->get("defaultLocaleLabel"));
        }
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
            FileLogger::error("Locale::getHtmlContenthtml - file not found: $filename");
            return ""; //_("File not found:") . " <b>" . $filename . "</b>";
        }
    }

    public function existsFileContent($id)
    {
        $current_lang = $this->getCurrentLang();
        $filename = SiteConfig::getInstance()->get("locale_path") . "/" . $current_lang . "/files/" . $id . ".txt";
        return file_exists($filename);
    }

    public function getFileContent($id)
    {
        $current_lang = $this->getCurrentLang();
        $filename = SiteConfig::getInstance()->get("locale_path") . "/" . $current_lang . "/files/" . $id . ".txt";
        if (file_exists($filename)) {
            return file_get_contents($filename);
        } else {
            FileLogger::error("Locale::getFileContent - file not found - $filename");
            return ""; //_("File not found:") . " " . $filename;
        }
    }

    public function processFile($tag, $replace_by = null)
    {
        $return_file = $this->getFileContent($tag);
        if (is_array($replace_by)) {
            $return_file = str_replace(array_keys($replace_by), array_values($replace_by), $return_file);
        }
        return $return_file;
    }

    /* -- static helper functions -- */

    /**
     * calculates cookie expiration date from seconds
     * @param int $secs cookie expiration in seconds from now
     */
    public static function calculateCookieExpire($secs)
    {
        return gmdate('D, d M Y H:i:s T', time() + $secs);
    }

    /*
    * turns a parsed url into a string
    */
    public static function unparse_url($parsed_url)
    {
        $scheme   = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
        $host     = isset($parsed_url['host']) ? $parsed_url['host'] : '';
        $port     = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';
        $user     = isset($parsed_url['user']) ? $parsed_url['user'] : '';
        $pass     = isset($parsed_url['pass']) ? ':' . $parsed_url['pass']  : '';
        $pass     = ($user || $pass) ? "$pass@" : '';
        $path     = isset($parsed_url['path']) ? $parsed_url['path'] : '';
        $query    = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : '';
        $fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : '';
        return "$scheme$user$pass$host$port$path$query$fragment";
    }
}

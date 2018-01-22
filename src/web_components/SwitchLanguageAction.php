<?php
/*
* Slim invokable controller to switch the page language using Fccn\WebComponents\Locale middleware
* The selected language must be defined in the url path in the form - <site_url>/<path>/{lang}
* i.e. mysite.pt/setlang/pt, sets language to pt
*
* Requires Locale configurations
* Needs to be used in combination with LocaleMiddleware
*/
namespace Fccn\WebComponents;

use Psr\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Fccn\Lib\SiteConfig as SiteConfig;
use Fccn\Lib\FileLogger as FileLogger;
use Fccn\Lib\Locale as Locale;

class SwitchLanguageAction
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        \Fccn\Lib\FileLogger::debug("SwitchLanguageAction - initialization");
    }

    #sets a cookie with locale information
    public function __invoke(Request $request, Response $response, $args)
    {
        #get language from url
        $route = $request->getAttribute('route');
        $lang = $route->getArgument('lang');
        $select_method = SiteConfig::getInstance()->get("locale_selection");
        $locale = false;
        \Fccn\Lib\FileLogger::debug("SwitchLanguageAction - got language $lang");
        #TODO if lang empty get from request attribute
        foreach (\Fccn\Lib\SiteConfig::getInstance()->get("locales") as $locale) {
            \Fccn\Lib\FileLogger::debug("SwitchLanguageAction - checking locale: ".print_r($locale, true));
            if (strtoupper($lang) == strtoupper($locale["label"])) {
                //set new locale
                #$response = $this->setLocale($response, $locale["label"]);
                $locale = $locale["label"];
                break;
            }
        }
        #define redirect
        $redirect_url = SiteConfig::getInstance()->get("base_path") . "/";
        if ($request->hasHeader('HTTP_REFERER')) {
            $header = $request->getHeader('HTTP_REFERER');
            if (is_array($header) && !empty($header) && isset($header[0])) {
                \Fccn\Lib\FileLogger::debug("SwitchLanguageAction - redirecting to referrer $header[0]");
                $redirect_url = $header[0];
            }
        }
        //set new locale depending on select method
        if (!empty($locale)) {
            if ($select_method == 'param') {
                $redirect_url = $this->setLocaleParam($redirect_url, $locale);
            } else { //default to cookie
                $response = $this->setLocaleCookie($response, $locale);
            }
        }

        FileLogger::debug("SwitchLanguageAction - returning response: ".print_r($response, true));
        return $response->withRedirect($redirect_url, 301);
    }

    /*
    * Sets a new locale with param
    * Adds/replaces locale param in redirect URL
    */
    private function setLocaleParam($redirect_url, $locale)
    {
        FileLogger::debug("SwitchLanguageAction - setLocaleParam: setting Locale param <$locale> to URL $redirect_url");
        $locale_param_name = SiteConfig::getInstance()->get("locale_param_name");
        $parsed = parse_url($redirect_url);
        $params = array();
        if (!empty($parsed['query'])) {
            $query_str=$parsed['query'];
            parse_str($query_str, $params);
        }
        $params[$locale_param_name] = strtolower($locale);
        $parsed['query'] = http_build_query($params);
        return Locale::unparse_url($parsed);
    }

    /*
    * Sets a new locale with cookie
    * Adds a cookie with locale information to response
    */
    private function setLocaleCookie(Response $response, $locale)
    {
        $locale_cookie_name = SiteConfig::getInstance()->get("locale_cookie_name");
        $locale_cookie_path = SiteConfig::getInstance()->get("locale_cookie_path");
        $locale_cookie_expire = Locale::calculateCookieExpire(3600 * 24 * 30);
        $response = $response->withHeader(
          'Set-Cookie',
          "{$locale_cookie_name}=$locale; Path={$locale_cookie_path}; Expires={$locale_cookie_expire}"
        );
        return $response;
    }
}

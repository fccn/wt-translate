<?php
use Psr\Container\ContainerInterface;
use Slim\App;
use Slim\Container;

/*
* Container to use on tests
*/

$container = new Container([
    App::class => function (ContainerInterface $c) {
        $app = new App($c);

        //add locale service
        $c['locale'] = function ($cnt) {
            $locale = new \Fccn\Lib\Locale(array('slim_middleware' => true));
            return $locale;
        };

        //setup slim logs with file logger
        $c['logger'] = function ($cnt) {
            $logger = \Fccn\Lib\FileLogger::getInstance();
            $logger->pushProcessor(new Monolog\Processor\UidProcessor());
            $logger->pushHandler(new Monolog\Handler\StreamHandler(\Fccn\Lib\SiteConfig::getInstance()->get('logfile_path'), \Fccn\Lib\SiteConfig::getInstance()->get('logfile_level')));
            return $logger;
        };
        // routes and middlewares here

        //define a locale middleware
        $app->add(new \Fccn\WebComponents\LocaleMiddleware($c['locale']));

        $app->get('/', function ($request, $response, $args) {
            #$cookie = \Dflydev\FigCookies\FigRequestCookies::get($request, 'locale');
            #\Fccn\Lib\FileLogger::debug("GET / - with cookie: ".print_r($cookie, true));
            $html_content = $this->locale->getHtmlContent('homepage');
            return $response->withStatus(200)->write($html_content);
        });

        #Switch language instantiable class
        $app->get('/setLang/{lang}', Fccn\WebComponents\SwitchLanguageAction::class);

        return $app;
    }
]);

return $container;

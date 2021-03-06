
# Webapp tools - localization utilities

This presents a set of utilities for handling multilanguage and translations for web applications. It adds Gettext support to the web application and configures Gettext with settings defined in the application configuration file. It also supports document translations and full HTML page translations.

The localization utilities also provides an utility to prepare and launch xgettext via Makefile and  integrates with the twig framework, via a twig filter and a utility for parsing twig templates into a format that is understandable by xgettext.

The project also provides a set of web components the Slim framework: a Locale service, Localization middleware and a language switcher controller action.

## Installation

You can install this collection in your project using composer:
```
composer require fccn/webapp-tools-translate

```

To complete the installation copy the contents of the **locale** and **utils** folder into **[project_root]/locale** and **[project_root]/utils** respectively. These folders contain the utilities for preparing and launching xgettext and parsing the twig files.

### Locale folder

The Makefile inside the locale folder prepares the content to be processed by xgettex and calls xgettext to build the .po and .mo files. It is configured to work inside the **locale** directory. It looks for php code on **[project_root]/app** folder and the parsed twig templates on **[project_root]/cache**.

The header.po file is the base file for generating the individual .po files for each language. This file must be located in the **locale** folder.

The locale folder also holds the translation files. Each language must have its own content folder. The folder must be named with the country ID (i.e pt_PT). Each language folder must have following structure:

- **[country ID]/**
 - **files/** - location for document translations
 - **html/** - location for full HTML page translations
 - **LC_MESSAGES/** - location for Gettex translations

The locale folder already contains pre built language folders for pt_PT and en_EN languages.

### Utils folder

If you use Twig in your web application, there will be a point where it is necessary to extract the template strings from the twig template files. Unfortunately, the xgettext utility does not understand Twig templates natively. For that reason the script in **[project_root]/utils/make_php_cache_files.php** converts Twig templates to PHP files, so that xgettext can be called on those files instead. The script requires Twig to have the same configurations as you define in your web application. You can edit the **[project_root]/utils/TwigConfigLoader.php** file to load the required Twig extensions and filters in the **make_php_cache_files.php** script.

## Configuration

The localization utilities were designed with the [FCCN's webapp skeleton project](https://github.com/fccn/webapp-skeleton.git) in mind. For this reason the script to build the .po and .mo files will search for the web application code in **[project_root]/app** folder. If your application's code is stored on another location you need to edit the **[project_root]/locale/Makefile** before using the Gettext utilities.

The localization utilities makes use of the site configuration loader from the [Webapp Tools - common](https://github.com/fccn/wt-common) project. The following key-value pairs need to be added to the application configuration file *$c* array:

```php
$c = array(
    ...
    #----- locale configuration
    "defaultLocale"      => "pt_PT",          # Setup the default locale
    "defaultLocaleLabel" => "PT",             # and default locale label
    "locale_selection"   => "param",          # locale selection method, can be 'param' if locale to be set up by a url parameter, or 'cookie' if  locale is set up by cookie

    #- array of available locales
    "locales"            => array(
                              array("label" => "GB", "locale" => "en_GB", "flag_alt" => "English flag", "language" => "English"),
                              array("label" => "PT", "locale" => "pt_PT", "flag_alt" => "Portuguese flag", "language" => "Português"),
                              # add other languages here....
                              ),

    "locale_textdomain"  => "messages",
    "locale_path"        => "../locale", #path of the locale folder
    "locale_cookie_name" => "locale",    #name of the cookie to store locale information
    "locale_cookie_path" => "/",     #relative path of the locale cookie
    "locale_param_name" => "lang",    #name of the URL param to store locale information
    "request_attribute_name" => "locale", #name of the request attribute to store locale info

    #-twig parser configurations
    "twig_parser_templates_path" => "../templates",   #path for twig templates folder, can be an array if you are importing templates from other projects
    "twig_parser_cache_path" => "../cache",            #path for cache folder
    #activate verbose debug for debugging purposes (defaults to false)
    "verbose_debug" => false,
    ...
  )
```

The twig parser supports multiple twig templates folder. You can setup multiple folders as shown below:

```php

$c = array(
    ...
    twig_parser_templates_path =>  array(
      0 => __DIR__ . '/../templates', //set the base template path
      //add other namespaces to twig, in the form of 'namespace' => 'path/to/twig/templates'
    ),
    ...
  );
```

When configuring with twig, you need to load the same twig extensions and filters used by the web application into the twig parser. This prevents missinterpretations when parsing the twig templates. To do that edit **[project_root]/utils/TwigConfigLoader.php** and add the filters and extensions to the *loadConfigs* function:

```php
  public function loadConfigs($twig){
    ....

    #add the i18n extensions
    $twig->addExtension(new Twig_Extensions_some_extension());

    #add translate filter
    $filter = new Twig_SimpleFilter("my filter", function($stdClassObject) {
        return null;
    }),
    $twig->addFilter($filter);

    ...
  }
```

You can also use preset configuration loaders like the one defined in **src/TranslateConfigurationLoader.php**. The *loadConfigs()* function in **[project_root]/utils/TwigConfigLoader.php** already loads the following set of filters and extensions for localization utilities:
- Twig_Extensions_Extension_I18n - Twig internationalization extension
- Twig_Extensions_Extension_Intl - Twig date and time localization extension
- Translate filter

### Integration with Slim

 To enable global access to locale information you can add an instance of Fccn\Lib\Locale as a service - the Locale service - to your Slim application. The example below shows how to add the service to the Slim container and create a global *lang* variable in the view:

```php

$app = new \Slim\App(...);

// Fetch DI Container
$container = $app->getContainer();

$container['locale'] = function ($cnt) {
    $locale = new Fccn\Lib\Locale();
    //add global lang var
    $cnt->view->getEnvironment()->addGlobal('lang', $locale);
    return $locale;
};
```

The localization utilities provides a Slim middleware to add localization to your web application - the Localization middleware. This middleware uses the Locale service described above. For a better integration you need to configure the Locale with middleware integration. The example below shows how to add the Localization middleware to the Slim application:

```php

$app = new \Slim\App();

//get container
$container = $app->getContainer();

//Create the locale service

$container['locale'] = function ($cnt) {
    //set Locale to middleware integration
    $locale = new Fccn\Lib\Locale(array('slim_middleware' => true));
    //add global lang var
    $cnt->view->getEnvironment()->addGlobal('lang', $locale);
    return $locale;
};

//Add locale middleware
$app->add(new Fccn\WebComponents\LocaleMiddleware($container['locale']));
```

To switch between languages you can use the language switcher controller action. The path for selecting a new language must be defined as ```<site_url>/<path>/{lang}``` where *lang* is the language label (case insensitive) defined in the locales array in the configuration file (*i.e. mysite.pt/setlang/pt, sets language to Portuguese*). The following example shows how to define the language switching route for the path ```<site_url>/utils/setlang/{lang}``` with the language switcher controller action:

```php

$app = new \Slim\App();

$app->get('/utils/setlang/{lang}', Fccn\WebComponents\SwitchLanguageAction::class);
```

### Adding more languages

To create additional languages do as follows inside the **locale** folder:

1. Add a new directory with country ID. ``mkdir -p es_ES/LC_MESSAGES``
1. Copy the header file to the directory. ``cp header.po es_ES/LC_MESSAGES/messages.po``
1. Create the directories to hold the rest of the files to translate. ``mkdir es_ES/files; mkdir es_ES/html``
1. Clean cache. ``make clean``
1. Update message files. ``make``
1. Edit the po file and translate it. ``vi es_ES/LC_MESSAGES/messages.po``
 1. Do not forget to assign language, i.e. **"Language: es_ES\n"**
1. Update message files. **make**
1. Edit the application configuration file, add the new language to the *locales* array.

## Usage

The following shows the different use cases for the language utilities.

### Translate HTML content

Put the translated HTML snippets on **locale/[country_id]/html/** folder. The snippets must have the same name on all language folders. For example, to print the en_EN HTML content from a snippet on  **locale/en_EN/html/my_snippet.html**, whose contents are:
```html
<div class="col-md-12">
<p>This is a tranlated html snipped</p>
</div>
```
 you should do as follows, after setting the locale to en_EN:
```php

  $locale = new \Fccn\Lib\Locale();
  $html_content = $locale->getHtmlContent('my_snippet');
  echo $html_content
```
this in turn prints out
```
<div class="col-md-12">
<p>This is a tranlated html snipped</p>
</div>
```

### Translate text content

Put the translated snippets on **locale/[country_id]/files/** folder. The snippets must have the same name on all language folders. For example, to print the en_EN content from a snippet on  **locale/en_EN/files/my_snippet.txt**, whose contents are:
```
This is a sample snippet of translated text
```
you should do as follows after setting the locale to en_EN:
```php

  $locale = new \Fccn\Lib\Locale();
  $text_content = $locale->getFileContent('my_snippet');
  echo $text_content
```
this in turn prints out
```
This is a sample snippet of translated text
```

It is possible to include variables in the translated text snippets. The variables are represented inside brackets {} (i.e, {name}). For example if you want the snippet to adapt to the user's name you can define en_EN version of the snippet in **locale/en_EN/files/hello_user.txt**:
```
Hello {user_name}.

```
To get the content with the instantiated variable you should do as follows after setting the locale to en_EN:
```php

  $locale = new \Fccn\Lib\Locale();
  $text_content = $locale->processFile('hello_user',array("{user_name}" => "New User"));
  echo $text_content
```
this in turn prints out
```
Hello New User
```

### Gettext translations

To add translatable content on the application code write the content you want to translate inside *_()* (i.e *_('Translate me')*).

To add translatable content on twig templates load the provided translation filters to twig:
```php
 $twig = new Twig_Environment();
 \Fccn\Lib\TranslateConfigurationLoader::loadTwigConfigs($twig);

```

Then on the twig templates just use the *trans* tag:
```
  <p>{% trans "Translatable content" %}</p>

```
Each time you add content to the application and templates you need to update the translations:
1. go to the **locale** directory and run ``make``.
1. This will update with new content the .po file on each of LC_MESSAGES folder inside the individual language directory.
1. Edit the .po files for each language and add the missing translations.
1. run ``make`` again on the **locale** folder to update the compiled translations in the .mo files.

To refresh the translated contents run ``make clean && make`` on the **locale** folder.

## Testing

This project uses codeception for testing. To run the tests call ``composer test`` on the root of the project folder.

## Contributing

Please read [CONTRIBUTING.md](CONTRIBUTING.md) for details on our code of conduct, and the process for submitting pull requests to us.

## Versioning

We use [SemVer](http://semver.org/) for versioning. For the versions available, see the [tags on this repository](https://github.com/fccn/wt-translate/tags).

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details

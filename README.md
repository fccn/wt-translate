
### Localization utilities

This presents a set of utilities for handling multilanguage and translations for web applications. It adds Gettext support to the web application and configures Gettext extension with the settings defined in the application configuration file. It also supports document translations and full HTML page translations.

The localization utilities also provide integration with the twig framework, through a twig filter and a utility for parsing twig pages into a format that is understandable by Gettext.

## Installation

You can install this collection in your project using composer:
```
composer require fccn/webapp-tools/translate

```

To complete the installation copy the contents of the **locale** and **utils** folder into the root of your project. These folders contain the utilities for reading the gettext references and parsing the twig files.

### Locale folder

The Makefile inside the locale folder collects the gettext references from the project's code and builds the .po and .mo files. It must be located at the root of the **locale** folder.

The header.po file is the base file for generating the individual .po files for each language. This file must be locate in the root of the **locale** folder.

The locale folder also holds the translation files. Each language must have its own content folder. The folder must be named with the country ID (i.e pt_PT). Inside each language folder there must be following structure:

- **[country ID]/**
 - **files/** - location for document translations
 - **html/** - location for full HTML page translations
 - **LC_MESSAGES/** - location for Gettex translations

The locale folder already contains pre built language folders for pt_PT and en_EN languages.

### Utils folder

The utils folder contains a php script that calls configures and calls the gettext twig parser.
This file is called by the Makefile and needs to be located in **[project_root]/utils/make_php_cache_files.php**. After copying the file to that location you can adapt it to fit your needs. To add twig extensions or filters edit the **[project_root]/utils/TwigConfigLoader.php**.

## Configuration

The localization utilities were designed with the FCCN's webapp skeleton project in mind. For that reason the Makefile in **locale** will search for the web application code in the app folder on the project's root. If your application's code is stored on another folder you need to edit the Makefile before using it.

The following key-value pairs need to be added to the application configuration file *$c* array:
```php
$c = array(
    ...
    #----- locale configuration
    "defaultLocale"      => "pt_PT",          # Setup the default locale
    "defaultLocaleLabel" => "PT",             # and default locale label

    #- array of available locales
    "locales"            => array(
                              array("label" => "GB", "locale" => "en_GB", "flag_alt" => "English flag", "language" => "English"),
                              array("label" => "PT", "locale" => "pt_PT", "flag_alt" => "Portuguese flag", "language" => "Português"),
                              # add other languages here....
                              ),

    "locale_textdomain"  => "messages",
    "locale_path"        => "../locale", #path of the locale folder
    "locale_cookie_name" => "locale",    #name of the cookie to store locale information

    #-twig parser configurations
    "twig_parser_templates_path" => "../templates",   #path for twig templates folder
    "twig_parser_cache_path" => "../cache"            #path for cache folder
    ...
  )
```

Add the twig extensions and filters used in the web application to the twig parser. This prevents missinterpretations when parsing the twig templates. To do that edit **[project_root]/utils/TwigConfigLoader.php** and add the filters and extensions to the *loadConfigs* function:
```php
  public function loadConfigs($twig){
    ....

    #add the i18n extensions
    $twig->addExtension(new Twig_Extensions_Extension_I18n());

    #add translate filter
    $filter = new Twig_SimpleFilter("translate", function($stdClassObject) {
        return null;
    }),
    $twig->addFilter($filter);

    ...
  }
```
You can also call preset configuration loaders like the one defined in **src/TranslateConfigurationLoader.php**. The TwigConfigLoader already loads the filters and extensions provided by the localization utilities.

### Add more languages

To create additional languages do as follows inside the **locale** folder:

1. Add a new directory with country ID. **mkdir -p es_ES/LC_MESSAGES**
1. Copy the header file to the directory. **cp header.po es_ES/LC_MESSAGES/messages.po**
1. Create the directories to hold the rest of the files to translate. **mkdir es_ES/files; mkdir es_ES/html**
1. Clean cache. **make clean**
1. Update message files. **make**
1. Edit the po file and translate it. **vi es_ES/LC_MESSAGES/messages.po**
1. Update message files. **make**
1. Edit the application configuration file, add the new language to the *locales* array.

## Usage

The following shows the different use cases for the language utilities.

### Translate HTML content

Put the translated HTML snippets on **locale/[country_id]/html/** folder. The snippets must have the same name on all language folders. To obtain the translated HTML content from a snippet on file named *my_snippet.html* do as follows:
```php

  $locale = new \Fccn\Lib\Locale();
  $html_content = $locale->getHtmlContent('my_snippet');

```

### Translate text content

Put the translated snippets on **locale/[country_id]/files/** folder. The snippets must have the same name on all language folders. To obtain the translated content from a snippet on file named *my_snippet.txt* do as follows:
```php

  $locale = new \Fccn\Lib\Locale();
  $html_content = $locale->getFileContent('my_snippet');

```

It is possible to include variables in the translated text snippets. The variables are represented inside brackets {} (i.e, {name}). For example if you want the snippet to adapt to the user's name you can define en_EN version of the snippet in **locale/en_EN/files/hello_user.txt**:
```
Hello {user_name}.

```
To get the content with the instantiated variable:
```php

  $locale = new \Fccn\Lib\Locale();
  $html_content = $locale->processFile('hello_user',array("{user_name}" => "New User"));

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

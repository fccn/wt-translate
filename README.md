
### Localization utilities

This presents a set of utilities for handling multilanguage and translations for web applications. It adds gettext support to the web application and configures gettext extension with the settings defined in the application configuration file. In addition to Gettex it also supports document translations and full html page translations.

The localization utilities also provide a twig integration, providing a twig filter and a utility for parsing twig pages into a format that is understandable by Gettext.

## Installation

You can install this collection in your project using composer:
```
composer require fccn/webapp-tools/translate

```


### Language folder structure

The translation files for each language must be put into a folder named with the country ID (i.e pt_PT) and with the following structure:

- **<contry ID>**
 - **files** - location for document translations
 - **html** - location for full HTML page translations
 - **LC_MESSAGES** - location for Gettex translations

## Configuration



### Create new language

1. Add a new directory with country ID. **mkdir ES**
1. Copy the header file to the directory. **cp header.po ES/messages.po**
1. Create the directories to hold the rest of the files to translate. **mkdir ES/files; mkdir ES/html**
1. Update cache. **make update-cache**
1. Update message files. **make**
1. Edit the po file and translate it. **vi ES/messages.po**
1. Update message files. **make**

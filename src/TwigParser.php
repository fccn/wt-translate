<?php
/*
* twig parser to gettext
*
*/

namespace Fccn\Lib;

class TwigParser
{

  /*
   * Initializes the parsing of the twig files for Gettext
   */
  public static function parse($twig_config_loader = false){
    $config = \Fccn\Lib\SiteConfig::getInstance();
    $tplDir = $config->get('twig_parser_templates_path');
    $tmpDir = $config->get('twig_parser_cache_path').'/';
    $loader = new \Twig_Loader_Filesystem($tplDir);

    // force auto-reload to always have the latest version of the template
    $twig = new \Twig_Environment($loader, array(
      'cache' => $tmpDir,
      'auto_reload' => true
    ));

    //add twig configurations
    if(!empty($twig_config_loader)){
      $twig_config_loader->loadConfigs($twig);
    }

    // iterate over all your templates
    foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($tplDir), \RecursiveIteratorIterator::LEAVES_ONLY) as $file)
    {
      // force compilation
      if ($file->isFile()) {
        $twig->loadTemplate(str_replace($tplDir.'/', '', $file));
      }
    }

    $php =fopen($tmpDir."db_dump.php", "w");
    fprintf($php, "<?php\n");

    fprintf($php, "\$a = _('anonymous');\n");

    # Iterates thru all the locales
    foreach($config->get("locales") as $a) {
      fprintf($php, "\$a = _('" . $a["flag_alt"] . "');\n");
    }

    fclose($php);
  }
}

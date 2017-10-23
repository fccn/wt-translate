<?php

  /*
  * Parses twig templates and converts them into a single file that can be
  * understood by gettext
  */

  // Autoload
  require __DIR__.'/../vendor/autoload.php';

  $config = \Fccn\Lib\SiteConfig::getInstance();
  $tplDir = $config->get('locale_cache_template_path');
  $tmpDir = $config->get('locale_cache_path').'/';
  $loader = new Twig_Loader_Filesystem($tplDir);

  // force auto-reload to always have the latest version of the template
  $twig = new Twig_Environment($loader, array(
    'cache' => $tmpDir,
    'auto_reload' => true
  ));

  // ------ configure Twig the way you want
  require_once "make_twig_cache_configs.php";

  // iterate over all your templates
  foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($tplDir), RecursiveIteratorIterator::LEAVES_ONLY) as $file)
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

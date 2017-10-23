<?php


class PhpCacheMakerTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    private $dump_file;

    protected function _before()
    {
      $this->dump_file = \Fccn\Lib\SiteConfig::getInstance()->get('locale_cache_path').'/db_dump.php';
      if(file_exists($this->dump_file)){
        unlink($this->dump_file);
      }
    }

    protected function _after()
    {
      $cachefiles = glob(\Fccn\Lib\SiteConfig::getInstance()->get('locale_cache_path').'/*');
      foreach ($cachefiles as $file) {
        exec('rm -rf '.escapeshellarg($file));  
      }
      #exec('rm -rf '.escapeshellarg($cachedir));
    }

    // tests
    public function testLoad()
    {
      require __DIR__."/../../utils/make_php_cache_files.php";
      $this->assertTrue(file_exists($this->dump_file));
    }
}

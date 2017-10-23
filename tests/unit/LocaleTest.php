<?php

namespace Fccn\Tests;

class LocaleTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    private $config;
    private $locale;

    protected function _before()
    {
      $this->config = \Fccn\Lib\SiteConfig::getInstance();
      $this->locale = new \Fccn\Lib\Locale();
    }

    protected function _after()
    {
    }

    // tests
    public function testCreate()
    {
      $this->assertFalse(empty($this->locale));
      $this->assertTrue($this->locale->getCurrentLang() == $this->config->get('defaultLocale'));
    }

    public function testExistsHtmlContent(){
      $this->assertTrue($this->locale->existsHtmlContent('html_file'));
      $this->assertFalse($this->locale->existsHtmlContent('another_html_file'));
    }

    public function testGetHtmlContent(){
      $fmtime = filemtime($this->config->get("logfile_path"));
      $file_content = file_get_contents($this->config->get("locale_path") . "/" . $this->locale->getCurrentLang() . "/html/html_file.html");
      $this->assertTrue($this->locale->getHtmlContent('html_file') == $file_content);
      $this->assertTrue($this->locale->getHtmlContent('another_html_file') === "");
      //check if log was written
      $newtime = filemtime($this->config->get("logfile_path"));
      $this->assertFalse($fmtime == $newtime);
    }

    public function testExistsFileContent(){
      $this->assertTrue($this->locale->existsFileContent('sample_file'));
      $this->assertFalse($this->locale->existsFileContent('another_sample_file'));
    }

    public function testGetFileContent(){
      $fmtime = filemtime($this->config->get("logfile_path"));
      $file_content = file_get_contents($this->config->get("locale_path") . "/" . $this->locale->getCurrentLang() . "/files/sample_file.txt");
      $this->assertTrue($this->locale->getFileContent('sample_file') == $file_content);
      $this->assertTrue($this->locale->getFileContent('another_sample_file') === "");
      //check if log was written
      $newtime = filemtime($this->config->get("logfile_path"));
      #$this->assertFalse($fmtime == $newtime);
    }

    public function testProcessFile(){
      $file_content = file_get_contents($this->config->get("locale_path") . "/" . $this->locale->getCurrentLang() . "/files/sample_w_fields_filled.txt");
      $this->assertTrue($this->locale->processFile('sample_w_fields',array("{field}" => "campos")) == $file_content);
      $this->assertTrue($this->locale->getFileContent('another_sample_file') === "");
    }


}

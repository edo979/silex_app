<?php

class LittlePhotoServiceProviderTest extends PHPUnit_Framework_TestCase
{

  public $app;

  public function setUp()
  {
    $this->app = new Silex\Application();
    $this->app->register(new ESProviders\LittlePhotoServiceProvider());
  }

  public function testRegisteringProvider()
  {
    // Simple check is provider register and have set namespace
    assertEquals($this->app['photoHandler'], $this->app['photoHandler']);
  }

}
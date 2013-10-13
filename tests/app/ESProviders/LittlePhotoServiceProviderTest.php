<?php

class LittlePhotoServiceProviderTest extends PHPUnit_Framework_TestCase
{

  public $app;
  protected $reflector;
  protected $testClass;

  public function setUp()
  {
    $this->app = new Silex\Application();
    $this->app->register(new ESProviders\LittlePhotoServiceProvider());

    $this->testClass = new ESProviders\LittlePhotoServiceProvider;
    $this->reflector = new ReflectionClass($this->testClass);
  }

  public function testRegisteringProvider()
  {
    // Simple check is provider register and have set namespace
    assertEquals($this->app['photoHandler'], $this->app['photoHandler']);
  }

  public function testAttachFileFromFormToHandler()
  {
    $attache_file = $this->reflector->getMethod('attach_file');
    $attache_file->setAccessible(true);
    $attache_file->invoke($this->testClass, array(
        'error'    => 0,
        'tmp_name' => 'temp',
        'name'     => '/images/image.jpg',
        'type'     => 'jpg',
        'size'     => 600
    ));

    $handlerProp = $this->reflector->getProperty('temp_path');
    $handlerProp->setAccessible(true);
    assertEquals('temp', $handlerProp->getValue($this->testClass));

    $handlerProp = $this->reflector->getProperty('filename');
    $handlerProp->setAccessible(true);
    assertEquals('image.jpg', $handlerProp->getValue($this->testClass));
    
    $handlerProp = $this->reflector->getProperty('type');
    $handlerProp->setAccessible(true);
    assertEquals('jpg', $handlerProp->getValue($this->testClass));
    
    $handlerProp = $this->reflector->getProperty('size');
    $handlerProp->setAccessible(true);
    assertEquals(600, $handlerProp->getValue($this->testClass));
    
    // check for errors uploading image
    $attache_file->invoke($this->testClass, 'formError');
    
    $handlerProp = $this->reflector->getProperty('errors');
    $handlerProp->setAccessible(true);
    assertEquals(array('No file was uploaded.'), $handlerProp->getValue($this->testClass));
    
    $attache_file->invoke($this->testClass, array(
        'error'    => 4
    ));
    
    $handlerProp = $this->reflector->getProperty('errors');
    $handlerProp->setAccessible(true);
    assertEquals('No file.', $handlerProp->getValue($this->testClass)[1]);
  }

}
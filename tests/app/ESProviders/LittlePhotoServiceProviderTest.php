<?php

namespace ESProviders;

use Silex\Provider\DoctrineServiceProvider;

/**
 * Mock getimagesize for testing purpose
 * 
 * @param null $filename
 * @return array
 */
function getimagesize($filename = null)
{
  $filename = null;

  return array(900, 800);
}

class LittlePhotoServiceProviderTest extends \PHPUnit_Framework_TestCase
{

  public $app;
  protected $reflector;
  protected $testClass;
  protected $fake_file = array(
      'error'    => 0,
      'tmp_name' => 'temp',
      'name'     => '/images/image.jpg', // use basename()
      'type'     => 'jpg',
      'size'     => 500
  );

  public function setUp()
  {
    $this->testClass = new LittlePhotoServiceProvider;
    $this->reflector = new \ReflectionClass($this->testClass);

    $this->app = new \Silex\Application();
  }

  public function testValidateFile()
  {
    // check for errors uploading image
    $file = $this->reflector->getMethod('validate_file');
    $file->setAccessible(true);
    $validate = $file->invoke($this->testClass, 'formError');

    // Get errors array
    $handlerProp = $this->reflector->getProperty('errors');
    $handlerProp->setAccessible(true);
    assertEquals(array('No file was uploaded.'), $handlerProp->getValue($this->testClass));
    assertEquals(FALSE, $validate);

    // check for errors uploading image
    $file = $this->reflector->getMethod('validate_file');
    $file->setAccessible(true);
    $validate = $file->invoke($this->testClass, array(
        'error' => 4
    ));

    // Get errors array
    $handlerProp = $this->reflector->getProperty('errors');
    $handlerProp->setAccessible(true);
    assertEquals('No file.', $handlerProp->getValue($this->testClass)[1]);
    assertEquals(FALSE, $validate);

    // Validate upload file
    $file = $this->reflector->getMethod('validate_file');
    $file->setAccessible(true);
    $validate = $file->invoke($this->testClass, $this->fake_file);

    assertEquals(TRUE, $validate);
  }

}
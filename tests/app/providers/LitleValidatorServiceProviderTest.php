<?php

/**
 * Form Validation provider
 *
 * @author Edis Selimovic
 */
class LitleValidatorServiceProviderTest extends PHPUnit_Framework_TestCase
{

  public function testRegisteringProvider()
  {
      $app = new Silex\Application();
      
      $app->register(new ESProviders\LitleValidatorServiceProvider());
      
      assertInstanceOf('ESProvider\LitleValidatorServiceProvider', $app['validator']);
  }

}
<?php

use SimpleModel\Article;
use Silex\Provider\DoctrineServiceProvider;

/**
 * Form Validation provider
 *
 * @author Edis Selimovic
 */
class LittleValidatorServiceProviderTest extends PHPUnit_Framework_TestCase
{

  public $app;

  public function setUp()
  {
    $this->app = new Silex\Application();
    $this->app->register(new ESProviders\LittleValidatorServiceProvider());

    // Database
    $this->app->register(new DoctrineServiceProvider(), array(
        'db.options' => array()));

    //Load Model For test
    $this->app['model.article'] = $this->app->share(function($app) {
        return new Article($app['db']);
      });
  }

  public function testRegisteringProvider()
  {
    // Simple check is provider register and have set namespace
    assertEquals($this->app['validator'], $this->app['validator']);
  }

  public function testValidatingRuleRequire()
  {
    // Data from form
    $data = array('title' => 'some title', 'body'  => '');
    // Rules set in model services
    $rules = array(
        'title' => 'required'
    );

    $result = $this->app['validator']($data, $rules);
    assertTrue($result);

    // Rules set in model services
    $rules = array(
        'title' => 'required',
        'body'  => 'required'
    );
    $result = $this->app['validator']($data, $rules);
    assertFalse($result); // body is required
    
    assertEquals('body polje morate upisati', $this->app['validator.errors'][0]);
  }

}
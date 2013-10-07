<?php

namespace ESProviders;

use Silex\Application;
use Silex\ServiceProviderInterface;

/**
 * Validate data
 *
 * @author Comp
 */
class LitleValidatorServiceProvider implements ServiceProviderInterface
{
  public function boot(Application $app)
  {
    
  }

  public function register(Application $app)
  {
    $app['validator'] = function(){
      return new LitleValidatorServiceProvider();
    };
  }
}
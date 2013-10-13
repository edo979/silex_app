<?php

namespace ESProviders;

use Silex\Application;
use Silex\ServiceProviderInterface;

class LittlePhotoServiceProvider implements ServiceProviderInterface
{
  public function boot(Application $app)
  {
    
  }

  public function register(Application $app)
  {
    $app['photoHandler'] = $app->protect(function() use($app) {
      
      });
  }  
}

// Get file from form
// Validate file
// Save file to db
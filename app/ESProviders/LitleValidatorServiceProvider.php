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

  private $errors = array();

  public function boot(Application $app)
  {
    
  }

  public function register(Application $app)
  {
    $app['validator'] = $app->protect(function($data, $rules) use($app) {
        $valid = $this->validate($data, $rules);
        // Register array for saving errors
        $app['validator.errors'] = $this->errors;

        return $valid;
      });
  }

  private function validate($data, $rules)
  {
    $valid = TRUE;

    foreach ($rules as $fildname => $rule)
    {
      $callbacks = explode('|', $rule);

      foreach ($callbacks as $callback)
      {
        $value = isset($data[$fildname]) ? $data[$fildname] : NULL;
        if ($this->$callback($value, $fildname) == FALSE)
          $valid = FALSE;
      }
    }

    return $valid;
  }

  private function required($value, $fildname)
  {
    $valid = !empty($value);

    if ($valid == FALSE)
    {
      $this->errors[] = "{$fildname} polje morate upisati";
    }

    return $valid;
  }

}
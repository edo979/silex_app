<?php

use Symfony\Component\HttpFoundation\Request;

$imageAdmin = $app['controllers_factory'];

// Photos
// New Photo
$imageAdmin->post('/new', function (Silex\Application $app)
{
  if (!empty($_FILES["file"]))
  {
    $app->register(new ESProviders\LittlePhotoServiceProvider(), array(
        'photoHandler.temp_file' => $_FILES["file"],
        'photoHandler.errors'    => array(),
        'photoHandler.imageId'   => 0
    ));
    if ($app['photoHandler'])
    {
      $id = $app['photoHandler.imageId'];
      return $app->json(array('imageId' => $id), 200);
    }
  }
  else
  {
    return $app->json(array('error' => 'error'), 400);
  }
});

return $imageAdmin;

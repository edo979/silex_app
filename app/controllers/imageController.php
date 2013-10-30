<?php

use Symfony\Component\HttpFoundation\Request;

$image = $app['controllers_factory'];

// New image in admin controler
// Get Photo
$app->get('/image/{id}', function (Silex\Application $app, $id)
{
  $image = $app['model.photo']->get($id);

  // create response
  $path = '../app/uploads/' . $image['filename'];
  if (!file_exists($path))
  {
    $app->abort(404);
  }
  return $app->sendFile($path, 200, array(
        'Content-Type' => 'image/png, image/jpg, image/gif',
        'Pragma'       => 'public'
      )
  );
})->bind('image');

return $image;
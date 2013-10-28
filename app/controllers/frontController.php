<?php

use Symfony\Component\HttpFoundation\Request;

// Routes
$app->get('/', function(Silex\Application $app)
{
  return $app['twig']->render('homepage.twig', array(
        'pageId' => 'home',
        'title'  => 'Home'
  ));
})->bind('homepage');

$app->get('/gallery', function(Silex\Application $app)
{
  return $app['twig']->render('gallery.twig', array(
        'pageId' => 'gallery',
        'title'  => 'Gallery'
  ));
})->bind('gallery');

$app->get('/about', function(Silex\Application $app)
{
  return $app['twig']->render('about.twig', array(
        'pageId' => 'about',
        'title'  => 'About'
  ));
})->bind('about');

$app->get('/contact', function(Silex\Application $app)
{
  return $app['twig']->render('contact.twig', array(
        'pageId' => 'contact',
        'title'  => 'Contact'
  ));
})->bind('contact');

$app->get('/login', function(Request $request) use ($app)
{
  return $app['twig']->render('login.twig', array(
        'title'         => 'Login',
        'pageId'        => 'login',
        'error'         => $app['security.last_error']($request),
        'last_username' => $app['session']->get('_security.last_username'),
  ));
})->bind('login');

// Get Photo
$app->get('/photos/{id}', function (Silex\Application $app, $id)
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
});

$app->mount('/admin', include 'admin/controller.php');
$app->mount('/blog', include 'blogController.php');

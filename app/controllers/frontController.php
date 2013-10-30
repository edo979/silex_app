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

// Mounting Controllers
// Admin Area
$app->mount('/admin', include 'admin/controller.php');
$app->mount('/admin/images', include 'admin/imageController.php');

// Public Controllers
$app->mount('/blog', include 'blogController.php');
$app->mount('/image', include 'imageController.php');

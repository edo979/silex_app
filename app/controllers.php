<?php

use Symfony\Component\HttpFoundation\Request;

// Routes
$app->get('/login', function(Request $request) use ($app) {
      return $app['twig']->render('login.twig', array(
            'error'         => $app['security.last_error']($request),
            'last_username' => $app['session']->get('_security.last_username'),
      ));
  });

$app->get('/hello', function() {
      return 'Hello!';
  });

$app->get('/', function() {
      return "Welcome home";
  });

$app->get('/admin/', function() {
      echo '<a href="/admin/logout">Logout</a>';
      return "Welcome Admin!!!";
  });
<?php

$admin = $app['controllers_factory'];
$admin->get('/dashboard', function (Silex\Application $app)
  {
    return $app['twig']->render('admin/dashboard.twig', array(
          'pageId' => 'admin',
          'title'  => 'Admin'
    ));
  })->bind('dashboard');

return $admin;
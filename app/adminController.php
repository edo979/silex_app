<?php

$admin = $app['controllers_factory'];
$admin->get('/dashboard', function (Silex\Application $app)
  {
    $pageId = 'dashboard';
    return $app['twig']->render('admin/dashboard.twig', array(
          'pageId' => $pageId,
          'title'  => ucfirst($pageId)
    ));
  })->bind('dashboard');

$admin->get('/articles', function (Silex\Application $app)
  {
    $pageId = 'articles';
    return $app['twig']->render('admin/articles.twig', array(
          'pageId' => $pageId,
          'title'  => ucfirst($pageId)
    ));
  })->bind('articles');

return $admin;
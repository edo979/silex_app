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
    $conn = $app['db'];

    $statement = $conn->executeQuery('SELECT * FROM articles');
    $articles = $statement->fetchAll();

    return $app['twig']->render('admin/articles.twig', array(
          'pageId' => $pageId,
          'title'  => ucfirst($pageId),
          'articles'=> $articles
    ));
  })->bind('articles');
  
  $admin->get('/article/{id}', function (Silex\Application $app, $id)
  {
    $pageId = 'article';
    return $app['twig']->render('admin/dashboard.twig', array(
          'pageId' => $pageId,
          'title'  => ucfirst($pageId)
    ));
  })->bind('article');

return $admin;
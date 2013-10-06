<?php

use Symfony\Component\HttpFoundation\Request;

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
    
    $articles = $app['model.article']->get();

    return $app['twig']->render('admin/articles.twig', array(
          'pageId' => $pageId,
          'title'  => ucfirst($pageId),
          'articles'=> $articles
    ));
  })->bind('articles');
  
  $admin->get('/article/{id}', function (Silex\Application $app, $id)
  {
    $pageId = 'article';
    $conn = $app['db'];
    
    $article = $app['model.article']->get($id);
    
    return $app['twig']->render('admin/article.twig', array(
          'pageId' => $pageId,
          'title'  => ucfirst($pageId),
          'article'=> $article
    ));
  })->bind('article');
  
  $admin->post('/article/{id}', function (Silex\Application $app, Request $request, $id)
  {
    $pageId = 'article';
    $conn = $app['db'];
    $data = array();
    
    $data['title'] = $request->get('title'); 
    $data['body'] = $request->get('body'); 
    
    $article = $app['model.article']->save($data, $id);
    
    if($article)
    {
      // Redirect
      return $app->redirect($app['url_generator']->generate('articles'));
    }
    else
    {
      // Show errors
    }
  });

return $admin;
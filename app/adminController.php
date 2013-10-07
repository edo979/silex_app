<?php

use Symfony\Component\HttpFoundation\Request;

$admin = $app['controllers_factory'];

// Dashboard
$admin->get('/dashboard', function (Silex\Application $app)
  {
    $pageId = 'dashboard';
    return $app['twig']->render('admin/dashboard.twig', array(
          'pageId' => $pageId,
          'title'  => ucfirst($pageId)
    ));
  })->bind('dashboard');

  
  
// List Articles
$admin->get('/articles', function (Silex\Application $app)
  {
    $pageId = 'articles';
    
    $articles = $app['model.article']->get();

    return $app['twig']->render('admin/articles.twig', array(
          'pageId' => $pageId,
          'title'  => ucfirst($pageId),
          'articles'=> $articles
    ));
  })->bind('articles');
  
  
  
  // New Article show form
  $admin->get('/articles/new', function (Silex\Application $app)
  {
    $pageId = 'newArticle';
    
    // Empty values for form
    $article = array('title' => '', 'body' => '');

    return $app['twig']->render('admin/article.twig', array(
          'pageId' => $pageId,
          'title'  => ucfirst($pageId),
          'article'=> $article
    ));
  })->bind('articlesNew');
  
  // New Article process form
  $admin->post('/articles/new', function (Silex\Application $app, Request $request)
  {    
    $data['title'] = $request->get('title'); 
    $data['body'] = $request->get('body'); 
    
    $article = $app['model.article']->save($data);
    
    if($article)
    {
      // Redirect
      return $app->redirect('/admin/articles');
    }
    else
    {
      // Show errors
    }
  });  
  
  
  
  // Article Edit show form
  $admin->get('/article/{id}', function (Silex\Application $app, $id)
  {
    $pageId = 'article';
    
    $article = $app['model.article']->get($id);
    
    return $app['twig']->render('admin/article.twig', array(
          'pageId' => $pageId,
          'title'  => ucfirst($pageId),
          'article'=> $article
    ));
  })->bind('article');
  
  // Article Edit process form
  $admin->post('/article/{id}', function (Silex\Application $app, Request $request, $id)
  {
    $data = array();
    
    $data['title'] = $request->get('title'); 
    $data['body'] = $request->get('body'); 
    
    $article = $app['model.article']->save($data, $id);

    if($article)
    {
      // Redirect
      return $app->redirect('/admin/articles');
    }
    else
    {
      // Show errors
    }
  });

return $admin;
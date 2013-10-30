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
        'pageId'   => $pageId,
        'title'    => ucfirst($pageId),
        'articles' => $articles
  ));
})->bind('articles');



// New Article show form
$admin->get('/articles/new', function (Silex\Application $app)
{
  $pageId = 'article';

  // Empty values for form
  $article = array('title' => '', 'body' => '', 'publish' => '1', 'pubdate' => date("Y-m-d"));
  
  // Get last three images form database
  $lastAddImages = $app['model.photo']->getLastAdd(3);

  return $app['twig']->render('admin/article.twig', array(
        'pageId'  => $pageId,
        'title'   => ucfirst($pageId),
        'article' => $article,
        'images'  => $lastAddImages
  ));
})->bind('articlesNew');

// New Article process form
$admin->post('/articles/new', function (Silex\Application $app, Request $request)
{
  $data = array();

  $data['id'] = $request->get('id');

  // Get article content
  if (is_numeric($data['id']) && $data['id'] == 0)
  {
    $data['title'] = $request->get('title');
    $data['body'] = $request->get('body');
    $data['publish'] = $request->get('publish');
    $data['pubdate'] = $request->get('publishDate');
    
    // TODO: Validator

    $id = $app['model.article']->save($data);

    return $app->json(array('articleId' => $id, 'modified' => date("d.m.Y H:i:s")), 200);
  }
  else
  {
    return $app->json(array('error' => 'error'), 400);
  }
});



// Article Edit show form
$admin->get('/article/{id}', function (Silex\Application $app, $id)
{
  $pageId = 'article';

  $article = $app['model.article']->get($id);
  
  // Get last three images form database
  $lastAddImages = $app['model.photo']->getLastAdd(3);

  return $app['twig']->render('admin/article.twig', array(
        'pageId'  => $pageId,
        'title'   => ucfirst($pageId),
        'article' => $article,
        'images'  => $lastAddImages
  ));
})->bind('article');

// Article Edit process form
$admin->post('/article/{id}', function (Silex\Application $app, Request $request, $id)
{
  if (is_numeric($id))
  {
    $data = array();

    $data['title'] = $request->get('title');
    $data['body'] = $request->get('body');
    $data['publish'] = $request->get('publish');
    $data['pubdate'] = $request->get('publishDate');
    
    // TODO: Validator

    $result = $app['model.article']->save($data, $id);

    if ($result)
    {
      // Format date
      $date = new DateTime($result['modified']);

      return $app->json(
        array('id' => $id,
          'modified' => $date->format('d.m.Y H:i:s')
        ), 200);
    }
  }

  return $app->json(array('error' => 'error'), 400);
});



// Delete article
$admin->get('/article/delete/{id}', function (Silex\Application $app, $id)
{
  $article = $app['model.article']->delete($id);

  if ($article)
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

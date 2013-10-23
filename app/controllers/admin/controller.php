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
  $article = array('title' => '', 'body' => '');

  return $app['twig']->render('admin/article.twig', array(
        'pageId'  => $pageId,
        'title'   => ucfirst($pageId),
        'article' => $article
  ));
})->bind('articlesNew');

// New Article process form
$admin->post('/articles/new', function (Silex\Application $app, Request $request)
{
  $data = array();

  $data['id'] = $request->get('id');
  $data['title'] = $request->get('title');
  $data['body'] = $request->get('body');

  if (is_numeric($data['id']) && $data['id'] == 0)
  {
    $id = $app['model.article']->save($data);

    return $app->json(array('id' => $id), 200);
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

  return $app['twig']->render('admin/article.twig', array(
        'pageId'  => $pageId,
        'title'   => ucfirst($pageId),
        'article' => $article
  ));
})->bind('article');

// Article Edit process form
$admin->post('/article/{id}', function (Silex\Application $app, Request $request, $id)
{
  $data = array();

  $data['id'] = $request->get('id');
  $data['title'] = $request->get('title');
  $data['body'] = $request->get('body');

  // Ajax request
  if (is_numeric($data['id']) && ($data['id'] == 0 || $data['id'] == $id))
  {
    $result = $app['model.article']->save($data, $id);

    if ($result)
    {
      return $app->json(array('id' => $data['id']), 200);
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



// Photos
// New Photo
$admin->post('/photos/new', function (Silex\Application $app)
{
  $app->register(new ESProviders\LittlePhotoServiceProvider(), array(
      'photoHandler.temp_file' => $_FILES["file"],
      'photoHandler.errors'    => array()
  ));

  $article_id = $app['model.article']->get_last_id();

  $fileName = $app['photoHandler'];

  if (!empty($fileName))
  {
    $data = array(
        'filename'    => $fileName,
        'article_ids' => $article_id
    );

    // save to db    
    $app['model.photo']->save($data);
    // get id from saved image
    $id = $app['model.photo']->get_last_id();

    // Return JSON with inserted image id
    die('{
        "jsonrpc" : "2.0",
        "result" : null,
        "id" : ' . $id . '
      }');
  }
});
// New Photo
$admin->get('/photos/article/{id}', function (Silex\Application $app, $id)
{
  
});

return $admin;

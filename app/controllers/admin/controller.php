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
  $article = array('title' => '', 'body' => '', 'publishDate' => '');

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

  // Get article content
  if (is_numeric($data['id']) && $data['id'] == 0)
  {
    $data['title'] = $request->get('title');
    $data['body'] = $request->get('body');
    $data['pubdate'] = $request->get('publishDate');
    
    // TODO: Validator

    $id = $app['model.article']->save($data);

    return $app->json(array('articleId' => $id), 200);
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
  if (is_numeric($id))
  {
    $data = array();

    $data['title'] = $request->get('title');
    $data['body'] = $request->get('body');
    $data['pubdate'] = $request->get('publishDate');
    
    // TODO: Validator

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
  if (!empty($_FILES["file"]))
  {
    $app->register(new ESProviders\LittlePhotoServiceProvider(), array(
        'photoHandler.temp_file' => $_FILES["file"],
        'photoHandler.errors'    => array(),
        'photoHandler.imageId'   => 0
    ));
    if ($app['photoHandler'])
    {
      $id = $app['photoHandler.imageId'];
      return $app->json(array('imageId' => $id), 200);
    }
  }
  else
  {
    return $app->json(array('error' => 'error'), 400);
  }
});

// Get Photo
$admin->get('/photos/{id}', function (Silex\Application $app, $id)
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

return $admin;

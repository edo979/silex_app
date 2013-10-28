<?php

use Symfony\Component\HttpFoundation\Request;

$blog = $app['controllers_factory'];

// list article
$blog->get('/', function (Silex\Application $app)
{
  $articles = $app['model.article']->get();
  
  return $app['twig']->render('blog.twig', array(
        'pageId' => 'blog',
        'title'  => 'Blog',
        'articles' => $articles
  ));
})->bind('blog');

return $blog;

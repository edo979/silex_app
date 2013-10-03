<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\SecurityServiceProvider;
use Silex\Provider\DoctrineServiceProvider;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;
use Silex\Provider\TwigServiceProvider;

$app = new Silex\Application();
$app['debug'] = true;

/*
 * Register the providers
 */
// Url generator
$app->register(new UrlGeneratorServiceProvider());

// Database
$app->register(new DoctrineServiceProvider(), array(
    'db.options' => array(
        'dbname'   => 'silex_app',
        'user'     => 'ediss',
        'password' => 'kahva',
        'host'     => '127.0.0.1',
        'driver'   => 'pdo_mysql',
        'port'     => '3306',
        'charset'  => 'utf8'
    )
));

// Sessions
$app->register(new SessionServiceProvider());
$app['session.db_options'] = array(
    'db_table'    => 'session',
    'db_id_col'   => 'session_id',
    'db_data_col' => 'session_value',
    'db_time_col' => 'session_time',
);
$app['session.storage.handler'] = $app->share(function () use ($app) {
      return new PdoSessionHandler(
        $app['db']->getWrappedConnection(), $app['session.db_options'], $app['session.storage.options']
      );
  });

// Twig
$app->register(new TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/views',
));

// security
$app->register(new SecurityServiceProvider(), array(
    'security.firewalls' => array(
        'admin' => array(
            'pattern' => '^/admin/',
            'form'    => array('login_path' => '/login', 'check_path' => '/admin/login_check'),
            'logout'  => array('logout_path' => '/admin/logout'),
            'users'   => array(
                'admin' => array('ROLE_ADMIN',
                    '5FZ2Z8QIkA7UTZ4BYkoC+GsReLf569mSKDsfods6LYQ8t+a8EW9oaircfMpmaLbPBh4FOBiiFyLfuZmTSUwzZg=='),
            ),
        ),
    )
));

return $app;
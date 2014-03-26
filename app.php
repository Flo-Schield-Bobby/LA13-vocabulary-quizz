<?php

require_once __DIR__.'/vendor/autoload.php';

// Symfony components
use Symfony\Component\HttpFoundation\Request;

// Silex utilities
use Silex\Application;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\SecurityServiceProvider;

// Doctrine ORM
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

// Third-party services
use DerAlex\Silex\YamlConfigServiceProvider;
use Dflydev\Silex\Provider\DoctrineOrm\DoctrineOrmServiceProvider;
use Entea\Twig\Extension\AssetExtension;

// Application
$app = new Application();

// Debug --> Set up to false in production !!
$app['debug'] = true;

// Session
$app->register(new SessionServiceProvider());
$app['session']->start();

// Url generation
$app->register(new UrlGeneratorServiceProvider());

// Load extensions and services
$app->register(new YamlConfigServiceProvider(__DIR__ . '/config/parameters.yml'));

$parameters = array(
    'database' => array(
        'driver'   => $app['config']['database']['driver'],
        'dbname'   => $app['config']['database']['name'],
        'host'     => $app['config']['database']['host'],
        'user'     => $app['config']['database']['user'],
        'password' => $app['config']['database']['password'],
        'port'     => $app['config']['database']['port']
    )
);

// Doctrine
$app->register(new DoctrineServiceProvider(), array(
    'db.options' => $parameters['database']
));

// Doctrine ORM
$app->register(new DoctrineOrmServiceProvider(), array(
    'orm.em.options' => array(
        'mappings' => array(
            array(
                'type' => 'annotation',
                'namespace' => 'L113'
            )
        )
    )
));

// EntityManager
$app['em'] = function ($app) use ($parameters) {
    $config = Setup::createAnnotationMetadataConfiguration(array(__DIR__ . '/src'), true, null, null, false);
    return EntityManager::create($parameters['database'], $config);
};

// Forms
$app->register(new FormServiceProvider());

// Twig
$app->register(new TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/views',
    'twig.class_path' => __DIR__ . '/vendor/twig/lib'
));

# Twig asset extension
$app['twig']->addExtension(new AssetExtension($app));

$app->get('/', function() use ($app) {
    return 'Hello World!';
});

$app->run();

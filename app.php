<?php

require_once __DIR__.'/vendor/autoload.php';

// Symfony components
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

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

use Fsb\La13\Entity\UserRepository;

// Application
$app = new Application();

// Debug --> Set up to false in production !!
$app['debug'] = true;

// Session
$app->register(new SessionServiceProvider());
$app['session']->start();

// Url generation
$app->register(new UrlGeneratorServiceProvider());

// Load config
$app->register(new YamlConfigServiceProvider(__DIR__ . '/config/parameters.yml'));

// Define global parameters
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
    'twig.class_path' => __DIR__ . '/vendor/twig/lib',
    'twig.debug' => true
));

# Twig asset extension
$app['twig']->addExtension(new AssetExtension($app));

# is_granted function
$app['twig']->addFunction(new Twig_SimpleFunction('is_granted', function ($role, $object = null) use ($app) {
    return $app['security']->isGranted($role, $object);
}));

// Security : logins and back-office
$app['security.encoder.digest'] = $app->share(function ($app) {
    // Algorithm : sha1
    // Encoded as base_64 : true
    // Iterations : 1000
    return new MessageDigestPasswordEncoder('sha1', true, 1000);
});
$app->register(new SecurityServiceProvider(), array(
    'security.role_hierarchy' => array(
        'ROLE_ADMIN' => array(
            'ROLE_USER',
            'ROLE_ALLOWED_TO_SWITCH'
        )
    ),
    'security.firewalls' => array(
        'login' => array(
            'pattern' => '^/login$',
            'anonymous' => true
        ),
        'front' => array(
            'pattern' => '^/',
            'form' => array(
                'login_path' => '/login',
                'check_path' => '/login_check'
            ),
            'logout' => array(
                'logout_path' => '/logout'
            ),
            'users' =>  $app->share(function () use ($app) {
                return new UserRepository();
            })
        ),
        'admin_login' => array(
            'pattern' => '^/admin/login$',
            'anonymous' => true
        ),
        'admin' => array(
            'pattern' => '^/admin',
            'form' => array(
                'login_path' => '/admin/login',
                'check_path' => '/admin/login_check'
            ),
            'logout' => array(
                'logout_path' => '/admin/logout'
            ),
            'users' => array(
                'admin' => array(
                    'ROLE_ADMIN',
                    '5FZ2Z8QIkA7UTZ4BYkoC+GsReLf569mSKDsfods6LYQ8t+a8EW9oaircfMpmaLbPBh4FOBiiFyLfuZmTSUwzZg=='
                )
            )
        )
    ),
    'security.access_rules' => array(
        array('^/login$', 'IS_AUTHENTICATED_ANONYMOUSLY'),
        array('^/', 'IS_AUTHENTICATED_FULLY'),
        array('^/admin/login$', 'IS_AUTHENTICATED_ANONYMOUSLY'),
        array('^/admin', 'ROLE_ADMIN')
    )
));


// Controllers
$app->get('/', function() use ($app) {
    return $app['twig']->render('front/index.html.twig');
})->bind('home');

$app->get('/login', function (Request $request) use ($app) {
    return $app['twig']->render('front/authentication/login.html.twig', array(
        'error' => $app['security.last_error']($request),
        'last_username' => $app['session']->get('_security.last_username')
    ));
})->bind('login');

$app->get('/words', function() use ($app) {
    return $app['twig']->render('front/index.html.twig');
})->bind('words');

$app->get('/profile', function() use ($app) {
    return $app['twig']->render('front/index.html.twig');
})->bind('profile');

$app->run();
$app->boot();

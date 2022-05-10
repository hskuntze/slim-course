<?php
    session_start();
    date_default_timezone_set('America/Sao_Paulo');
    require __DIR__ . '/../vendor/autoload.php';
    //Configurações do Slim
    $config = [
        'settings' => [
            'displayErrorDetails' => true,
            'db' => [
                'driver' => 'mysql',
                'host' => 'localhost',
                'database' => 'hkblog',
                'username' => 'root',
                'password' => 'diamante21',
                'charset' => 'utf8',
                'collation' => 'utf8_unicode_ci',
                'prefix' => ''
            ]
        ]
    ];
    
    $app = new Slim\App($config);
    
    //Criamos um container, que é basicamente um array
    $container = $app->getContainer();

    $capsule = new Illuminate\Database\Capsule\Manager;
    $capsule->addConnection($container['settings']['db']);
    $capsule->setAsGlobal();
    $capsule->bootEloquent();

    $container['view'] = function($container){
        $view = new Slim\Views\Twig(__DIR__ . '/../resources/views', ['cache' => false]);
        $view->addExtension(new Slim\Views\TwigExtension(
            $container->router,
            $container->request->getUri()
        ));
        $view->getEnvironment()->addGlobal('flash', $container->flash);
        $view->getEnvironment()->addGlobal('auth', [
            'check' => $container->auth->check(),
            'user' => $container->auth->user()
        ]);
        return $view;
    };

    $container['validator'] = function($container){
        return new App\Validation\Validator;
    };

    $container['flash'] = function($container){
        return new Slim\Flash\Messages;
    };

    $container['auth'] = function($container){
        return new App\Auth\Auth($container);
    };

    //No array criamos uma chave, e como valor da chave passamos uma função
    $container['HomeController'] = function($container){
        return new App\Controllers\HomeController($container);
    };

    $container['AuthController'] = function($container){
        return new App\Controllers\AuthController($container);
    };

    
    $app->add(new App\Middleware\DisplayInputErrorsMiddleware($container));

    require __DIR__ . '/routes.php';
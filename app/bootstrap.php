<?php
    session_start();
    date_default_timezone_set('America/Sao_Paulo');
    require __DIR__ . '/../vendor/autoload.php';
    //Configurações do Slim
    $config = [
        'settings' => [
            'displayErrorDetails' => true
        ]
    ];
    
    $app = new Slim\App($config);
    
    //Criamos um container, que é basicamente um array
    $container = $app->getContainer();
    //No array criamos uma chave, e como valor da chave passamos uma função
    $container['HomeController'] = function($container){
        return new App\Controllers\HomeController($container);
    };

    $container['AuthController'] = function($container){
        return new App\Controllers\AuthController($container);
    };

    $container['view'] = function($container){
        $view = new Slim\Views\Twig(__DIR__ . '/../resources/views', ['cache' => false]);
        $view->addExtension(new Slim\Views\TwigExtension(
            $container->router,
            $container->request->getUri()
        ));
        
        return $view;
    };

    require __DIR__ . '/routes.php';
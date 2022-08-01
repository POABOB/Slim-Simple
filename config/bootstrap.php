<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use Symfony\Component\Dotenv\Dotenv;
use Slim\App;
use Medoo\Medoo;

require_once __DIR__ . '/../vendor/autoload.php';

// 註冊我們設定好的CLASS
$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions(__DIR__ . '/container.php');
$container = $containerBuilder->build();

// LOAD .ENV
$dotenv = $container->get(Dotenv::class);
$dotenv->load(__DIR__.'/../.env');

// 建立App INSTANCE
$app = $container->get(App::class);
$db = $container->get(Medoo::class);

// 中間件
(require __DIR__ . '/middleware.php')($app);

// 路由
(require __DIR__ . '/routes.php')($app, $db);

return $app;
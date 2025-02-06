<?php

use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

// Create Slim app
$app = AppFactory::create();

// Add middleware to parse JSON body
$app->addBodyParsingMiddleware();

// Create SQLite database connection (using PDO)
$container = $app->getContainer();
$container['db'] = function () {
    return new PDO('sqlite:' . __DIR__ . '/../database/chat.db');
};

// Include routes
require __DIR__ . '/../src/routes.php';

// Run the app
$app->run();

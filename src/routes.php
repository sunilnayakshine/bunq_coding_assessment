<?php

use Slim\Factory\AppFactory;
use App\Controllers\UserController;
use App\Controllers\GroupController;
use App\Controllers\MessageController;
// Create the Slim app instance
$app = AppFactory::create();

// POST API to add a new user
$app->post('/sign-up', [UserController::class, 'addUser']);

// GET API to fetch all users
$app->get('/users', [UserController::class, 'getUsers']);


// POST API to create a new group
$app->post('/create-group', [GroupController::class, 'createGroup']);

$app->post('/join-group', [GroupController::class, 'joinGroup']);

$app->post('/send-message', [MessageController::class, 'sendMessage']);

$app->post('/get-message', [MessageController::class, 'getMessages']);

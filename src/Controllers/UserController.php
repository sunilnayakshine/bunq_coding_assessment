<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\UserModel;

class UserController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    // Add a user to the database
    public function addUser(Request $request, Response $response): Response
    {
        $rawdata = (string) $request->getBody();
        
        $data = json_decode($rawdata, true);
        $username = $data['username'];
        $password = $data['password'];

        // Check for empty username or password
        if (empty($username) || empty($password)) {
            error_log("Username or password is empty");
            $response->getBody()->write(json_encode(['error' => 'Username and password are required']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        try {
            $username = $this->userModel->createUser($username, $password);
            $response->getBody()->write(json_encode(['Created user: ' => $username]));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            // Log error details and handle error if user already exists
            error_log('Error in addUser: ' . $e->getMessage());
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    }

}


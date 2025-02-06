<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\UserModel;
use App\Models\GroupModel;

class GroupController
{
    private $userModel;
    private $groupModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->groupModel = new GroupModel();
    }

    public function createGroup(Request $request, Response $response): Response
    {
        // Get the raw body as a string
        $rawData = (string) $request->getBody();

        // Parse the body as JSON
        $data = json_decode($rawData, true);

        // Validate request payload
        if (empty($data['username']) || empty($data['password']) || empty($data['group_name']) || empty($data['description'])) {
            $response = $response->withStatus(400)
                ->withHeader('Content-Type', 'application/json');
            $response->getBody()->write(json_encode(['error' => 'Invalid input']));
            return $response;
        }

        $username = $data['username'];
        $password = $data['password'];
        $groupname = $data['group_name'];
        $groupdescription = $data['description'];


        // Verify user credentials
        $isValidUser = $this->userModel->verifyUserCredentials($username, $password);

        if (!$isValidUser) {
            $response = $response->withStatus(401)
                ->withHeader('Content-Type', 'application/json');
            $response->getBody()->write(json_encode(['error' => 'Invalid username or password']));
            return $response;
        }

        // Checking whether groups exists or not
        $isGroupExists = $this->groupModel->doesGroupExist($groupname);

        if ($isGroupExists){
            $response = $response->withStatus(400)
                  ->withHeader('Content-Type', 'application/json');
            $response->getBody()->write(json_encode(['error' => 'Group already exists']));
            return $response;

        }

        // Create the group
        $groupId = $this->groupModel->createGroup($groupname, $groupdescription, $username);

        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode(['message' => 'Group created successfully', 'Group Name' => $groupname]));
        return $response;
    }

    public function joinGroup(Request $request, Response $response, array $args): Response
    {
        // Get the raw body as a string
        $rawData = (string) $request->getBody();

        // Parse the body as JSON
        $data = json_decode($rawData, true);

        // Validate request payload
        if (empty($data['username']) || empty($data['password']) || empty($data['group_name'])) {
            $response = $response->withStatus(400)
                ->withHeader('Content-Type', 'application/json');
            $response->getBody()->write(json_encode(['error' => 'Invalid input']));
            return $response;
        }

        $username = $data['username'];
        $password = $data['password'];
        $groupname = $data['group_name'];

        // Verify user credentials
        $isValidUser = $this->userModel->verifyUserCredentials($username, $password);

        if (!$isValidUser) {
            $response = $response->withStatus(401)
                ->withHeader('Content-Type', 'application/json');
            $response->getBody()->write(json_encode(['error' => 'Invalid username or password']));
            return $response;
        }

        // Check if the group exists
        $groupId = $this->groupModel->getGroupIdByName($groupname);

        if (!$groupId) {
            $response = $response->withStatus(404)
                ->withHeader('Content-Type', 'application/json');
            $response->getBody()->write(json_encode(['error' => 'Group not found']));
            return $response;
        }

        // Check if the user is already in the group
        if ($this->groupModel->isUserInGroup($groupname, $username)) {
            $response = $response->withStatus(400)
                ->withHeader('Content-Type', 'application/json');
            $response->getBody()->write(json_encode(['message' => 'Your already a member of this group']));
            return $response;
        }

        // Add the user to the group
        $this->groupModel->addUserToGroup($username, $groupname);

        // Return success response
        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode(['message' => 'Joined the group successfully', 'Group Name' => $groupname]));
        return $response;
    }
}

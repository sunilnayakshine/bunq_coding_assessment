<?php
namespace App\Controllers;

use App\Models\MessageModel;
use App\Models\UserModel;
use App\Models\GroupModel;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class MessageController
{
    private $messageModel;

    public function __construct()
    {
         $this->messageModel = new MessageModel();
         $this->userModel = new UserModel();
         $this->groupModel = new GroupModel();
    }

    // Method to handle message creation
    public function sendMessage(Request $request, Response $response, $args)
    {
        $rawdata = (string) $request->getBody(); // Get the request data
        error_log("received data: " . $rawdata);
        $data = json_decode($rawdata, true);

        // Extract data from the payload
        $username = $data['username'];
        $password = $data['password'];
        $message = $data['message'];
        $groupName = $data['group_name'];

        try {
            // Create a new message in the database
            $messageId = $this->messageModel->createMessage($username, $message, $groupName);

            // Prepare the response data
            $responseData = [
                'status' => 'success',
                'message_id' => $messageId
            ];

            // Set the response body with JSON and content type
            $response->getBody()->write(json_encode($responseData));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            // Prepare the error response
            $errorResponse = [
                'status' => 'error',
                'message' => $e->getMessage()
            ];

            // Set the error response body with JSON and content type
            $response->getBody()->write(json_encode($errorResponse));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    }

    // Method to handle retrieving messages by group
    public function getMessages(Request $request, Response $response, $args)
    {
        $rawdata = (string) $request->getBody();
        $data = json_decode($rawdata, true);
        $groupName = $data['group_name']; // Get the group name from the URL parameter
        $userName = $data['username'];
        $password = $data['password'];

        // Verify user credentials
        $isValidUser = $this->userModel->verifyUserCredentials($userName, $password);

        if (!$isValidUser) {
            $response = $response->withStatus(401)
                ->withHeader('Content-Type', 'application/json');
            $response->getBody()->write(json_encode(['error' => 'Invalid username or password']));
            return $response;
        }

        // Check if the group exists
        $isValidGroupName = $this->groupModel->doesGroupExist($groupName);  // Fetch group ID based on group name

        if (!$isValidGroupName) {
            $response = $response->withStatus(404)
                ->withHeader('Content-Type', 'application/json');
            $response->getBody()->write(json_encode(['error' => 'Group not found']));
            return $response;
        }

        // Check if the user is in the group
        if (!$this->groupModel->isUserInGroup($groupName, $userName)) {
            $response = $response->withStatus(400)
                ->withHeader('Content-Type', 'application/json');
            $response->getBody()->write(json_encode(['message' => 'User not belongs group.']));
            error_log("done ");
            return $response;
        }

        try {
                // Fetch messages for the specified group

            $messages = $this->messageModel->getMessagesByGroup($groupName);

            // Prepare the response data
            $responseData = [
                'status' => 'success',
                'messages' => $messages
            ];

            // Set the response body with JSON and content type
            $response->getBody()->write(json_encode($responseData));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            // Prepare the error response
            $errorResponse = [
                'status' => 'error',
                'message' => $e->getMessage()
            ];

            // Set the error response body with JSON and content type
            $response->getBody()->write(json_encode($errorResponse));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    }
}


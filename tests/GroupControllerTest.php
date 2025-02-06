<?php

use PHPUnit\Framework\TestCase;
use Mockery;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use App\Controllers\GroupController;
use App\Models\UserModel;
use App\Models\GroupModel;

class GroupControllerTest extends TestCase
{
    // Clean up Mockery after each test
    protected function tearDown(): void
    {
        Mockery::close();
    }

    public function testCreateGroupInvalidInput()
    {
        // Mock request with missing fields
        $mockRequest = Mockery::mock(ServerRequestInterface::class);
        $mockRequest->shouldReceive('getBody')->andReturn(json_encode([
            'username' => '',  // Empty username
            'password' => '',  // Empty password
            'group_name' => 'testGroup',
            'description' => 'Test group'
        ]));

        // Mock response and stream
        $mockResponse = Mockery::mock(ResponseInterface::class);
        $mockStream = Mockery::mock(StreamInterface::class);
        $mockResponse->shouldReceive('getBody')->andReturn($mockStream);
        $mockResponse->shouldReceive('withHeader')->with('Content-Type', 'application/json')->andReturnSelf();
        $mockResponse->shouldReceive('withStatus')->with(400)->andReturnSelf(); // Mock withStatus for 400
	$mockResponse->shouldReceive('getStatusCode')->andReturn(400);
        // Mock write on stream
        $mockStream->shouldReceive('write')->once()->with(json_encode(['error' => 'Invalid input']));

        // Test invalid input scenario (empty username or password)
        $controller = new GroupController();
        $response = $controller->createGroup($mockRequest, $mockResponse);

        // Assert the response has status 400
        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testCreateGroupUserNotValid()
    {
        // Mock request with valid fields but invalid user credentials
        $mockRequest = Mockery::mock(ServerRequestInterface::class);
        $mockRequest->shouldReceive('getBody')->andReturn(json_encode([
            'username' => 'invaliduser',  // Invalid username
            'password' => 'password',
            'group_name' => 'testGroup',
            'description' => 'Test group'
        ]));

        // Mock response and stream
        $mockResponse = Mockery::mock(ResponseInterface::class);
        $mockStream = Mockery::mock(StreamInterface::class);
        $mockResponse->shouldReceive('getBody')->andReturn($mockStream);
        $mockResponse->shouldReceive('withHeader')->with('Content-Type', 'application/json')->andReturnSelf();
        $mockResponse->shouldReceive('withStatus')->with(401)->andReturnSelf(); // Mock withStatus for 401
	$mockResponse->shouldReceive('getStatusCode')->andReturn(401);
        // Mock UserModel verifyUserCredentials to return false
        $mockUserModel = Mockery::mock(UserModel::class);
        $mockUserModel->shouldReceive('verifyUserCredentials')->with('invaliduser', 'password')->andReturn(false);

        $controller = new GroupController($mockUserModel, new GroupModel());

        // Mock write on stream
        $mockStream->shouldReceive('write')->once()->with(json_encode(['error' => 'Invalid username or password']));

        // Call the createGroup method
        $response = $controller->createGroup($mockRequest, $mockResponse);

        // Assert the response has status 401
        $this->assertEquals(401, $response->getStatusCode());
    }


    public function testJoinGroupInvalidInput()
    {
        // Mock request with missing fields
        $mockRequest = Mockery::mock(ServerRequestInterface::class);
        $mockRequest->shouldReceive('getBody')->andReturn(json_encode([
            'username' => '',  // Empty username
            'password' => '',
            'group_name' => 'testGroup'
        ]));

        // Mock response and stream
        $mockResponse = Mockery::mock(ResponseInterface::class);
        $mockStream = Mockery::mock(StreamInterface::class);
        $mockResponse->shouldReceive('getBody')->andReturn($mockStream);
        $mockResponse->shouldReceive('withHeader')->with('Content-Type', 'application/json')->andReturnSelf();
        $mockResponse->shouldReceive('withStatus')->with(400)->andReturnSelf(); // Mock withStatus for 400
	$mockResponse->shouldReceive('getStatusCode')->andReturn(400);
        // Mock write on stream
        $mockStream->shouldReceive('write')->once()->with(json_encode(['error' => 'Invalid input']));

        // Test invalid input scenario (empty username or password)
        $controller = new GroupController();
        $response = $controller->joinGroup($mockRequest, $mockResponse, []);

        // Assert the response has status 400
        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testJoinGroupUserNotValid()
    {
        // Mock request with valid fields but invalid user credentials
        $mockRequest = Mockery::mock(ServerRequestInterface::class);
        $mockRequest->shouldReceive('getBody')->andReturn(json_encode([
            'username' => 'invaliduser',  // Invalid username
            'password' => 'password',
            'group_name' => 'testGroup'
        ]));

        // Mock response and stream
        $mockResponse = Mockery::mock(ResponseInterface::class);
        $mockStream = Mockery::mock(StreamInterface::class);
        $mockResponse->shouldReceive('getBody')->andReturn($mockStream);
        $mockResponse->shouldReceive('withHeader')->with('Content-Type', 'application/json')->andReturnSelf();
        $mockResponse->shouldReceive('withStatus')->with(401)->andReturnSelf(); // Mock withStatus for 401
	$mockResponse->shouldReceive('getStatusCode')->andReturn(401);
        // Mock UserModel verifyUserCredentials to return false
        $mockUserModel = Mockery::mock(UserModel::class);
        $mockUserModel->shouldReceive('verifyUserCredentials')->with('invaliduser', 'password')->andReturn(false);

        // Mock write on stream
        $mockStream->shouldReceive('write')->once()->with(json_encode(['error' => 'Invalid username or password']));

        $controller = new GroupController($mockUserModel, new GroupModel());

        // Call the joinGroup method
        $response = $controller->joinGroup($mockRequest, $mockResponse, []);

        // Assert the response has status 401
        $this->assertEquals(401, $response->getStatusCode());
    }
}

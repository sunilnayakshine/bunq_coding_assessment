<?php

use PHPUnit\Framework\TestCase;
use Mockery;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use App\Controllers\UserController;

class UserControllerTest extends TestCase
{
    // Clean up Mockery after each test
    protected function tearDown(): void
    {
        Mockery::close();
    }


    public function testAddUserEmptyUsername()
    {
        // Create a mock request object with an empty username
        $mockRequest = Mockery::mock(ServerRequestInterface::class);
        $mockRequest->shouldReceive('getParsedBody')->andReturn(['username' => '', 'password' => 'password']);
        $mockRequest->shouldReceive('getBody')->andReturn(''); // Simulating body response

        // Create a mock response object with an expectation for getBody() and status code
        $mockResponse = Mockery::mock(ResponseInterface::class);
        $mockStream = Mockery::mock(StreamInterface::class);

        // Set up expectation for write on the stream
        $mockStream->shouldReceive('write')->andReturnUsing(function($argument) {
            echo "Captured write argument: $argument\n";
            return $argument;
        });

        // Set expectations for response methods
        $mockResponse->shouldReceive('withHeader')->with('Content-Type', 'application/json')->andReturnSelf();
        $mockResponse->shouldReceive('getBody')->andReturn($mockStream);
        $mockResponse->shouldReceive('withStatus')->with(400)->andReturnSelf();  // Expect status 400
        $mockResponse->shouldReceive('getStatusCode')->andReturn(400);  // Mock getStatusCode for 400

        // Call the addUser method
        $controller = new UserController();
        $response = $controller->addUser($mockRequest, $mockResponse);

        // Assert that the response has the expected status code
        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testAddUserEmptyPassword()
    {
        // Create a mock request object with an empty password
        $mockRequest = Mockery::mock(ServerRequestInterface::class);
        $mockRequest->shouldReceive('getParsedBody')->andReturn(['username' => 'testuser', 'password' => '']);
        $mockRequest->shouldReceive('getBody')->andReturn(''); // Simulating body response

        // Create a mock response object with an expectation for getBody() and status code
        $mockResponse = Mockery::mock(ResponseInterface::class);
        $mockStream = Mockery::mock(StreamInterface::class);

        // Set up expectation for write on the stream
        $mockStream->shouldReceive('write')->andReturnUsing(function($argument) {
            echo "Captured write argument: $argument\n";
            return $argument;
        });

        // Set expectations for response methods
        $mockResponse->shouldReceive('withHeader')->with('Content-Type', 'application/json')->andReturnSelf();
        $mockResponse->shouldReceive('getBody')->andReturn($mockStream);
        $mockResponse->shouldReceive('withStatus')->with(400)->andReturnSelf();  // Expect status 400
        $mockResponse->shouldReceive('getStatusCode')->andReturn(400);  // Mock getStatusCode for 400

        // Call the addUser method
        $controller = new UserController();
        $response = $controller->addUser($mockRequest, $mockResponse);

        // Assert that the response has the expected status code
        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testAddUserException()
    {
        // Create a mock request object with an existing username
        $mockRequest = Mockery::mock(ServerRequestInterface::class);
        $mockRequest->shouldReceive('getParsedBody')->andReturn(['username' => 'existinguser', 'password' => 'password']);
        $mockRequest->shouldReceive('getBody')->andReturn(''); // Simulating body response

        // Create a mock response object with an expectation for getBody() and status code
        $mockResponse = Mockery::mock(ResponseInterface::class);
        $mockStream = Mockery::mock(StreamInterface::class);

        // Set up expectation for write on the stream
        $mockStream->shouldReceive('write')->andReturnUsing(function($argument) {
            echo "Captured write argument: $argument\n";
            return $argument;
        });

        // Set expectations for response methods
        $mockResponse->shouldReceive('withHeader')->with('Content-Type', 'application/json')->andReturnSelf();
        $mockResponse->shouldReceive('getBody')->andReturn($mockStream);
        $mockResponse->shouldReceive('withStatus')->with(400)->andReturnSelf();  // Expect status 400
        $mockResponse->shouldReceive('getStatusCode')->andReturn(400);  // Mock getStatusCode for 400

        // Call the addUser method
        $controller = new UserController();
        $response = $controller->addUser($mockRequest, $mockResponse);

        // Assert that the response has the expected status code
        $this->assertEquals(400, $response->getStatusCode());
    }
}


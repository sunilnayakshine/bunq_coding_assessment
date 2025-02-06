<?php

use PHPUnit\Framework\TestCase;
use App\Models\UserModel;

class UserModelTest extends TestCase
{
    private $userModel;

    protected function setUp(): void
    {
        // Use an in-memory SQLite database for testing
        $pdo = new PDO('sqlite::memory:');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Create a mock database schema
        $pdo->exec('CREATE TABLE users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL UNIQUE,
            password TEXT NOT NULL
        );');

        // Mock the UserModel to use the in-memory database
        $this->userModel = new UserModel();
        $reflection = new ReflectionClass(UserModel::class);
        $property = $reflection->getProperty('db');
        $property->setAccessible(true);
        $property->setValue($this->userModel, $pdo);
    }

    public function testCreateUserSuccess(): void
    {
        $username = 'testuser';
        $password = 'password123';

        // Call createUser and verify the result
        $result = $this->userModel->createUser($username, $password);

        $this->assertIsString($result, 'Result should be a string.');
        $this->assertEquals($username, $result, 'The returned username does not match.');
    }

    public function testCreateUserAlreadyExists(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Username already exists');

        $username = 'testuser';
        $password = 'password123';

        $this->userModel->createUser($username, $password);
        $this->userModel->createUser($username, $password);
    }

    public function testVerifyUserCredentialsSuccess(): void
    {
        $username = 'testuser';
        $password = 'password123';

        $this->userModel->createUser($username, $password);

        $isValid = $this->userModel->verifyUserCredentials($username, $password);
        $this->assertTrue($isValid);
    }

    public function testVerifyUserCredentialsFailure(): void
    {
        $username = 'testuser';
        $password = 'password123';

        $this->userModel->createUser($username, $password);

        $isValid = $this->userModel->verifyUserCredentials($username, 'wrongpassword');
        $this->assertFalse($isValid);
    }

    protected function tearDown(): void
    {
        unset($this->userModel);
    }
}


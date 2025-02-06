<?php
use PHPUnit\Framework\TestCase;
use App\Models\MessageModel;
use PDO;

class MessageModelTest extends TestCase
{
    private $messageModel;
    private $pdo;

    protected function setUp(): void
    {
        // Use an in-memory SQLite database for testing
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Create mock schema for testing
        $this->pdo->exec('CREATE TABLE messages (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL,
            message TEXT NOT NULL,
            group_name TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );');

        // Mock the MessageModel to use the in-memory database
        $this->messageModel = new MessageModel();
        $reflection = new ReflectionClass(MessageModel::class);
        $property = $reflection->getProperty('db');
        $property->setAccessible(true);
        $property->setValue($this->messageModel, $this->pdo);
    }

    public function testCreateMessage()
    {
        // Test data
        $username = 'user1';
        $message = 'Hello, this is a test message!';
        $groupname = 'test_group';

        // Call the createMessage method
        $messageId = $this->messageModel->createMessage($username, $message, $groupname);

        // Assert the message was created successfully
        $this->assertIsInt($messageId, 'Message ID should be an integer');

        // Fetch the inserted message from the database
        $stmt = $this->pdo->query('SELECT * FROM messages WHERE id = ' . $messageId);
        $messageData = $stmt->fetch(PDO::FETCH_ASSOC);

        // Assert the message data
        $this->assertNotEmpty($messageData, 'Message should exist in the database');
        $this->assertEquals($username, $messageData['username']);
        $this->assertEquals($message, $messageData['message']);
        $this->assertEquals($groupname, $messageData['group_name']);
    }

    public function testGetMessagesByGroup()
    {
        // Test data
        $username1 = 'user1';
        $message1 = 'Message 1';
        $groupname = 'test_group';

        $username2 = 'user2';
        $message2 = 'Message 2';

        // Insert two messages into the database with different timestamps
        $this->pdo->exec("INSERT INTO messages (username, message, group_name, created_at)
                           VALUES ('$username1', '$message1', '$groupname', '2025-02-06 12:00:00')");
        $this->pdo->exec("INSERT INTO messages (username, message, group_name, created_at)
                           VALUES ('$username2', '$message2', '$groupname', '2025-02-06 12:01:00')");

        // Fetch messages by group
        $messages = $this->messageModel->getMessagesByGroup($groupname);

        // Assert the messages are fetched correctly
        $this->assertIsArray($messages, 'Messages should be an array');
        $this->assertCount(2, $messages, 'There should be two messages');

        // Assert the message contents in the correct order
        $this->assertEquals($message2, $messages[0]['message'], 'Most recent message should be first');
        $this->assertEquals($message1, $messages[1]['message'], 'Second message should follow the most recent one');
    }

    public function testGetMessagesByGroupThrowsExceptionWhenNoMessages()
    {
        // Test with a group that has no messages
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('No messages found for the group');

        $this->messageModel->getMessagesByGroup('non_existing_group');
    }

    protected function tearDown(): void
    {
        // Clean up by closing the PDO connection
        $this->pdo = null;
    }
}


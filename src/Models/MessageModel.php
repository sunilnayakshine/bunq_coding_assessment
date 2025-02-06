<?php
namespace App\Models;

use PDO;

class MessageModel
{
    private $db;
    private $userModel;

    public function __construct()
    {
        // Database connection
        try {
            $this->db = new PDO('sqlite:/var/www/html/database/chat.db');
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            throw new \Exception('Database connection failed: ' . $e->getMessage());
        }

        // Initialize the UserModel for user authentication
        $this->userModel = new UserModel();
    }

    // Method to create a new message
    public function createMessage(string $username, string $message, string $groupname): int
    {
        // Prepare the insert query
        $stmt = $this->db->prepare('INSERT INTO messages (username, message, group_name)
                                    VALUES (:username, :message, :groupname)');
        if ($stmt === false) {
            throw new \Exception('Failed to prepare the SQL statement');
        }

        // Bind parameters
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':message', $message);
        $stmt->bindParam(':groupname', $groupname);

        // Execute the query and check for errors
        if (!$stmt->execute()) {
            throw new \Exception('Failed to insert the message');
        }

        return $this->db->lastInsertId();
    }

    // Method to fetch messages by group name with detailed logging
    public function getMessagesByGroup(string $groupname): array
    {
        // Prepare the select query
        $stmt = $this->db->prepare('SELECT id, username, message, group_name, created_at FROM messages WHERE group_name = :groupname ORDER BY created_at DESC');
        if ($stmt === false) {
            throw new \Exception('Failed to prepare the SQL statement');
        }

        // Bind the group name parameter
        $stmt->bindParam(':groupname', $groupname);

        // Execute the query
        if (!$stmt->execute()) {
            throw new \Exception('Failed to execute the query');
        }

        // Fetch all the messages for the group
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($messages === false || empty($messages)) {
            throw new \Exception('No messages found for the group');
        }

        return $messages;
    }
}



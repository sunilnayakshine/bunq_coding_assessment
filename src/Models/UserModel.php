<?php

namespace App\Models;
ini_set('memory_limit', '1G');
use PDO;

class UserModel
{
    private $db;

    public function __construct()
    {
        // Database connection
        try {
            $this->db = new PDO('sqlite:/app/database/chat.db');
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            throw new \Exception('Database connection failed: ' . $e->getMessage());
        }

    }

    // Create a new user
    public function createUser(string $username, string $password): string
    {
        // Check if the user already exists
        $stmt = $this->db->prepare('SELECT id FROM users WHERE username = :username');
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($existingUser) {
            // User already exists
            throw new \Exception('Username already exists. Please try another username.');
        }

        // Proceed to insert the new user
        try {
            $stmt = $this->db->prepare('INSERT INTO users (username, password) VALUES (:username, :password)');
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', password_hash($password, PASSWORD_BCRYPT));

            $stmt->execute();
        } catch (\PDOException $e) {
            // Log the error and rethrow it
            error_log("Error in createUser: " . $e->getMessage());
            throw new \Exception("Error creating user: " . $e->getMessage());
        }

        return $username;
    }

    // Verify user credentials
    public function verifyUserCredentials(string $username, string $password): bool
    {
        $stmt = $this->db->prepare('SELECT password FROM users WHERE username = :username');
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($user && password_verify($password, $user['password'])) {
            return true;
        }

        return false;
    }

}

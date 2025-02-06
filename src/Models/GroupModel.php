<?php

namespace App\Models;

use PDO;

class GroupModel
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
        $this->createTables();
    }

    // Create necessary tables (groups, group_members)
    private function createTables()
    {
        // Create groups table if it doesn't exist
        $query = '
            CREATE TABLE IF NOT EXISTS groups (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                group_name TEXT NOT NULL,
                group_type TEXT NOT NULL
            )
        ';
        $this->db->exec($query);

        // Create group_members table to handle user-group relationships
        $query = '
            CREATE TABLE IF NOT EXISTS group_members (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                group_id INTEGER,
                user_id INTEGER,
                FOREIGN KEY (group_id) REFERENCES groups(id),
                FOREIGN KEY (user_id) REFERENCES users(id)
            )
        ';
        $this->db->exec($query);
    }

    // Create a new group
    public function createGroup(string $groupname, string $groupdescription, string $username): int
    {
        // Prepare the insert query
        $stmt = $this->db->prepare('INSERT INTO groups (group_name, group_description, user_name) VALUES (:groupname, :groupdescription, :username)');
        if ($stmt === false) {
            throw new \Exception('Failed to prepare the SQL statement');
        }

        // Bind parameters
        $stmt->bindParam(':groupname', $groupname);
        $stmt->bindParam(':groupdescription', $groupdescription);
        $stmt->bindParam(':username', $username);

        // Execute the query
        if (!$stmt->execute()) {
            // Log error if execution fails
            error_log('Failed to execute the query for group creation: ' . $stmt->errorInfo());
            throw new \Exception('Failed to execute the query');
        }

        // Return the last inserted group ID
        $groupId = $this->db->lastInsertId();

        // Log the created group ID
        error_log('Created Group: ' . $groupname);

        return $groupId;
    }


        // Get Group ID by Group Name
    public function getGroupIDByName(string $groupname): ?int
    {
        // Prepare the select query
        $stmt = $this->db->prepare('SELECT id FROM groups WHERE group_name = :groupname');
        if ($stmt === false) {
            error_log('Failed to prepare the SQL statement for getting group ID by name');
            throw new \Exception('Failed to prepare the SQL statement');
        }

        // Bind the group name parameter
        $stmt->bindParam(':groupname', $groupname);
        error_log('Fetching group ID for group: ' . $groupname);

        // Execute the query
        if (!$stmt->execute()) {
            error_log('Failed to execute the query: ' . $stmt->errorInfo());
            throw new \Exception('Failed to execute the query');
        }

        // Fetch the group ID
        $group = $stmt->fetch(PDO::FETCH_ASSOC);

        // Log the fetched result
        error_log('Fetched group: ' . print_r($group, true));

        // Return the group ID or null if not found
        return $group ? $group['id'] : null;
    }

    // Check if a user is already in a group
    public function isUserInGroup(string $groupname, string $username): bool
    {
        // Prepare the select query to check if user is in the group
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM group_members WHERE group_name = :groupname AND user_name = :username');
        if ($stmt === false) {
            error_log('Failed to prepare the SQL statement for checking user in group');
            throw new \Exception('Failed to prepare the SQL statement');
        }

        // Bind the parameters
        $stmt->bindParam(':groupname', $groupname);
        $stmt->bindParam(':username', $username);
        error_log('Checking if ' . $username . ' is in group: ' . $groupname);

        // Execute the query
        if (!$stmt->execute()) {
            error_log('Failed to execute the query: ' . $stmt->errorInfo());
            throw new \Exception('Failed to execute the query');
        }

        // Fetch the count
        $count = $stmt->fetchColumn();

        // Log the result
        error_log('User ' . $username . ' is ' . ($count > 0 ? '' : 'not ') . 'in group: ' . $groupname);

        // Return true if user is in the group
        return $count > 0;
    }

    // Add a user to a group
    public function addUserToGroup(string $username, string $groupname): void
    {
        // Prepare the insert query
        $stmt = $this->db->prepare('INSERT INTO group_members (user_name, group_name) VALUES (:username, :groupname)');
        if ($stmt === false) {
            error_log('Failed to prepare the SQL statement for adding user to group');
            throw new \Exception('Failed to prepare the SQL statement');
        }

        // Bind the parameters
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':groupname', $groupname);
        error_log('Adding user: ' . $username . ' to group: ' . $groupname);

        // Execute the query
        if (!$stmt->execute()) {
            error_log('Failed to execute the query: ' . $stmt->errorInfo());
            throw new \Exception('Failed to execute the query');
        }

        // Log the success
        error_log('Successfully added user: ' . $username . ' to group: ' . $groupname);
    }

    // Check if a group exists
    public function doesGroupExist(string $groupname): bool
    {
        // Prepare the select query
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM groups WHERE group_name = :groupname');
        if ($stmt === false) {
            error_log('Failed to prepare the SQL statement for checking group existence');
            throw new \Exception('Failed to prepare the SQL statement');
        }

        // Bind the group name parameter
        $stmt->bindParam(':groupname', $groupname);
        error_log('Checking if group exists: ' . $groupname);

        // Execute the query
        if (!$stmt->execute()) {
            error_log('Failed to execute the query: ' . $stmt->errorInfo());
            throw new \Exception('Failed to execute the query');
        }

        // Fetch the count
        $count = $stmt->fetchColumn();
        error_log('Count of group ' . $groupname . ' existence: ' . $count);

        // Return true if the group exists
        return $count > 0;
    }
}


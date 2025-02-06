<?php

use PHPUnit\Framework\TestCase;
use App\Models\GroupModel;

class GroupModelTest extends TestCase
{
    private $groupModel;

    protected function setUp(): void
    {
        // Use an in-memory SQLite database for testing
        $pdo = new PDO('sqlite::memory:');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Create mock database schema for groups and group_members
        $pdo->exec('CREATE TABLE groups (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            group_name TEXT NOT NULL UNIQUE,
            group_description TEXT NOT NULL,
            user_name TEXT NOT NULL
        );');

        $pdo->exec('CREATE TABLE group_members (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_name TEXT NOT NULL,
            group_name TEXT NOT NULL
        );');

        // Mock the GroupModel to use the in-memory database
        $this->groupModel = new GroupModel();
        $reflection = new ReflectionClass(GroupModel::class);
        $property = $reflection->getProperty('db');
        $property->setAccessible(true);
        $property->setValue($this->groupModel, $pdo);
    }

    public function testCreateGroupSuccess(): void
    {
        $groupname = 'testgroup';
        $groupdescription = 'This is a test group';
        $username = 'testuser';

        // Call createGroup and verify the result
        $groupId = $this->groupModel->createGroup($groupname, $groupdescription, $username);

        $this->assertIsInt($groupId, 'Group ID should be an integer.');
    }

    public function testCreateGroupFailed(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('SQLSTATE[23000]: Integrity constraint violation: 19 UNIQUE constraint failed: groups.group_name');

        $groupname = 'testgroup';
        $groupdescription = 'This is a test group';
        $username = 'testuser';

        // Insert the same group twice to simulate failure
        $this->groupModel->createGroup($groupname, $groupdescription, $username);
        $this->groupModel->createGroup($groupname, $groupdescription, $username); // This should fail
    }

    public function testGetGroupIDByNameSuccess(): void
    {
        $groupname = 'testgroup';
        $groupdescription = 'This is a test group';
        $username = 'testuser';

        $this->groupModel->createGroup($groupname, $groupdescription, $username);

        // Retrieve group ID by name and check if it matches
        $groupId = $this->groupModel->getGroupIDByName($groupname);
        $this->assertIsInt($groupId, 'Group ID should be an integer.');
    }

    public function testGetGroupIDByNameNotFound(): void
    {
        $groupname = 'nonexistentgroup';

        // Try to get the ID of a non-existing group
        $groupId = $this->groupModel->getGroupIDByName($groupname);
        $this->assertNull($groupId, 'Group ID should be null for a non-existent group.');
    }

    public function testIsUserInGroupSuccess(): void
    {
        $groupname = 'testgroup';
        $groupdescription = 'This is a test group';
        $username = 'testuser';

        $this->groupModel->createGroup($groupname, $groupdescription, $username);
        $this->groupModel->addUserToGroup($username, $groupname);

        // Check if the user is in the group
        $isUserInGroup = $this->groupModel->isUserInGroup($groupname, $username);
        $this->assertTrue($isUserInGroup, 'User should be in the group.');
    }

    public function testIsUserInGroupNotFound(): void
    {
        $groupname = 'testgroup';
        $username = 'testuser';

        // Check if a user is in the group without adding them
        $isUserInGroup = $this->groupModel->isUserInGroup($groupname, $username);
        $this->assertFalse($isUserInGroup, 'User should not be in the group.');
    }

    public function testAddUserToGroupSuccess(): void
    {
        $groupname = 'testgroup';
        $groupdescription = 'This is a test group';
        $username = 'testuser';

        $this->groupModel->createGroup($groupname, $groupdescription, $username);

        // Add user to group and verify
        $this->groupModel->addUserToGroup($username, $groupname);

        $isUserInGroup = $this->groupModel->isUserInGroup($groupname, $username);
        $this->assertTrue($isUserInGroup, 'User should be successfully added to the group.');
    }

    public function testDoesGroupExistSuccess(): void
    {
        $groupname = 'testgroup';
        $groupdescription = 'This is a test group';
        $username = 'testuser';

        $this->groupModel->createGroup($groupname, $groupdescription, $username);

        // Check if the group exists
        $groupExists = $this->groupModel->doesGroupExist($groupname);
        $this->assertTrue($groupExists, 'The group should exist.');
    }

    public function testDoesGroupExistFailure(): void
    {
        $groupname = 'nonexistentgroup';

        // Check if a non-existing group exists
        $groupExists = $this->groupModel->doesGroupExist($groupname);
        $this->assertFalse($groupExists, 'The non-existing group should not exist.');
    }

    protected function tearDown(): void
    {
        unset($this->groupModel);
    }
}


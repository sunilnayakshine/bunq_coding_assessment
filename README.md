
# Bunq Chat App [Online Assessment ]

This repository contains a Slim-based RESTful API for managing users, groups, and messages. The API supports operations such as signing up users, creating/joining groups, and sending/retrieving messages.

To  make the coding run easy I've containerized the code, so whoever reviewing this code can simply start the container and hit API for testing. 

## Table of Contents

- [Installation](#installation)
- [API Endpoints](#api-endpoints)
- [Usage](#usage)
- [Functionality Testing](#functionality-testing)
- [Unit Testing](#testing)


## Installation

To set up the project locally, follow these steps:

1. Clone the repository:
    ```bash
    git clone https://github.com/sunilnayakshine/bunq_coding_assessment.git
    cd bunq_coding_assessment
    ```

   

2. Build the Docker Image:
    ```bash
    docker build -t bunq-chat-back-api . 
    ```
3. Strat the server on port 8080.
	```bash
	docker run -p 8080:8080 bunq-chat-back-api
 	```


Now the API should be up and running at `http://localhost:8080`.

## API Endpoints

### 1. Sign Up User
- **POST** `/sign-up`
    - Description: Allows a new user to sign up.
    - Request Body:
        ```json
        {
            "username": "string",
            "password": "string"
        }
        ```
    - Response: 
        - `200 OK`: User signed up successfully.
        - `400 Bad Request`: Invalid input data.
        - `409 Conflict`: Username already exists.

### 2. Create Group
- **POST** `/create-group`
    - Description: Allows the creation of a new group.
    - Request Body:
        ```json
        {
            "username": "string",
            "password": "string"
            "group_name": "string",
            "description": "string"
            
        }
        ```
    - Response:
        - `200 Created`: Group created successfully.
        - `400 Bad Request`: Invalid group name.
        -  `409 Conflict`: Group name already exists.

### 3. Join Group
- **POST** `/join-group`
    - Description: Allows a user to join an existing group.
    - Request Body:
        ```json
        {
            "username": "string",
            "password": "string",
            "group_name": "string"
        }
        ```
    - Response:
        - `200 OK`: User joined the group successfully.
        - `404 Not Found`: Group not found.
        - `400 Bad Request`: Invalid user or group name.

### 4. Send Message
- **POST** `/send-message`
    - Description: Allows a user to send a message to a group.
    - Request Body:
        ```json
        {
            "username": "string",
            "password": "strinf",
            "group_name": "string",
            "message": "string"
        }
        ```
    - Response:
        - `200 OK`: Message sent successfully.
        - `400 Bad Request`: Invalid input data.
        - `404 Not Found`: Group not found.

### 5. Get Messages
- **POST** `/get-message`
    - Description: Allows a user to retrieve messages from a group.
    - Request Body:
        ```json
        {
            "username": "string",
            "password": "password",
            "group_name": "string"
        }
        ```
    - Response:
        - `200 OK`: Messages returned successfully.
        - `404 Not Found`: Group not found.
        - `400 Bad Request`: Invalid input data.

## Usage

You can interact with the API by sending HTTP requests to the specified endpoints.

### Example: Sign Up a User
```bash
curl -X POST http://localhost:8080/sign-up \
    -H "Content-Type: application/json" \
    -d '{
        "username": "user1",
        "password": "password123"
    }'
``` 
### Example: Create a new group

```bash
curl -X POST http://localhost:8080/create-group \
-H "Content-Type: application/json" \
-d '{
	 "username": "user1", 
	 "password": "password123",
	 "description": "Online test",
	 "group_name": "Bunq Chat Group"
 }'
``` 
### Example: Join group
```bash
curl -v -X POST http://localhost:8080/join-group \
-H "Content-Type: application/json" \
-d '{
	  "username": "user1",
	  "password": "password123",
	  "group_name": "Bunq Chat Group'
}'
``` 
### Example: Send Message
```bash
curl -v -X POST http://localhost:8080/send-message \
-H "Content-Type: application/json" \
-d '{
	  "username": "testuser1", 
      "password": "password123",
	  "message": "Please review the code",
	  "group_name": "Bunq Chat Group"
"}'
```

### Example: Get Messages
```bash
curl -v -X POST http://localhost:8080/get-message \
-H "Content-Type: application/json" \
-d '{
	  "username": "testuser1",
	  "password": "password123",
	  "group_name": "Bunq Chat Group"
}'
```

## Functionality Testing

To make the code reviewer life easier I'm dropping a payload to test and verify the features of Bunq Chat APP. Just try it out..

1. What if I sign up with username that already exists ?(Run twice)
```bash
curl -X POST http://localhost:8080/sign-up \
-H "Content-Type: application/json" \
-d '{
      "username": "ImThere",
      "password": "password123"
    }'
```
2. What if I'm trying to access Bunq Chat API without sign up ?
```bash
curl -X POST http://localhost:8080/create-group \
-H "Content-Type: application/json" \
-d '{
	 "username": "ImNotThere", 
	 "password": "password123",
	 "description": "Online test",
	 "group_name": "Bunq Chat Group"
 }'
``` 

3. What if I'm trying to create group that already exists ? (Run this is twice)
```bash
curl -v -X POST http://localhost:8080/join-group \
-H "Content-Type: application/json" \
-d '{
	  "username": "user1",
	  "password": "password123",
	  "group_name": "Bunq Chat Group"
"}'
```

4. What if I'm trying to join the group that doesn't exists ? 
```bash
curl -v -X POST http://localhost:8080/join-group \
-H "Content-Type: application/json" \
-d '{
	  "username": "user1",
	  "password": "password123",
	  "group_name": "Bank Of The Free"
"}'
```
5. What if I'm trying to read the message which I was not part of ? (Assuming that your already singed up)

```bash
curl -v -X POST http://localhost:8080/join-group \
-H "Content-Type: application/json" \
-d '{
	  "username": "ImThere",
	  "password": "password123",
	  "group_name": "Bunq Chat Group"
"}'
curl -v -X POST http://localhost:8080/get-message \
-H "Content-Type: application/json" \
-d '{
	  "username": "ImThere",
	  "password": "password123",
	  "group_name": "Bank Of The Free"
}'
``` 
and many more. Please feel free to explore the API and functionality. 
  
 ## Unit Testing
To run tests for the API:
```bash
./vendor/bin/phpunit.php tests
``` 

# Dependencies

This project uses the following dependencies:

## Required Dependencies
The following libraries are required to run the application:
- **Slim Framework** (`slim/slim`): A lightweight framework for building RESTful APIs. Version: `^4.0`.
- **PSR-7 Implementation** (`slim/psr7`): Handles HTTP message implementation. Version: `^1.5`.
- **PDO SQLite Extension** (`ext-pdo_sqlite`): Required for database access.

## Development Dependencies
For testing and development purposes, the following libraries are used:
- **PHPUnit** (`phpunit/phpunit`): Unit testing framework. Version: `^9.6`.
- **Mockery** (`mockery/mockery`): Mocking library for PHPUnit. Version: `^1.6`.

## Composer Configuration
Below is the `composer.json` file for this project:

```json
{
    "name": "bunq/online",
    "require": {
        "slim/slim": "^4.0",
        "slim/psr7": "^1.5",
        "ext-pdo_sqlite": "*"
    },
    "autoload": {
        "psr-4": {
            "App\\": "src/"
        }
    },
    "require-dev": {
        "phpunit/phpunit": "^9.6",
        "mockery/mockery": "^1.6"
    }
}

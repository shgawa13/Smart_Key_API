<?php

class Users
{
    public function __construct(private UsersGatway $gatway){}
    
    // how can i implement login to this class?
    // processRequest   
    /**
     * Processes the incoming request based on the HTTP method and ID.
     *
     * @param string $request The HTTP request method (GET, POST, PUT, DELETE).
     * @param string $id The ID of the user (if applicable).
     * 
     */
    public function processRequest(string $request, string $id): void
    {

        switch ($request) {
            case 'GET':
                if ($id !=="") {           // ← id   → single
                    $this->getUser($id);
                } else {                     // ← no id → list
                    $this->getAllUsers();
                }
                break; 
            case 'POST':
                $data = (array)json_decode(file_get_contents("php://input"), true);
                $this->addNewUser($data);
                break;
            case 'PUT':
                $data = json_decode(file_get_contents('php://input'), true);
                // optional: bad-JSON guard
                if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
                    http_response_code(400);
                    echo json_encode(['error' => 'Invalid JSON in request body']);
                    break;
                }
                $current = $this->gatway->getById($id);
                if (!$current) {
                    http_response_code(404);
                    echo json_encode(['error' => "User not found with ID: $id"]);
                    break;
                }
                $this->updateUser($current, $data);
                break;
            case 'DELETE':
                $this->deleteUser($id);
                break;
            default:
                http_response_code(405);
                echo json_encode(['error' => 'Method not allowed']);
        }

    }
    // here we will handle the login request
    public function processLoginRequest(string $request, string $data): void
    {
        switch ($request)
        {
            case 'POST':
                $data = (array)json_decode(file_get_contents("php://input"), true);
                if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
                    http_response_code(400);
                    echo json_encode(['error' => 'Invalid JSON in request body']);
                    break;
                }

                $this->Login($data);
                break;

                

        }
    }
    
    
    // addNewUser
    private function addNewUser(array $data): void
    {
        // if new user is added return the ID of the new user
        $errors = $this->getValidationErrors($data);
        if ($errors) {
            http_response_code(422);
            echo json_encode(['error' => 'Validation errors', 'details' => $errors]);
            return;
        }

        $id = $this->gatway->addNew($data);
        if ($id === 0) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to add new user']);
            return;
        };

        http_response_code(201);
        echo json_encode(['message' => 'User added successfully', 'id' => $id]);
    }
    // getUser
    private function getUser(string $id): void
    {
        // Logic to get a user by ID
        $res = $this->gatway->getById($id);
        if ($res === false) {
            http_response_code(404);
            echo json_encode(['error' => 'User not found with ID: ' . $id]);
            return;
        };
        http_response_code(200);
        echo json_encode($res);
    }

    // getAllUsers
    private function getAllUsers(): void
    {
        // Logic to get all users
        $res = $this->gatway->getAll();
        if ($res === []) {
            http_response_code(404);
            echo json_encode(['error' => 'No users found']);
            return;
        };
        http_response_code(200);
        echo json_encode($res);
    }

    // updateUser
    private function updateUser(array $current,array $data): void
    {
        $errors = $this->getValidationErrors($data);
        if ($errors) {
            http_response_code(422);
            echo json_encode(['error' => 'Validation errors', 'details' => $errors]);
            return;
        }
    
        if (!$current) {                       // row really is missing
            http_response_code(404);
            echo json_encode(['error' => "Customer not found with ID: {$current['CustomerID']}"]);
            return;
        }
    
        // 2. try to update
        $rows = $this->gatway->update($current, $data);
    
        http_response_code(200);
        echo json_encode(['message' => 'User updated successfully ', 'rows' => $rows]);
    }

    // deleteUser
    private function deleteUser(string $id): void
    {
        // // Logic to delete a user by ID
        $res = $this->gatway->delete($id);
        if ($res === false) {
            http_response_code(404);
            echo json_encode(['error' => 'Failed to delete user with ID: ' . $id]);
            return;
        };
        http_response_code(200);
        echo json_encode(['message' => 'User deleted successfully']);
    }


    function Login(array $data): array|false 
    {
        // Validate the input data
        $errors = $this->getValidationErrorsForLogin($data);
        if (!empty($errors)) {
            http_response_code(422);
            echo json_encode(['error' => 'Validation errors', 'details' => $errors]);
            return false;
        }
        // Attempt to log in the user
        $user = $this->gatway->Login($data);
        if ($user === false) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid credentials']);
            return false;
        }
        // If login is successful, return the user data
        http_response_code(200);
        echo json_encode(['message' => 'Login successful', 'user' => $user]);
        return $user;
        
    }

    // getValidationErrors
    private function getValidationErrors(array $data): array
    {
        $errors = [];
        $required = [
            'UserName',
            'Password',
            'Email',
            'IsAdmin',

            ];

            foreach ($required as $field) {
                if (!isset($data[$field]) || $data[$field] === '') {
                    $errors[$field] = "$field is required";
                }
            }
        return $errors;
    }

    private function getValidationErrorsForLogin(array $data): array
    {
        $errors = [];
        $required = [
            'Email',
            'Password',
        ];

        foreach ($required as $field) {
            if (!isset($data[$field]) || $data[$field] === '') {
                $errors[$field] = "$field is required";
            }
        }
        return $errors;
    }
};





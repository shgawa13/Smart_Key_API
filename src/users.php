<?php

class Users
{
    public function __construct(private UsersGatway $gatway){}
    

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
                $this->updateUser($id);
                break;
            case 'DELETE':
                $this->deleteUser($id);
                break;
            default:
                http_response_code(405);
                echo json_encode(['error' => 'Method not allowed']);
        }

    }
    
    // addNewUser
    private function addNewUser(array $data): void
    {
        // if new user is added return the ID of the new user
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
        if ($res === false) {
            http_response_code(404);
            echo json_encode(['error' => 'No users found']);
            return;
        };
        http_response_code(200);
        echo json_encode($res);
    }

    // updateUser
    private function updateUser(string $id): void
    {
        // // Logic to update a user by ID
        // $data = (array)json_decode(file_get_contents("php://input"), true);
        // $res = $this->gatway->update($id, $data);
        // if ($res === false) {
        //     http_response_code(404);
        //     echo json_encode(['error' => 'Failed to update user with ID: ' . $id]);
        //     return;
        // };
        // http_response_code(200);
        // echo json_encode(['message' => 'User updated successfully']);
    }

    // deleteUser
    private function deleteUser(string $id): void
    {
        // // Logic to delete a user by ID
        // $res = $this->gatway->delete($id);
        // if ($res === false) {
        //     http_response_code(404);
        //     echo json_encode(['error' => 'Failed to delete user with ID: ' . $id]);
        //     return;
        // };
        // http_response_code(200);
        // echo json_encode(['message' => 'User deleted successfully']);
    }
};





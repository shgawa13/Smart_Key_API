<?php

class Customers
{
    public function __construct(private CustomersGatway $gatway)
    {
        
    }

    public function processRequest(string $request, ?string $id): void
    {
        switch ($request) {
            case 'GET':
                if ($id !=="") {           // ← id   → single
                     $this->getCustomer($id);
                } else {                     // ← no id → list
                    $this->getAllCustomers();
                }
                break; 

            case 'POST':
                $data = (array)json_decode(file_get_contents("php://input"), true);
                $this->addNewCustomer($data);
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
                    echo json_encode(['error' => "Customer not found with ID: $id"]);
                    break;
                }

                $this->updateCustomer($current, $data);
                break;

            case 'DELETE':
                $this->deleteCustomer($id);
                break;

            default:
                http_response_code(405);
                echo json_encode(['error' => 'Method not allowed']);
        }

    }
    // addNewCustomer
    private function addNewCustomer(array $data): void
    {
        $errors = $this->getValidationErrors($data);
        if (!empty($errors)) {
            http_response_code(422);
            echo json_encode(['error' => 'Validation errors', 'details' => $errors]);
            return;
        }
        
        // if new customer is added return the ID of the new customer
        $id = $this->gatway->addNew($data);
        if ($id === 0) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to add new customer']);
            return;
        };

        http_response_code(201);
        echo json_encode(['message' => 'Customer added successfully', 'id' => $id]);
    }


    private function getCustomer(int $id): void
    {
        // Logic to get a customer by ID
        $res = $this->gatway->getById($id);
        if ($res === false) {
            http_response_code(404);
            echo json_encode(['error' => 'Customer not found with ID: ' . $id]);
            return;
        }else{
            http_response_code(200);
            echo json_encode($this->gatway->getById($id));
        }
        
    }

    // getAllCustomers
    private function getAllCustomers(): void 
    {
        
        echo json_encode($this->gatway->getAll());
    }

    private function updateCustomer(array $current, array $data): void
    {
        // 1. validate
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

    // 3. handle result

    http_response_code(200);
    echo json_encode(['message' => 'Customer updated successfully' . $rows . ' rows updated']);
    }

    private function deleteCustomer(string $id): void
    {
        $res = $this->gatway->delete($id);
        if ($res === false) {
            http_response_code(404);
            echo json_encode(['error' => 'Customer not found with ID: ' . $id]);
            return;
        }
        http_response_code(204);
        echo json_encode(['message' => 'Customer deleted successfully']);

    }

    private function getValidationErrors(array $data): array
    {
        // list the fields that are mandatory
        $required = [
            'FirstName',
            'SecondName',
            'LastName',
            'DateOfBirth',
            'Gender',
            'PhoneNumber',
            'DriverLicenseNumber',
        ];

        $errors = [];

        foreach ($required as $field) {
            if (empty($data[$field])) {
                $errors[] = "$field is required";
            }
        }

        return $errors;
    }
}


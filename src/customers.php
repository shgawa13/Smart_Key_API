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
                //$this->addNewCustomer();
                $data = (array)json_decode(file_get_contents("php://input"), true);
                $this->addNewCustomer($data);
                break;
            case 'PUT':
                $this->updateCustomer($id);
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


    private function getCustomer(string $id): void
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
    private function getAllCustomers(): void 
    {
        //  get all customers
        echo json_encode($this->gatway->getAll());
    }

    private function updateCustomer(string $id): void
    {
        // Logic to update a customer by ID
        echo json_encode(['message' => 'Update customer', 'id' => $id]);
    }
    private function deleteCustomer(string $id): void
    {
        // Logic to delete a customer by ID
        echo json_encode(['message' => 'Delete customer', 'id' => $id]);
    }

}


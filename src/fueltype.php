<?php

class FuelType
{
    private int $FuelTypeID;
    private string $FuelTypeName;

    public function __construct(private FuelTypeGatway $gatway){}

    public function processRequest(string $request, ?string $id): void
    {
        switch ($request) {
            case 'GET':
                if ($id !=="") {           // ← id   → single
                     $this->getFuelType($id);
                } else {                     // ← no id → list
                    $this->getAllFuelTypes();
                }
                break; 

            case 'POST':
                $data = (array)json_decode(file_get_contents("php://input"), true);
                $this->addNewFuelType($data);
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
                    echo json_encode(['error' => "FuelType not found with ID: $id"]);
                    break;
                }
                $this->updateFuelType($current, $data);
                break;
            case 'DELETE':
                $this->deleteFuelType($id);
                break;
            default:
                http_response_code(405);
                echo json_encode(['error' => 'Method not allowed']);
        }
    }
    // addNewFuelType
    private function addNewFuelType(array $data): void
    {
        $errors = $this->getValidationErrors($data);
        if (!empty($errors)) {
            http_response_code(422);
            echo json_encode(['error' => 'Validation errors', 'details' => $errors]);
            return;
        }
        
        $id = $this->gatway->addNew($data);
        http_response_code(201);
        echo json_encode(['message' => 'FuelType added successfully ', 'id' => $id]);
    }
    // get all fuel types
    private function getAllFuelTypes(): void
    {
        $data = $this->gatway->getAll();
        if (empty($data)) {
            http_response_code(404);
            echo json_encode(['error' => 'No fuel types found']);
            return;
        }
        http_response_code(200);
        echo json_encode($data);
    }
    // get fuel type by ID
    private function getFuelType(string $id): void
    {
        $data = $this->gatway->getById($id);
        if (empty($data)) {
            http_response_code(404);
            echo json_encode(['error' => "FuelType with ID: $id not found"]);
            return;
        }
        http_response_code(200);
        echo json_encode($data);
    }
    // update fuel type
    private function updateFuelType(array $current, array $data): void
    {
        $errors = $this->getValidationErrors($data);
        if (!empty($errors)) {
            http_response_code(422);
            echo json_encode(['error' => 'Validation errors', 'details' => $errors]);
            return;
        }
        
       $id =  $this->gatway->update($current, $data);
        http_response_code(200);
        echo json_encode(['message' => "FuelType  was updated successfully" ,"rowseffect =" => $id]);
    }
    // delete fuel type
    private function deleteFuelType(string $id): void
    {
      $id = $this->gatway->delete($id);
        http_response_code(200);
        echo json_encode(['message' => "FuelType was deleted successfully"]);
    }
    // getValidationErrors
    private function getValidationErrors(array $data): array
    {
        $errors = [];
        if (empty($data['FuelType'])) {
            $errors[] = 'FuelType is required';
        }
        return $errors;
    }
  }
<?php

class VehicleCategories
{

    private VehiclecategoriesGatway $gatway;

    public function __construct(VehiclecategoriesGatway $gatway)
    {
        $this->gatway = $gatway;
    }

    public function processRequest(string $method, string $id = null): void
    {
        switch ($method) {
            case 'GET':
                if ($id) {
                    $this->getById($id);
                } else {
                    $this->getAll();
                }
                break;
            case 'POST':
                $data = json_decode(file_get_contents('php://input'), true);  
                $this->addNew($data);
                break;
            case 'PUT':
                $data = json_decode(file_get_contents('php://input'), true);
                // optional: bad-JSON guard
                if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
                  http_response_code(400);
                  echo json_encode(['error' => 'Invalid JSON in request body']);
                  break;
              }
                $current = $this->gatway->getById((int)$id);

                if (!$current) {
                    http_response_code(404);
                    echo json_encode(['error' => "Vehicle category not found with ID: $id"]);
                    break;
                }

                $this->update($id);
                break;
            case 'DELETE':
                $this->delete($id);
                break;
            default:
                http_response_code(405);
                echo json_encode(['error' => 'Method not allowed']);
        }
    }
    // get all vehicle categories
    private function getAll(): void
    {
        $data = $this->gatway->getAll();
        echo json_encode($data);
    }
    // get vehicle category by ID
    private function getById(string $id): void
    {
        $data = $this->gatway->getById((int)$id);
        if ($data) {
            echo json_encode($data);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Vehicle category not found']);
        }
    }       
    // add new vehicle category
    private function addNew(array $data): void
    {
      $errors = $this->getValidationErrors($data);
        if (!empty($errors)) {
            http_response_code(422);
            echo json_encode(['error' => 'Validation errors', 'details' => $errors]);
            return;
        }

        $id = $this->gatway->addNew($data);
        if ($id === 0) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to add new vehicle category']);
            return;
        };
        http_response_code(201);
        echo json_encode(['message' => 'Vehicle category added successfully', 'id' => $id]);
    }
    // update vehicle category
    private function update(string $id): void
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (isset($data['CategoryName'])) {
            $current = $this->gatway->getById((int)$id);
            if ($current) {
                $this->gatway->update($current, $data);
                echo json_encode(['message' => 'Vehicle category updated successfully']);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Vehicle category not found']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid input']);
        }
    }
    // delete vehicle category
    private function delete(string $id): void
    {
       $rows= $this->gatway->delete($id);
        if ($rows > 0) {
            echo json_encode(['message' => 'Vehicle category deleted successfully', 'rows' => $rows]);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Vehicle  category not found ID: ' . $id]);
        }
        
       
    }
    // validate input data
    private function getValidationErrors(array $data): array
    {
        $errors = [];
        if (empty($data['CategoryName'])) {
            $errors[] = 'CategoryName is required';
        }
        return $errors;
    }
}
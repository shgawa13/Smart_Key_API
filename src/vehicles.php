<?php 

class Vehicles
{
    public function __construct(private VehiclesGatway $gatway)
    {
        
    }

  public function processRequest(string $request, string $id): void{
    switch ($request) {
          case 'GET':
                if ($id !=="") {           // ← id   → single
                    $this->getVehicleByID($id);
                } else {                     // ← no id → list
                    $this->getAllVehicles();
                }
              break;
              
          case 'POST':
              $data = (array)json_decode(file_get_contents("php://input"), true);
              $this->addNewVehicle($data);
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
                  echo json_encode(['error' => "Vehicle not found with ID: $id"]);
                  break;
              }

              $this->updateVehicle($current, $data);
              break;

          case 'DELETE':
              $this->deleteVehicle($id);
              break;

          default:
              http_response_code(405);
              echo json_encode(['error' => 'Method not allowed']);
      }
  }
  // addNewVehicle
  private function addNewVehicle(array $data): void
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
      echo json_encode(['error' => 'Failed to add new vehicle']);
      return;
    };
    
     // if new vehicle is added return the ID of the new vehicle
      http_response_code(201);
      echo json_encode(['message' => 'Vehicle added successfully', 'id' => $id]);
  }
  // getVehicle
  private function getVehicleByID(string $id): void
  {
      // Logic to get a vehicle by ID
      $vehicle = $this->gatway->getById($id);
      if ($vehicle) {
          http_response_code(200);
          echo json_encode($vehicle);
      } else {
          http_response_code(404);
          echo json_encode(['error' => 'Vehicle not found']);
      }
  }
  // getAllVehicles
  private function getAllVehicles(): void
  {
      // Logic to get all vehicles

      
      $vehicles = $this->gatway->getAll();
      if ($vehicles !== null) {
          http_response_code(200);
          echo json_encode($vehicles);
      } else {
          http_response_code(404);
          echo json_encode(['error' => 'No vehicles found']);
      }
  }
  // updateVehicle
  private function updateVehicle(array $current, array $data): void
  {
      // Logic to update a vehicle
      $errors = $this->getValidationErrors($data);
      if (!empty($errors)) {
          http_response_code(422);
          echo json_encode(['error' => 'Validation errors', 'details' => $errors]);
          return;
      }

      if (!$current) {                      
        http_response_code(404);
        echo json_encode(['error' => "Customer not found with ID: {$current['CustomerID']}"]);
        return;
    }
    
    $rows = $this->gatway->updateVehicle($current, $data);

    http_response_code(200);
    echo json_encode(['message' => 'Customer updated successfully' . $rows . ' rows updated']);
    
  }
  // deleteVehicle
  private function deleteVehicle(string $id): void
  {
      // Logic to delete a vehicle
      if ($this->gatway->deleteVehicle($id)) {
          http_response_code(200);
          echo json_encode(['message' => 'Vehicle deleted successfully']);
      } else {
          http_response_code(500);
          echo json_encode(['error' => 'Failed to delete vehicle']);
      }
  }

  // getValidationErrors
  private function getValidationErrors(array $data): array
  {
    $errors = [];
      // list the fields that are mandatory
      $required = [
      'Make',
      'Model',
      'Year',
      'Mileage',
      'FuelTypeID',
      'PlateNumber',
      'CarCategoryID',
      'RentalPricePerDay',
      'IsAvailableForRent',
      ];


      foreach ($required as $field) {
        if (empty($data[$field])) {
            $errors[$field] = "$field is required";
        }

      
    }
    return $errors;
  }   
   

}
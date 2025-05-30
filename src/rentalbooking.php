<?php 

class RentalBooking{
  public function __construct(private RentalbookingGatway $gatway)
    {
        
    }

    public function processRequest(string $request, ?string $id): void
    {
        switch ($request) {
            case 'GET':
                if ($id !=="") {          
                     $this->getRentalBooking($id);
                } else {                     
                    $this->getAllRentalBookings();
                }
                break; 

            case 'POST':
                $data = (array)json_decode(file_get_contents("php://input"), true);
                $this->addNewRentalBooking($data);
                break;

            case 'PUT':
                $data = json_decode(file_get_contents('php://input'), true);
                // optional: bad-JSON guard
                if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
                    http_response_code(400);
                    echo json_encode(['error' => 'Invalid JSON in request body']);
                    break;
                }

                $current = $this->gatway->getRentalBookingById($id);
             
                if (!$current) {
                    http_response_code(404);
                    echo json_encode(['error' => "Rental booking not found with ID: $id"]);
                    break;
                }

                $this->updateRentalBooking($current, $data);
                break;

            case 'DELETE':
                $this->deleteRentalBooking($id);
                break;

            default:
                http_response_code(405);
                echo json_encode(['error' => 'Method not allowed']);
        }

    }
    // addNewRentalBooking
    private function addNewRentalBooking(array $data): void
    {
        $errors = $this->getValidationErrors($data);
        if (!empty($errors)) {
            http_response_code(422);
            echo json_encode(['error' => 'Validation errors', 'details' => $errors]);
            return;
        }
        
       $id = $this->gatway->addRentalBooking($data);
       if ($id === 0) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to add new Booking']);
        return;
    };

    http_response_code(201);
    echo json_encode(['message' => 'Booking added successfully', 'id' => $id]);

    }

    // getRentalBooking
    private function getRentalBooking(string $id): void
    {
        $data = $this->gatway->getRentalBookingById($id);
        if ($data) {
            http_response_code(200);
            echo json_encode($data);
        } else {
            http_response_code(404);
            echo json_encode(['error' => "Rental booking not found with ID: $id"]);
        }
    }

    // getAllRentalBookings
    private function getAllRentalBookings(): void
    {
        $data = $this->gatway->getAllRentalBookings();
        if ($data) {
            http_response_code(200);
            echo json_encode($data);
        } else {
            http_response_code(404);
            echo json_encode(['error' => "No rental bookings found"]);
        }
    }
    // updateRentalBooking
    private function updateRentalBooking(array $current, array $new): void
    {
        $errors = $this->getValidationErrors($new);
        if (!empty($errors)) {
            http_response_code(422);
            echo json_encode(['error' => 'Validation errors', 'details' => $errors]);
            return;
        }
        
        $rows = $this->gatway->updateRentalBooking($current, $new);
        if ($rows > 0) {
            http_response_code(200);
            echo json_encode(['message' => 'Rental booking updated successfully', 'rows' => $rows]);
        } else {
            http_response_code(404);
            echo json_encode(['error' => "Rental booking not found with ID: {$current['BookingID']}"]);
        }
    }

    // deleteRentalBooking
    private function deleteRentalBooking(string $id): void
    {
        $isDeleted = $this->gatway->deleteRentalBooking($id);
        if (!$isDeleted) {
            http_response_code(404);
            echo json_encode(['error' => "Rental booking not found with ID: $id"]);
            return;
        }

        http_response_code(204);
        echo json_encode(['message' => "Rental booking with ID: $id deleted successfully"]);
    }
    // getValidationErrors
    private function getValidationErrors(array $data): array
    {
      
        $errors = [];
        $requiredFields =  [
            'CustomerID',
            'VehicleID',
            'RentalStartDate', 
            'RentalEndDate', 
            'PickupLocation',
            'InitialRentalDays', 
            'RentalPricePerDay', 
            'InitialTotalDueAmount'
        ];


        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $errors[] = "$field is required";
            }
        }
        return $errors;
      }
                           
}
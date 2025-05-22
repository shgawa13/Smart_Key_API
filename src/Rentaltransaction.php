<?php

class Rentaltransaction
{
//   RentalTransaction (
//     BookingID, UserID, PaymentDetails, PaidTotalDueAmount,
//     ActualTotalDueAmount, TotalRemaining, TotalReturnGetAmount,
//     TransactionDate, UpdatedTransactionDate
// )

public function __construct(private RentaltransactionGatway $gatway){}

    public function processRequest(string $request, ?string $id): void
    {
        switch ($request) {
            case 'GET':
                if ($id !=="") {           // ← id   → single
                     $this->getRentalTransaction($id);
                } else {                     // ← no id → list
                    $this->getAllRentalTransactions();
                }
                break; 

            case 'POST':
                $data = (array)json_decode(file_get_contents("php://input"), true);
                $this->addNewRentalTransaction($data);
                break;

            case 'PUT':
                $data = json_decode(file_get_contents('php://input'), true);
                // optional: bad-JSON guard
                if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
                    http_response_code(400);
                    echo json_encode(['error' => 'Invalid JSON in request body']);
                    break;
                }

                $current = $this->gatway->getRentalTransactionById($id);

                if (!$current) {
                    http_response_code(404);
                    echo json_encode(['error' => "Rental transaction not found with ID: $id"]);
                    break;
                }

                $this->updateRentalTransaction($current, $data);
                break;

            case 'DELETE':
                $this->deleteRentalTransaction($id);
                break;

            default:
                http_response_code(405);
                echo json_encode(['error' => 'Method not allowed']);
        }

    }
    // addNewRentalTransaction
    private function addNewRentalTransaction(array $data): void
    {
        $errors = $this->getValidationErrors($data);
        if (!empty($errors)) {
            http_response_code(422);
            echo json_encode(['error' => 'Validation errors', 'details' => $errors]);
            return;
        }
        
        $this->gatway->addRentalTransaction($data);
    }
    // getRentalTransaction
    private function getRentalTransaction(string $id): void
    {
        $data = $this->gatway->getRentalTransactionById($id);
        if ($data) {
            http_response_code(200);
            echo json_encode($data);
        } else {
            http_response_code(404);
            echo json_encode(['error' => "Rental transaction not found with ID: $id"]);
        }
    }
    // getAllRentalTransactions
    private function getAllRentalTransactions(): void
    {
        $data = $this->gatway->getAllRentalTransactions();
        if ($data) {
            http_response_code(200);
            echo json_encode($data);
        } else {
            http_response_code(404);
            echo json_encode(['error' => "No rental transactions found"]);
        }
    }

    // updateRentalTransaction
    private function updateRentalTransaction(array $current, array $new): void
    {
        $errors = $this->getValidationErrors($new);
        if (!empty($errors)) {
            http_response_code(422);
            echo json_encode(['error' => 'Validation errors', 'details' => $errors]);
            return;
        }
        
        $this->gatway->UpdateRentalTransaction($current, $new);
    }

    // deleteRentalTransaction
    private function deleteRentalTransaction(string $id): void
    {
        $this->gatway->deleteRentalTransaction($id);
        http_response_code(204);
    }
    // getValidationErrors
    private function getValidationErrors(array $data): array
    {
        $errors = [];
        
        $requiredFields = [
            'BookingID',
            'UserID',
            'PaymentDetails',
            'PaidTotalDueAmount',
            'ActualTotalDueAmount',
            'TotalRemaining',
            'TotalReturnGetAmount',
            'TransactionDate',
            'UpdatedTransactionDate'
        ];

        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $errors[$field] = "$field is required";
            }
        }
        
        return $errors;
    }
}

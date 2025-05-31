<?php

class RentaltransactionGatway
{
  private PDO $conn;

    public function __construct(Database $database)
    {
        $this->conn = $database->getConnection();
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
  // add new rental transaction 
  public function AddRentalTransaction(array $data): int
  {
      $sql = "INSERT INTO RentalTransaction (BookingID, UserID, PaymentDetails, PaidTotalDueAmount,
                                      ActualTotalDueAmount, TotalRemaining, TotalReturnGetAmount,
                                      TransactionDate, UpdatedTransactionDate) 
              VALUES (:BookingID, :UserID, :PaymentDetails, :PaidTotalDueAmount,
                      :ActualTotalDueAmount, :TotalRemaining, :TotalReturnGetAmount,
                      :TransactionDate, :UpdatedTransactionDate)";

      $transactionId = 0;
      try {
          $stmt = $this->conn->prepare($sql);
          $stmt->bindParam(':BookingID', $data['BookingID']);
          $stmt->bindParam(':UserID', $data['UserID']);
          $stmt->bindParam(':PaymentDetails', $data['PaymentDetails']);
          $stmt->bindParam(':PaidTotalDueAmount', $data['PaidTotalDueAmount']);
          $stmt->bindParam(':ActualTotalDueAmount', $data['ActualTotalDueAmount']);
          $stmt->bindParam(':TotalRemaining', $data['TotalRemaining']);
          $stmt->bindParam(':TotalReturnGetAmount', $data['TotalReturnGetAmount']);
          $stmt->bindParam(':TransactionDate', $data['TransactionDate']);
          $stmt->bindParam(':UpdatedTransactionDate', $data['UpdatedTransactionDate']);

          if ($stmt->execute()) {
              // here we return the last inserted ID
              $transactionId = (int)$this->conn->lastInsertId();
          }
          
      } catch (PDOException $e) {
          echo "Error: " . $e->getMessage();
          
      }
      finally
      {
          return $transactionId;
      }
      
  }

  // get all rental transactions
  public function GetAllRentalTransactions(): array
  {
      $data = [];
      try {
          $query = "SELECT * FROM RentalTransaction ORDER BY TransactionID ASC";
          $stmt = $this->conn->prepare($query);
          $stmt->execute();
          
      } catch (PDOException $e) {
          echo "Error: " . $e->getMessage();
          
      }
      finally
      {
          return $stmt->fetchAll(PDO::FETCH_ASSOC);
      }

  } 
  // get rental transaction by ID
  public function GetRentalTransactionById(int $TransactionID): array | bool
  {
      $IsFound = false;
      try {
          $query = "SELECT * FROM RentalTransaction WHERE TransactionID = :TransactionID";
          $stmt = $this->conn->prepare($query);
          $stmt->bindParam(':TransactionID', $TransactionID);
          $stmt->execute();

          if ($stmt->rowCount() > 0) {
              $IsFound = true;
          } else {
              $IsFound = false;
          }

      } catch (PDOException $e) {
          echo "Error: " . $e->getMessage();
      }
      finally
      {
          return $stmt->fetch(PDO::FETCH_ASSOC) ?: $IsFound;
      }
      
  }
  // update rental transaction
  public function UpdateRentalTransaction(array $data, array $current): int
 {
    $sql = "UPDATE RentalTransaction SET 
              BookingID = :BookingID, 
              UserID = :UserID, 
              PaymentDetails = :PaymentDetails,
              PaidTotalDueAmount = :PaidTotalDueAmount, 
              ActualTotalDueAmount = :ActualTotalDueAmount,
              TotalRemaining = :TotalRemaining, 
              TotalReturnGetAmount = :TotalReturnGetAmount,
              TransactionDate = :TransactionDate, 
              UpdatedTransactionDate = :UpdatedTransactionDate
            WHERE TransactionID = :TransactionID";

    $Effectedrows = 0;

    try {
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':BookingID', $data['BookingID'] ?? $current['BookingID'], PDO::PARAM_INT);
        $stmt->bindValue(':UserID', $data['UserID'] ?? $current['UserID'], PDO::PARAM_INT);
        $stmt->bindValue(':PaymentDetails', $data['PaymentDetails'] ?? $current['PaymentDetails'], PDO::PARAM_STR);
        $stmt->bindValue(':PaidTotalDueAmount', (string)$data['PaidTotalDueAmount'] ?? $current['PaidTotalDueAmount'], PDO::PARAM_STR);
        $stmt->bindValue(':ActualTotalDueAmount', (string)$data['ActualTotalDueAmount'] ?? $current['ActualTotalDueAmount'], PDO::PARAM_STR);
        $stmt->bindValue(':TotalRemaining', (string)$data['TotalRemaining'] ?? $current['TotalRemaining'], PDO::PARAM_STR);
        $stmt->bindValue(':TotalReturnGetAmount', (string)$data['TotalReturnGetAmount'] ?? $current['TotalReturnGetAmount'], PDO::PARAM_STR);
        $stmt->bindValue(':TransactionDate', $data['TransactionDate'] ?? $current['TransactionDate'], PDO::PARAM_STR);
        $stmt->bindValue(':UpdatedTransactionDate', $data['UpdatedTransactionDate'] ?? $current['UpdatedTransactionDate'], PDO::PARAM_STR);
        $stmt->bindValue(':TransactionID', $data['TransactionID'] ?? $current['TransactionID'], PDO::PARAM_INT);

        $stmt->execute();
        $Effectedrows = $stmt->rowCount();

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    } finally {
        return $Effectedrows;
    }
}

  // delete rental transaction
  public function deleteRentalTransaction(int $id): int
  {
      $sql = "DELETE FROM RentalTransaction WHERE TransactionID = :transactionId";
      $Effectedrows = 0;
      try {
          $stmt = $this->conn->prepare($sql);
          $stmt->bindParam(':transactionId', $id);
          $stmt->execute();
          $Effectedrows = $stmt->rowCount();
          
      } catch (PDOException $e) {
          echo "Error: " . $e->getMessage();
          
      }
      finally
      {
          return $Effectedrows;
      }
      
  }
}

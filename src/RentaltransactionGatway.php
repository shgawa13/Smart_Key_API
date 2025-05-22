<?php

class RentaltransactionGatway
{
  private PDO $conn;

    public function __construct(Database $database)
    {
        $this->conn = $database->getConnection();
    }
  // add new rental transaction 
  public function AddRentalTransaction(array $data): int
  {
      $sql = "INSERT INTO RentalTransaction (BookingID, UserID, PaymentDetails, PaidTotalDueAmount,
                                      ActualTotalDueAmount, TotalRemaining, TotalReturnGetAmount,
                                      TransactionDate, UpdatedTransactionDate) 
              VALUES (:bookingId, :userId, :paymentDetails, :paidTotalDueAmount,
                      :actualTotalDueAmount, :totalRemaining, :totalReturnGetAmount,
                      :transactionDate, :updatedTransactionDate)";

      $transactionId = 0;
      try {
          $stmt = $this->conn->prepare($sql);
          $stmt->bindParam(':bookingId', $data['BookingID']);
          $stmt->bindParam(':userId', $data['UserID']);
          $stmt->bindParam(':paymentDetails', $data['PaymentDetails']);
          $stmt->bindParam(':paidTotalDueAmount', $data['PaidTotalDueAmount']);
          $stmt->bindParam(':actualTotalDueAmount', $data['ActualTotalDueAmount']);
          $stmt->bindParam(':totalRemaining', $data['TotalRemaining']);
          $stmt->bindParam(':totalReturnGetAmount', $data['TotalReturnGetAmount']);
          $stmt->bindParam(':transactionDate', $data['TransactionDate']);
          $stmt->bindParam(':updatedTransactionDate', $data['UpdatedTransactionDate']);

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
  public function GetRentalTransactionById(int $id): array | bool
  {
      $IsFound = false;
      try {
          $query = "SELECT * FROM RentalTransaction WHERE TransactionID = :id";
          $stmt = $this->conn->prepare($query);
          $stmt->bindParam(':id', $id);
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
  public function UpdateRentalTransaction(array $data,array $current): int
  {
      $sql = "UPDATE RentalTransaction SET BookingID = :bookingId, UserID = :userId, PaymentDetails = :paymentDetails,
              PaidTotalDueAmount = :paidTotalDueAmount, ActualTotalDueAmount = :actualTotalDueAmount,
              TotalRemaining = :totalRemaining, TotalReturnGetAmount = :totalReturnGetAmount,
              TransactionDate = :transactionDate, UpdatedTransactionDate = :updatedTransactionDate
              WHERE TransactionID = :transactionId";

      $Effectedrows = 0;
      try {
          $stmt = $this->conn->prepare($sql);
          $stmt->bindValue(':bookingId', $data['BookingID'] ?? $current['BookingID'] ,PDO::PARAM_INT);
          $stmt->bindValue(':userId', $data['UserID'] ?? $current['UserID'],PDO::PARAM_INT);
          $stmt->bindValue(':paymentDetails', $data['PaymentDetails'] ?? $current['PaymentDetails']);
          $stmt->bindValue(':paidTotalDueAmount', $data['PaidTotalDueAmount'] ?? $current['PaidTotalDueAmount']);
          $stmt->bindValue(':actualTotalDueAmount', $data['ActualTotalDueAmount'] ?? $current['ActualTotalDueAmount']);
          $stmt->bindValue(':totalRemaining', $data['TotalRemaining'] ?? $current['TotalRemaining']);
          $stmt->bindValue(':totalReturnGetAmount', $data['TotalReturnGetAmount'] ?? $current['TotalReturnGetAmount']);
          $stmt->bindValue(':transactionDate', $data['TransactionDate'] ?? $current['TransactionDate']);
          $stmt->bindValue(':updatedTransactionDate', $data['UpdatedTransactionDate'] ?? $current['UpdatedTransactionDate']);
          $stmt->bindValue(':transactionId', $current['TransactionID'] ?? $data['TransactionID']);
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

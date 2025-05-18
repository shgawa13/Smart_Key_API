<?php

class CustomersGatway
{
  private PDO $conn;

  public function __construct(Database $database)
  {
    $this->conn = $database->getConnection();
  }

  public function getAll(): array
  {
    $query = "SELECT * FROM customer ORDER BY CustomerID ASC";
    $stmt = $this->conn->prepare($query);
    $stmt->execute();
    
    $data = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $row['Gender'] = (bool)$row['Gender'];
      $data[] = $row;
    }
    return $data;
  }
  
  public function getById(int $id): array | bool
  {
    $query = "SELECT * FROM customer WHERE CustomerID = :id";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: false;
  }

  public function addNew(array $data): int
  {
     $NewId = 0;

    try{
      $query = "INSERT INTO customer(FirstName, SecondName,LastName,DateOfBirth,
      Gender,PhoneNumber,DriverLicenseNumber)
      VALUES (:FirstName, :SecondName, :LastName, :DateOfBirth,
      :Gender, :PhoneNumber, :DriverLicenseNumber)";
      // Prepare the statement
      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(':FirstName', $data['FirstName']);
      $stmt->bindParam(':SecondName', $data['SecondName']);
      $stmt->bindParam(':LastName', $data['LastName']);
      $stmt->bindParam(':DateOfBirth', $data['DateOfBirth']);
      $stmt->bindValue(':Gender',
      isset($data['Gender']) ? (int)$data['Gender'] : 0,
      PDO::PARAM_INT); 
      $stmt->bindParam(':PhoneNumber', $data['PhoneNumber']);
      $stmt->bindParam(':DriverLicenseNumber', $data['DriverLicenseNumber']);
      $stmt->execute();
      // here we return the last inserted ID
      $NewId = (int)$this->conn->lastInsertId();

      }
      catch (PDOException $e) {
        // Handle the exception
        http_response_code(500);
        // Log the error message  
       json_decode("Error: " . $e->getMessage());
        $NewId = 0;
      }
      finally
      {
        return $NewId;
      }
      
  }
  

}
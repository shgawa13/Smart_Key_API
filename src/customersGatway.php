<?php

class CustomersGatway
{
  private PDO $conn;

  public function __construct(Database $database)
  {
    $this->conn = $database->getConnection();
  }
  /**
   * Add a new customer to the database
   *
   * @param array $data The customer data
   * @return int The ID of the newly added customer
   */
  
   // Add a new customer to the database
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

  /**
   * Get all customers from the database
   *
   * @return array An array of customer data
   */
  public function getAll(): array
  {
    $data = [];
    try{
      $query = "SELECT * FROM customer ORDER BY CustomerID ASC";
      $stmt = $this->conn->prepare($query);
      $stmt->execute();
      
      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $row['Gender'] = (bool)$row['Gender'];
        $data[] = $row;
      }

    }catch (PDOException $e) {
      // Handle the exception
      http_response_code(500);
      // Log the error message  
      json_decode("Error: " . $e->getMessage());
    }
    return $data;
  }
  /**
   * Get a customer by ID
   *
   * @param int $id The ID of the customer
   * @return array|bool The customer data or false if not found
   */
  // Get a customer by ID
  public function getById(int $id): array | bool
  {
    $isFound = false;
    try{
      $query = "SELECT * FROM customer WHERE CustomerID = :id";
      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(':id', $id, PDO::PARAM_INT);
      $stmt->execute();
      if ($stmt->rowCount() > 0) {
        $isFound = true;
      } else {
        $isFound = false;
      }
    }
    catch (PDOException $e) {
      // Handle the exception
      http_response_code(500);
      // Log the error message  
      json_decode("Error: " . $e->getMessage());
      $isFound = false;
    }
    finally{
      return $stmt->fetch(PDO::FETCH_ASSOC) ?: $isFound;
    }
    
  }
  
  /**
   * Update a customer by ID
   *
   * @param array $current The current customer data
   * @param array $new The new customer data
   * @return int The number of affected rows
   */
  // Update a customer by ID
  public function update(array $current, array $new): int
  {
    $rowEffected = 0;
    try{
      $query = "UPDATE customer SET FirstName = :FirstName, SecondName = :SecondName,
      LastName = :LastName, DateOfBirth = :DateOfBirth, Gender = :Gender,
      PhoneNumber = :PhoneNumber, DriverLicenseNumber = :DriverLicenseNumber
      WHERE CustomerID = :id";

      $stmt = $this->conn->prepare($query);
      // Check if the new value is set, otherwise use the current value
      $stmt->bindValue(':FirstName', $new['FirstName'] ?? $current['FirstName'],
      PDO::PARAM_STR);
      $stmt->bindValue(':SecondName', $new['SecondName']?? $current['SecondName'],
      PDO::PARAM_STR);
      $stmt->bindValue(':LastName', $new['LastName'] ?? $current['LastName'],
      PDO::PARAM_STR);
      $stmt->bindValue(':DateOfBirth', $new['DateOfBirth']?? $current['DateOfBirth'],
      PDO::PARAM_STR);
      $stmt->bindValue(':Gender', $new['Gender'] ?? $current['Gender'],
      PDO::PARAM_BOOL);
      $stmt->bindValue(':PhoneNumber', $new['PhoneNumber'] ?? $current['PhoneNumber'],
      PDO::PARAM_STR);
      $stmt->bindValue(':DriverLicenseNumber', $new['DriverLicenseNumber']?? $current['DriverLicenseNumber'],
      PDO::PARAM_STR);

      $stmt->bindValue(":id", $current["CustomerID"], PDO::PARAM_INT);
      $stmt->execute();

      $rowEffected = $stmt->rowCount();
      
  
     }
     catch (PDOException $e) {
      // Handle the exception
      http_response_code(500);
      // Log the error message  
      json_decode("Error: " . $e->getMessage());
      var_dump($e->getMessage());
      return 0;
    }
    finally{
      return $rowEffected;
    }
  }

  /**
   * Delete a customer by ID
   *
   * @param int $id The ID of the customer
   * @return bool True on success, false on failure
   */
  public function delete(int $id): bool
  {
    $rowEffected = 0;
    try{
      $query = "DELETE FROM customer WHERE CustomerID = :id";
      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(':id', $id, PDO::PARAM_INT);
      $stmt->execute();

      $rowEffected = $stmt->rowCount();
      if ($rowEffected > 0) {
        $rowEffected = true;
      } else {
        $rowEffected = false;
      }
    }
    catch (PDOException $e) {
      // Handle the exception
      http_response_code(500);
      // Log the error message  
      json_decode("Error: " . $e->getMessage());
      return false;
    }
    finally{
      return $rowEffected;
    }
  }

  /**
   * Get a customer by phone number
   *
   * @param string $phoneNumber The phone number of the customer
   * @return array|bool The customer data or false if not found
   */
  public function GetCustomerByPhoneNumber(string $phoneNumber): array | bool
  {
    $isFound = false;
    try{
      $query = "SELECT * FROM customer WHERE PhoneNumber = :phoneNumber";
      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(':phoneNumber', $phoneNumber, PDO::PARAM_STR);
      $stmt->execute();
      if ($stmt->rowCount() > 0) {
        $isFound = true;
      } else {
        $isFound = false;
      }
    }
    catch (PDOException $e) {
      // Handle the exception
      http_response_code(500);
      // Log the error message  
      json_decode("Error: " . $e->getMessage());
      $isFound = false;
    }
    finally{
      return $stmt->fetch(PDO::FETCH_ASSOC) ?: $isFound;
    }
  }
    
  /**
   * Get a customer by driver license number
   *
   * @param string $driverLicenseNumber The driver license number of the customer
   * @return array|bool The customer data or false if not found
   */
  public function GetCustomerByDriverLicenseNumber(string $driverLicenseNumber): array | bool
  {
    $isFound = false;
    try{
      $query = "SELECT * FROM customer WHERE DriverLicenseNumber = :driverLicenseNumber";
      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(':driverLicenseNumber', $driverLicenseNumber, PDO::PARAM_STR);
      $stmt->execute();
      if ($stmt->rowCount() > 0) {
        $isFound = true;
      } else {
        $isFound = false;
      }
    }
    catch (PDOException $e) {
      // Handle the exception
      http_response_code(500);
      // Log the error message  
      json_decode("Error: " . $e->getMessage());
      $isFound = false;
    }
    finally{
      return $stmt->fetch(PDO::FETCH_ASSOC) ?: $isFound;
    }
  }
  
}
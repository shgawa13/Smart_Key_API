<?php

class VehiclesGatway
{
  private PDO $conn;

  public function __construct(Database $database)
  {
    $this->conn = $database->getConnection();
  }

  public function addNew(array $data): int
  {
    $NewId = 0;

    try{
      $query = "INSERT INTO Vehicle(Make, Model, Year, Mileage, FuelTypeID, 
      PlateNumber, CarCategoryID, RentalPricePerDay, IsAvailableForRent,CarImage)
      VALUES (:Make, :Model, :Year, :Mileage, :FuelTypeID,
      :PlateNumber, :CarCategoryID, :RentalPricePerDay, :IsAvailableForRent,:CarImage)";
      // Prepare the statement
      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(':Make', $data['Make'], PDO::PARAM_STR);
      $stmt->bindParam(':Model', $data['Model'], PDO::PARAM_STR);
      $stmt->bindParam(':Year', $data['Year'], PDO::PARAM_INT);
      $stmt->bindParam(':Mileage', $data['Mileage'], PDO::PARAM_INT);
      $stmt->bindParam(':FuelTypeID', $data['FuelTypeID'], PDO::PARAM_INT);
      $stmt->bindParam(':PlateNumber', $data['PlateNumber'], PDO::PARAM_STR);
      $stmt->bindParam(':CarCategoryID', $data['CarCategoryID'], PDO::PARAM_INT);
      $stmt->bindParam(':RentalPricePerDay', $data['RentalPricePerDay'], PDO::PARAM_INT);
      $stmt->bindValue(':IsAvailableForRent',
      isset($data['IsAvailableForRent']) ? (int)filter_var($data['IsAvailableForRent'], FILTER_VALIDATE_BOOLEAN) : 0,
      PDO::PARAM_INT);
      $stmt->bindParam(':CarImage', $data['CarImage'], PDO::PARAM_STR);
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

  public function getById(int $id): array | bool
  {
    try{
      $query = "SELECT * FROM Vehicle WHERE VehicleID = :id";
      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(':id', $id, PDO::PARAM_INT);
      $stmt->execute();
      return $stmt->fetch(PDO::FETCH_ASSOC);
    }catch (PDOException $e) {
      // Handle the exception
      http_response_code(500);
      // Log the error message  
     json_decode("Error: " . $e->getMessage());
     return false;
    }
  }

  public function getAll(): array
  {
    $data = [];
    try{
      $query = "SELECT * FROM Vehicle ORDER BY VehicleID ASC";
      $stmt = $this->conn->prepare($query);
      $stmt->execute();

      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $row['IsAvailableForRent'] = (bool)$row['IsAvailableForRent'];
        $data[] = $row;
      }
    }
    catch (PDOException $e) {
      // Handle the exception
      http_response_code(500);
      // Log the error message  
      json_decode("Error: " . $e->getMessage());
    }

    return $data;
  }
  
  public function updateVehicle(array $current, array $data): int
  {
    $query = "UPDATE Vehicle SET Make = :Make, Model = :Model, Year = :Year, Mileage = :Mileage,
    FuelTypeID = :FuelTypeID, PlateNumber = :PlateNumber, CarCategoryID = :CarCategoryID,
    RentalPricePerDay = :RentalPricePerDay, IsAvailableForRent = :IsAvailableForRent, CarImage = :CarImage
    WHERE VehicleID = :id";

      $rowsEffected = 0;

    try{
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':Make', $data['Make'] ?? $current['Make'],
        PDO::PARAM_STR);
        $stmt->bindValue(':Model', $data['Model'] ?? $current['Model'],
        PDO::PARAM_STR);
        $stmt->bindValue(':Year', $data['Year'] ?? $current['Year'],
        PDO::PARAM_INT);
        $stmt->bindValue(':Mileage', $data['Mileage'] ?? $current['Mileage'],
        PDO::PARAM_INT);
        $stmt->bindValue(':FuelTypeID', $data['FuelTypeID'] ?? $current['FuelTypeID'],
        PDO::PARAM_INT);
        $stmt->bindValue(':PlateNumber', $data['PlateNumber'] ?? $current['PlateNumber'],
        PDO::PARAM_STR);
        $stmt->bindValue(':CarCategoryID', $data['CarCategoryID'] ?? $current['CarCategoryID'],
        PDO::PARAM_INT);
        $stmt->bindValue(':RentalPricePerDay', $data['RentalPricePerDay'] ?? $current['RentalPricePerDay'], 
          PDO::PARAM_INT);
          $IsAvailableForRent = isset($new['IsAvailableForRent']) ? filter_var($new['IsAvailableForRent'],
           FILTER_VALIDATE_BOOLEAN) : (bool)$current['Gender'];
      $stmt->bindValue(':Gender', $IsAvailableForRent, PDO::PARAM_INT);
        $stmt->bindValue(':CarImage', $data['CarImage'] ?? $current['CarImage'],
        PDO::PARAM_STR);
        $stmt->bindValue(':id', $current['VehicleID'], PDO::PARAM_INT);
        $rowsEffected= $stmt->execute();

    }catch(PDOException $e) {
      // Handle the exception
      http_response_code(500);
      // Log the error message  
     json_decode("Error: " . $e->getMessage());
    }
    finally{
      return $rowsEffected;
    }

  } 

  public function deleteVehicle(string $id): bool
  { 
    $isDeleted = false;
     try{
      
      $query = "DELETE FROM Vehicle WHERE VehicleID = :id";
      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(':id', $id, PDO::PARAM_INT);
      $isDeleted = $stmt->execute();

     }
     catch (PDOException $e) 
     {
      // Handle the exception
      http_response_code(500);
      // Log the error message  
     json_decode("Error: " . $e->getMessage());
     $isDeleted = false;

     } 
     finally{
      return $isDeleted;
     }
       
  }
  
}
<?php

class VehiclecategoriesGatway
{

  private PDO $conn;

  public function __construct(Database $database)
  {
    $this->conn = $database->getConnection();
  }

  // add new vehicle category
  public function addNew(array $data): int
  {
    $sql = "INSERT INTO VehicleCategories (CategoryName) VALUES (:CategoryName)";
    $ID = 0;
    try {
      $stmt = $this->conn->prepare($sql);
      $stmt->bindValue(':CategoryName', $data['CategoryName']);
      $stmt->execute();
      // here we return the last inserted ID
      $ID = (int)$this->conn->lastInsertId();
    } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
    } finally {
      return $ID;
    }
  }

  // get all vehicle categories
  public function getAll(): array
  {
    $data = [];
    try {
      $query = "SELECT * FROM VehicleCategories ORDER BY CategoryID ASC";
      $stmt = $this->conn->prepare($query);
      $stmt->execute();
      $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
    } finally {
      return $data;
    }
  }

  // get vehicle category by ID
  public function getById(int $id): array | bool
  {
    $IsFound = false;
    try {
      $query = "SELECT * FROM VehicleCategories WHERE CategoryID = :id";
      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(':id', $id, PDO::PARAM_INT);
      $stmt->execute();

      if ($stmt->rowCount() > 0) {
        $IsFound = true;
      }

    } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
    }
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: $IsFound;
  }

  // update vehicle category
  public function update(array $current, array $data): int
  {
    $sql = "UPDATE VehicleCategories SET CategoryName = :CategoryName WHERE CategoryID = :CategoryID";
    $Effectedrows = 0;
    try {
      $stmt = $this->conn->prepare($sql);
      $stmt->bindValue(':CategoryName', $data['CategoryName'] ?? $current['CategoryName'], PDO::PARAM_STR);
      $stmt->bindValue(':CategoryID', $current['CategoryID']?? $current['CategoryID'], PDO::PARAM_INT);
      $stmt->execute();
      // here we return the last inserted ID
      $Effectedrows = (int)$stmt->rowCount();
    } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
    } finally {
      return $Effectedrows;
    }
  }

  // delete vehicle category
  public function delete(int $id): int
  {
    $sql = "DELETE FROM VehicleCategories WHERE CategoryID = :CategoryID";
    $Effectedrows = 0;
    try {
      $stmt = $this->conn->prepare($sql);
      $stmt->bindValue(':CategoryID', $id);
      $stmt->execute();
      // here we return the last inserted ID
      $Effectedrows = (int)$stmt->rowCount();
    } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
    } finally {
      return $Effectedrows;
    }
  }
  // get validation errors

}
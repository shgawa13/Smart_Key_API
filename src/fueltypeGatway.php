<?php

class FuelTypeGatway
{
    private PDO $conn;

    public function __construct(Database $database)
    {
        $this->conn = $database->getConnection();
    }
    
    // add new fuel type
    public function addNew(array $data): int
    {
        $sql = "INSERT INTO FuelTypes (FuelType) VALUES (:FuelType)";
        $ID = 0;
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':FuelType', $data['FuelType']);
            $stmt->execute();
            // here we return the last inserted ID
            $ID = (int)$this->conn->lastInsertId();
          
            
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            
        }
        finally
        {
            return $ID;
        }
        
    }   
    // get all fuel types
    public function getAll(): array
    {
        $data = [];
        try {
            $query = "SELECT * FROM FuelTypes ORDER BY ID ASC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        finally
        {
            return $data;
        }
        
    }
    // get fuel type by ID
    public function getById(int $id): array | bool
    {
      $IsFound = false;
        try{
            $query = "SELECT * FROM FuelTypes WHERE ID = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $IsFound = true;
            } else {
                $IsFound = false;
            }

        }catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        finally
        {
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: $IsFound;
        }
        
    }


    // update fuel type
    public function update(array $current, array $data): int
    {
        $sql = "UPDATE FuelTypes SET FuelType = :FuelType WHERE ID = :id";

        $EffectedRows = 0;
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':FuelType', $data['FuelType']);
            $stmt->bindValue(':id', $current['ID']);
            $stmt->execute();
            $EffectedRows = (int)$stmt->rowCount();
            
           
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        finally
        {
            return $EffectedRows;
        }
        
    }
    // delete fuel type
    public function delete(string $id): bool
    {
        $sql = "DELETE FROM FuelTypes WHERE ID = :id";

        $IsDeleted = false;
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $IsDeleted = true;
            } else {
                $IsDeleted = false;
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        finally
        {
            return $IsDeleted;
        }
    }
  }
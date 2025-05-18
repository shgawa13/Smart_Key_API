<?php

class UsersGatway
{
  private PDO $conn;

  public function __construct(Database $database)
  {
    $this->conn = $database->getConnection();
  }

  
    public function addNew(array $data): int
    {
        $newId = 0;

        try {
            $query = "INSERT INTO users (UserName, Password) VALUES (:UserName, :Password)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':UserName', $data['UserName'],PDO::PARAM_STR);
            $stmt->bindParam(':password', $data['password'], PDO::PARAM_STR);
            $stmt->execute();

            // Return the last inserted ID
            $newId = (int)$this->conn->lastInsertId();
        } catch (PDOException $e) {
           
           http_response_code(500);
           // Log the error message  
           json_decode("Error: " . $e->getMessage());
        }

        return $newId;
    }
      
    public function getById(int $id): array | bool
    {
        $query = "SELECT * FROM users WHERE UserID = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: false;
    }

    public function getAll(): array
    {
        $query = "SELECT * FROM users ORDER BY UserID ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
  }
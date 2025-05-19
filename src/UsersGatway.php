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
            $stmt->bindParam(':UserName', $data['UserName'], PDO::PARAM_STR);
            $stmt->bindParam(':Password', $data['Password'], PDO::PARAM_STR);
            $stmt->execute();

            // Return the last inserted ID
            $newId = (int)$this->conn->lastInsertId();
        } catch (PDOException $e) {
           
           http_response_code(500);
          // Log the error message  
          json_decode("Error: " . $e->getMessage());
          $newId = 0;
        }

        return $newId;
    }
      
    public function updateUser(int $id, array $data): bool
    {
      $query = "UPDATE users SET UserName = :UserName, Password = :Password WHERE UserID = :id";
      $stmt = $this->conn->prepare($query);
      $stmt->bindValue(':UserName', $data['UserName'], PDO::PARAM_STR);
      $stmt->bindValue(':Password', $data['Password'], PDO::PARAM_STR);
      $stmt->bindValue(':id', $id, PDO::PARAM_INT);

      return $stmt->execute();
    }

    public function getById(int $id): array | bool
    {
        try{

          $query = "SELECT * FROM users WHERE UserID = :id";
          $stmt = $this->conn->prepare($query);
          $stmt->bindParam(':id', $id, PDO::PARAM_INT);
          $stmt->execute();

        }catch (PDOException $e) {
          // Handle the exception
          http_response_code(500);
          // Log the error message  
          json_decode("Error: " . $e->getMessage());
        }

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: false;
    }

    public function getAll(): array
    {
        try{
          $query = "SELECT * FROM users ORDER BY UserID ASC";
          $stmt = $this->conn->prepare($query);
          $stmt->execute();
        }
        catch (PDOException $e) {
          // Handle the exception
          http_response_code(500);
          // Log the error message  
          json_decode("Error: " . $e->getMessage());
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update(array $current, array $new): int
    {
      $rowsEffected = 0;
      try{
        $query = "UPDATE users SET UserName = :UserName, Password = :Password WHERE UserID = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':UserName', $new['UserName'] ?? $current['UserName'], PDO::PARAM_STR);
        $stmt->bindValue(':Password', $new['Password'] ?? $current['Password'], PDO::PARAM_STR);
        $stmt->bindValue(':id', $current['UserID'], PDO::PARAM_INT);
        $stmt->execute();
        $rowsEffected = (int)$stmt->rowCount();
      }
      catch (PDOException $e) {
        // Handle the exception
        http_response_code(500);
        // Log the error message  
        json_decode("Error: " . $e->getMessage());
      }
      finally{
        return $rowsEffected;
      }
    }

    public function delete(int $id): bool
    { 
      $isDeleted = false;
       try{
        
        $query = "DELETE FROM users WHERE UserID = :id";
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

    public function getValidationErrors(array $data): array
    {
        $errors = [];

        if (empty($data['UserName'])) {
            $errors['UserName'] = 'UserName is required';
        }

        if (empty($data['Password'])) {
            $errors['Password'] = 'Password is required';
        }

        return $errors;
    }

  }
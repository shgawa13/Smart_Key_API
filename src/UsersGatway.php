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
        var_dump($data);
        try {
            $query = "INSERT INTO users (UserName, Password, Email,IsAdmin) 
            VALUES (:UserName, :Password,:Email, :IsAdmin)";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':UserName', $data['UserName']);
            $stmt->bindParam(':Password', $data['Password']);
            $stmt->bindParam(':Email', $data['Email']);
            // If the user is an admin, set IsAdmin to true, otherwise false
            $stmt->bindParam(':IsAdmin', $data['IsAdmin']);
            $stmt->execute();

            // Return the last inserted ID
            $newId = (int)$this->conn->lastInsertId();
            var_dump($newId);
        } catch (PDOException $e) {
           
           http_response_code(500);
          // Log the error message  
          json_decode("Error: " . $e->getMessage());
          $newId = 0;
        }

        return $newId;
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
        $query = "UPDATE users SET UserName = :UserName, Password = :Password, Email= :Email ,IsAdmin = :IsAdmin
        WHERE UserID = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':UserName', $new['UserName'] ?? $current['UserName'], PDO::PARAM_STR);
        $stmt->bindValue(':Password', $new['Password'] ?? $current['Password'], PDO::PARAM_STR);
        $stmt->bindValue(':Email', $new['Email'] ?? $current['Email'], PDO::PARAM_STR);
        $stmt->bindValue(':IsAdmin', $new['IsAdmin'] ?? $current['IsAdmin'], PDO::PARAM_BOOL);
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

    public function Login(array $data): array | false
    {
        
        try {
            $query = "SELECT * FROM users WHERE Email = :Email AND Password = :Password";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':Email', $data['Email']);
            $stmt->bindParam(':Password', $data['Password']);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC) ?: false;
        } catch (PDOException $e) {
            http_response_code(500);
            json_decode("Error: " . $e->getMessage());
            return false;
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
<?php

class Database
{
    public function __construct(private string $host,
                                private string $name,
                                private string $user,
                                private string $password)
    {}
    public function getConnection(): PDO
    {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->name}; charset=utf8";
            return  new PDO($dsn, $this->user, $this->password, [
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_STRINGIFY_FETCHES => false,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
            
        } catch (PDOException $e) {
            echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
            exit;
        }
    }
}
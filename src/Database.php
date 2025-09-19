<?php
class Database {
    private $host = 'localhost';
    private $username = 'seedbank_user';
    private $password = 'Mypassword@02';
    // private $database = 'my_database';
    private $database = 'seedbank_db';
    private $connection;

    public function __construct() {
        $this->connect();
    }

    private function connect() {
        try {
            $this->connection = new mysqli($this->host, $this->username, $this->password, $this->database);
            
            if ($this->connection->connect_error) {
                throw new Exception("Connection failed: " . $this->connection->connect_error);
            }
            
            $this->connection->set_charset("utf8");
        } catch (Exception $e) {
            die("Database connection error: " . $e->getMessage());
        }
    }

    public function getConnection() {
        return $this->connection;
    }

    public function close() {
        if ($this->connection) {
            $this->connection->close();
        }
    }

    // Execute prepared statement
    public function executeQuery($sql, $types = "", $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->connection->error);
            }

            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }

            $stmt->execute();
            
            if ($stmt->error) {
                throw new Exception("Execute failed: " . $stmt->error);
            }

            return $stmt;
        } catch (Exception $e) {
            die("Query error: " . $e->getMessage());
        }
    }
}?><?php
require_once 'Database.php';
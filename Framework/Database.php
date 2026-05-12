<?php
namespace Framework;

/* import global classes which are outside namespace */
use PDO;
use PDOException;
use Exception;


class Database
{
    public $conn;

    /**
     * @param array $config
     */
    public function __construct($config)
    {
        $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']}";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ];

        try{
            $this->conn = new PDO($dsn, $config['username'], $config['password'], $options);
        }
        catch(PDOException $e){
            throw new Exception("Database connection failed: {$e->getMessage()}");
        }
    }

    /**
     * query database
     * @param string $query
     * @param array $params
     * @throws PDOException
     */
    public function query($query, $params= []){        
        try{
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            return $stmt;
        }
        catch(PDOException $e){
            throw new Exception("Query failed to execute: {$e->getMessage()}");
        }
    }
}
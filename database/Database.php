<?php
require_once("config/config.php");

class Database {
  private $host = DB_HOST;
  private $user = DB_USER;
  private $password = DB_PASSWORD;
  private $dbname = DB_NAME;

  private $connection;
  private $error;
  private $statement;
  private $dbconnected = false;

  public function __construct() {
    // Set PDO connection
    $dsn = 'mysql:host=' . $this->host . ';' . 'dbname=' . $this->dbname;
    $options = array(
      PDO::ATTR_PERSISTENT => true,
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    );

    try {
      $this->connection = new PDO($dsn, $this->user, $this->password, $options);
      $this->dbconnected = true;
    } catch (PDOException $e) {
      $this->error = $e->getMessage() . PHP_EOL;
      $this->dbconnected = false;
    }
  }

  public function getError() {
    return $this->error;
  }

  public function isConnected() {
    return $this->dbconnected;
  }

  // Prepare statements with query
  public function query($query) {
    $this->statement = $this->connection->prepare($query);
  }

  // Execute the perpared statement
  public function execute() {
    return $this->statement->execute();
  }

  // Get result set as Array of Objects
  public function resultSet() {
    $this->execute();
    return $this->statement->fetchAll(PDO::FETCH_OBJ);
  }

  // Get record row count
  public function rowCount() {
    return $this->statement->rowCount();
  }

  // Get single record
  public function single() {
    $this->execute();
    return $this->statement->fetch(PDO::FETCH_OBJ);
  }

  public function bind($param, $value, $type = null) {
    if (is_null($type)) {
      switch (true) {
        case is_int($value):
          $type = PDO::PARAM_INT;
          break;
        case is_bool($value):
          $type = PDO::PARAM_BOOL;
          break;
        case is_null($value):
          $type = PDO::PARAM_NULL;
          break;
        default:
          $type = PDO::PARAM_STR;
      }
    }
    $this->statement->bindValue($param, $value, $type);
  }
}

<?php

namespace App;

use PDO;
use PDOException;

class Database
{
  private static $instance = null;
  private $connection;

  private function __construct()
  {
    try {
      $this->connection = new PDO('mysql:host=localhost;dbname=barberia', 'root', '');
      $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
      die('Error de conexión: ' . $e->getMessage());
    }
  }

  public static function getInstance()
  {
    if (self::$instance === null) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  public function getConnection()
  {
    return $this->connection;
  }
}

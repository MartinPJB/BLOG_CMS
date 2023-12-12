<?php

namespace Core\Database;

use Core\Config;
use PDO;
use PDOException;

/**
 * Database manager | Handles all actions related to the database
 */
class Manager
{
  /**
   * The PDO connection to the database
   *
   * @var PDO|null
   */
  private static $pdo = null;

  /**
   * The credentials to connect to the database
   */
  private static $credentials;

  /**
   * The name of the current database
   *
   * @var string
   */
  private static $current_db_name;

  /**
   * Prevents direct instantiation of the class
   */
  private function __construct()
  {
  }

  /**
   * Returns the PDO connection to the database
   *
   * @return PDO
   */
  public static function getConnection()
  {
    if (!self::$pdo) {
      $host = Config::get('database_host') ? Config::get('database_host') : 'localhost';

      try {
        self::$pdo = new PDO("mysql:host=$host;charset=utf8mb4", Config::get('database_user'), Config::get('database_password'));
        self::$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
      }
    }

    return self::$pdo;
  }

  /**
   * Connects to a specific database
   *
   * @param string $database The name of the database to connect to
   */
  public static function connectToDatabase($database)
  {
    self::$current_db_name = $database;
    self::executeStatement("USE `{$database}`");
  }

  /**
   * Get the current database name
   */
  public static function getCurrentDatabaseName()
  {
    return self::$current_db_name;
  }

  /**
   * Get the last inserted ID
   */
  public static function getLastInsertedId()
  {
    return self::getConnection()->lastInsertId();
  }

  /**
   * Creates a new database if it does not already exist
   *
   * @param string $database The name of the database to create
   */
  public static function createDatabase($database)
  {
    $sql = "CREATE DATABASE IF NOT EXISTS `{$database}`";
    self::executeStatement($sql);
  }

  /**
   * Drops a database if it exists
   *
   * @param string $database The name of the database to drop
   */
  public static function dropDatabase($database)
  {
    $sql = "DROP DATABASE IF EXISTS `{$database}`";
    self::executeStatement($sql);
  }

  /**
   * Creates a new table if it does not already exist
   *
   * @param string $table The name of the table to create
   * @param array $columns The columns of the table to create
   * @param array $options The options for the table to create
   */
  public static function createTable($table, $columns, $options = [])
  {
    $columnDefinitions = [];
    foreach ($columns as $columnName => $columnDefinition) {
      $columnDefinitions[] = "`$columnName` $columnDefinition";
    }

    $columnsString = implode(', ', $columnDefinitions);
    $columnsString .= !empty($options) ? ', ' . implode(', ', $options) : '';
    $sql = "CREATE TABLE IF NOT EXISTS `$table` ($columnsString)";
    self::executeStatement($sql);
  }

  /**
   * Reads data from a table
   *
   * @param string $table The name of the table to read from
   * @param array $columns The columns to read from
   * @param array $whereConditions The conditions for the read
   * @return array Returns an array containing the data read
   */
  public static function read($table, $columns = [], $whereConditions = [])
  {
    $columns = self::buildColumns($columns);
    $whereClause = self::buildWhereClause($table, $whereConditions);
    $sql = "SELECT $columns FROM `$table`$whereClause";
    return self::executeAndFetch($sql, $whereConditions);
  }

  /**
   * Reads data from a table using joins
   *
   * @param string $table The name of the table to read from
   * @param array $columns The columns to read from
   * @param array $whereConditions The conditions for the read
   * @param array $joinTables The tables to join
   * @param array $joinConditions The conditions for the joins
   * @return array Returns an array containing the data read
   */
  public static function readWithJoin($table, $columns = [], $whereConditions = [], $joinTables = [], $joinConditions = [])
  {
    $columns = self::buildColumns($columns);
    $whereClause = self::buildWhereClause($table, $whereConditions);

    $joinClause = '';
    if (!empty($joinTables) && !empty($joinConditions)) {
      $joinClause = ' ' . implode(' ', array_map(function ($joinTable, $joinCondition) {
        return "JOIN `$joinTable` ON $joinCondition";
      }, $joinTables, $joinConditions));
    }

    $sql = "SELECT $columns FROM `$table`$joinClause$whereClause";
    return self::executeAndFetch($sql, $whereConditions);
  }

  /**
   * Creates data in a table
   *
   * @param string $table The name of the table to create data in
   * @param array $data The data to create
   */
  public static function create($table, $data)
  {
    $columns = implode(', ', array_keys($data));
    $values = implode(', ', array_map(function ($column) {
      return ":$column";
    }, array_keys($data)));

    $sql = "INSERT INTO `$table` ($columns) VALUES ($values)";
    self::execute($sql, $data);
  }

  /**
   * Updates data in a table
   *
   * @param string $table The name of the table to update data in
   * @param array $data The data to update
   * @param array $whereConditions The conditions for the update
   * @return array Returns an array containing the data updated
   */
  public static function update($table, $data, $whereConditions)
  {
    $setClause = self::buildSetClause($data, $table);
    $whereClause = self::buildWhereClause($table, $whereConditions);

    $sql = "UPDATE `$table` $setClause $whereClause";
    return self::executeAndFetch($sql, array_merge($data, $whereConditions));
  }

  /**
   * Deletes data from a table
   *
   * @param string $table The name of the table to delete data from
   * @param array $whereConditions The conditions for the delete
   */
  public static function delete($table, $whereConditions)
  {
    $whereClause = self::buildWhereClause($table, $whereConditions);

    $sql = "DELETE FROM `$table` $whereClause";
    self::execute($sql, $whereConditions);
  }

  /**
   * Returns the name of the current database
   *
   * @return string Returns the name of the current database
   */
  public static function getDatabaseName()
  {
    return self::$current_db_name;
  }

  /**
   * Executes a SQL statement and returns the results
   *
   * @param string $sql The SQL statement to execute
   * @param array $params The parameters for the SQL statement
   * @return array Returns an array containing the results of the SQL statement
   */
  public static function executeAndFetch($sql, $params = [])
  {
    try {
      $stmt = self::getConnection()->prepare($sql);
      $stmt->execute($params);
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
      return [];
    }
  }

  /**
   * Executes a SQL statement and does not return the results
   *
   * @param string $sql The SQL statement to execute
   * @param array $params The parameters for the SQL statement
   */
  public static function execute($sql, $params = [])
  {
    $stmt = self::getConnection()->prepare($sql);
    $stmt->execute($params);
  }

  /**
   * Executes a SQL statement
   *
   * @param string $sql The SQL statement to execute Returns nothing
   */
  public static function executeStatement($sql)
  {
    self::getConnection()->exec($sql);
  }

  /**
   * Checks if an array is associative or sequential
   *
   * The code of this function is based on the PHP 8.1 function array_is_list()
   *
   * @param array $array The array to check
   * @return boolean Returns True if the array is associative, otherwise False
   */
  private static function isAssociativeArray($array)
  {
    if ($array === []) {
      return true;
    }
    return array_keys($array) === range(0, count($array) - 1);
  }

  /**
   * Builds the clause of the columns to read
   *
   * @param array $columns The columns to read
   * @return string Returns the clause of the columns to read
   */
  private static function buildColumns($columns)
  {
    if (self::isAssociativeArray($columns)) {
      return empty($columns) ? '*' : implode(', ', $columns);
    }

    $result = [];

    foreach ($columns as $table => $values) {
      if (self::isAssociativeArray($columns[$table])) {
        foreach ($values as $value) {
          $result[] = "$table.$value";
        }
      }
    }

    return empty($result) ? '*' : implode(', ', $result);
  }

  /**
   * Builds the clause of the conditions to read
   *
   * @param array $whereConditions The conditions to read
   * @return string Returns the clause of the conditions to read
   */
  private static function buildWhereClause($tableName, $whereConditions)
  {
    $whereClause = '';
    if (!empty($whereConditions)) {
      $conditions = [];
      foreach ($whereConditions as $columnName => $value) {
        $conditions[] = "`$tableName`.`$columnName` = :$columnName";
      }
      $whereClause = ' WHERE ' . implode(' AND ', $conditions);
    }
    return $whereClause;
  }

  /**
   * Builds the clause of the data to update
   *
   * @param array $data The data to update
   * @param string $table The name of the table to update
   * @return string Returns the clause of the data to update
   */
  private static function buildSetClause($data, $table)
  {
    $setClause = '';
    if (!empty($data)) {
      $setValues = [];
      foreach ($data as $columnName => $value) {
        $setValues[] = "`$table`.`$columnName` = :$columnName";
      }
      $setClause = ' SET ' . implode(', ', $setValues);
    }
    return $setClause;
  }
}

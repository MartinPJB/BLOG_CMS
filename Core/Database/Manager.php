<?php

namespace Core\Database;

use PDO;
use PDOException;

class Manager
{

  /**
   * The PDO connection to the database
   *
   * @var PDO|null
   */
  private static ?PDO $pdo = null;

  /**
   * The credentials to connect to the database
   */
  private static array $credentials;

  /**
   * The name of the current database
   *
   * @var string
   */
  private static string $current_db_name;

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
  public static function getConnection(array $credentials = []): PDO
  {
    if (empty($credentials)) {
      $credentials = self::$credentials;
    }
    self::$credentials = $credentials;

    if (!self::$pdo) {
      $host = self::$credentials['host'] ?? 'localhost';
      self::$pdo = new PDO("mysql:host=$host;charset=utf8mb4", $credentials['user'], $credentials['password'], [
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      ]);
    }

    return self::$pdo;
  }

  /**
   * Connects to a specific database
   *
   * @param string $database The name of the database to connect to
   * @return void
   */
  public static function connectToDatabase(string $database): void
  {
    self::$current_db_name = $database;
    self::getConnection()->exec("USE `{$database}`");
  }

  /**
   * Creates a new database if it does not already exist
   *
   * @param string $database The name of the database to create
   * @return void
   */
  public static function createDatabase(string $database): void
  {
    $sql = "CREATE DATABASE IF NOT EXISTS `{$database}`";
    self::getConnection()->exec($sql);
  }

  /**
   * Drops a database if it exists
   *
   * @param string $database The name of the database to drop
   * @return void
   */
  public static function dropDatabase(string $database): void
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
   * @return void
   */
  public static function createTable(string $table, array $columns, array $options = []): void
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
  public static function read(string $table, array $columns = [], array $whereConditions = []): array
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
  public static function readWithJoin(string $table, array $columns = [], array $whereConditions = [], array $joinTables = [], array $joinConditions = []): array
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
   * @return array Returns an array containing the data created
   */
  public static function create(string $table, array $data): array
  {
    $columns = implode(', ', array_keys($data));
    $values = implode(', ', array_map(fn ($column) => ":$column", array_keys($data)));

    $sql = "INSERT INTO `$table` ($columns) VALUES ($values)";
    return self::executeAndFetch($sql, $data);
  }

  /**
   * Updates data in a table
   *
   * @param string $table The name of the table to update data in
   * @param array $data The data to update
   * @param array $whereConditions The conditions for the update
   * @return array Returns an array containing the data updated
   */
  public static function update(string $table, array $data, array $whereConditions): array
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
   * @return array Returns an array containing the data deleted
   */
  public static function delete(string $table, array $whereConditions): array
  {
    $whereClause = self::buildWhereClause($table, $whereConditions);

    $sql = "DELETE FROM `$table` $whereClause";
    return self::executeAndFetch($sql, $whereConditions);
  }

  /**
   * Returns the name of the current database
   *
   * @return string Returns the name of the current database
   */
  public static function getDatabaseName(): string
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
  public static function executeAndFetch(string $sql, array $params = []): array
  {
    $stmt = self::getConnection()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * Executes a SQL statement
   *
   * @param string $sql The SQL statement to execute
   * @return void Returns nothing
   */
  public static function executeStatement(string $sql): void
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
  private static function isAssociativeArray(array $array): bool
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
  private static function buildColumns(array $columns): string
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
  private static function buildWhereClause(string $tableName, array $whereConditions): string
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
  private static function buildSetClause(array $data, string $table): string
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

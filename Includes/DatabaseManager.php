<?php

namespace Includes;

use PDO;
use PDOException;

/**
 * Classe DatabaseManager
 *
 * Cette classe permet de gérer les opérations de base de données principales.
 */
class DatabaseManager
{

  /**
   * La connexion PDO à la base de données
   *
   * @var PDO|null
   */
  private ?PDO $pdo;

  /**
   * Le nom de la base de données courante
   *
   * @var string
   */
  private string $current_db_name;

  /**
   * Constructeur de la classe DatabaseManager
   *
   * @param array $credentials Les identifiants de connexion à la base de données (host, user, password)
   */
  public function __construct(array $credentials)
  {
    $dsn = "mysql:host={$credentials['host']};charset=utf8mb4";
    $options = [
      PDO::ATTR_EMULATE_PREPARES => false,
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ];

    try {
      $this->pdo = new PDO($dsn, $credentials['user'], $credentials['password'], $options);
      $this->current_db_name = "";
    } catch (PDOException $e) {
      throw new PDOException($e->getMessage(), (int) $e->getCode());
    }
  }

  /**
   * Se déconnecte de la connexion PDO.
   *
   * @return void Retourne rien
   */
  public function disconnect(): void
  {
    $this->current_db_name = "";
    $this->pdo = NULL;
  }

  /**
   * Crée une base de données si elle n'existe pas déjà
   *
   * @param string $database Le nom de la base de données à créer
   * @return void Retourne rien
   */
  public function createDatabase(string $database): void
  {
    $sql = "CREATE DATABASE IF NOT EXISTS `{$database}`";
    $this->pdo->exec($sql);
  }

  /**
   * Permet de se connecter à une base de données spécifique
   *
   * @param string $database Le nom de la base de données à laquelle se connecter
   * @return void Retourne rien
   */
  public function connectToDatabase(string $database): void
  {
    $this->current_db_name = $database;
    $this->pdo->exec("USE `{$database}`");
  }

  /**
   * Supprime une base de données si elle existe
   *
   * @param string $database Le nom de la base de données à supprimer
   * @return void Retourne rien
   */
  public function dropDatabase(string $database): void
  {
    $sql = "DROP DATABASE IF EXISTS `{$database}`";
    $this->executeStatement($sql);
  }

  /**
   * Crée une table si elle n'existe pas déjà
   *
   * @param string $table Le nom de la table à créer
   * @param array $columns Les colonnes de la table à créer
   * @return void Retourne rien
   */
  public function createTable(string $table, array $columns, array $options = []): void
  {
    $columnDefinitions = [];
    foreach ($columns as $columnName => $columnDefinition) {
      $columnDefinitions[] = "`$columnName` $columnDefinition";
    }

    $columnsString = implode(', ', $columnDefinitions);
    $columnsString .= !empty($options) ? ', ' . implode(', ', $options) : '';
    $sql = "CREATE TABLE IF NOT EXISTS `$table` ($columnsString)";
    $this->executeStatement($sql);
  }

  /**
   * Lis des données dans une table
   *
   * @param string $table Le nom de la table à lire
   * @param array $columns Les colonnes à lire
   * @param array $whereConditions Les conditions de lecture
   * @return array Retourne un tableau contenant les données lues
   */
  public function read(string $table, array $columns = [], array $whereConditions = []): array
  {
    $columns = $this->buildColumns($columns);
    $whereClause = $this->buildWhereClause($table, $whereConditions);

    $sql = "SELECT $columns FROM `$table`$whereClause";
    return $this->executeAndFetch($sql, $whereConditions);
  }

  /**
   * Lis des données dans une table en utilisant une/des jointure(s)
   *
   * @param string $table Le nom de la table à lire
   * @param array $columns Les colonnes à lire
   * @param array $whereConditions Les conditions de lecture
   * @param array $joinTables Les tables à joindre
   * @param array $joinConditions Les conditions de jointure
   * @return array Retourne un tableau contenant les données lues
   */
  public function readWithJoin(string $table, array $columns = [], array $whereConditions = [], array $joinTables = [], array $joinConditions = []): array
  {
    $columns = $this->buildColumns($columns);
    $whereClause = $this->buildWhereClause($table, $whereConditions);

    $joinClause = '';
    if (!empty($joinTables) && !empty($joinConditions)) {
      $joinClause = ' ' . implode(' ', array_map(function ($joinTable, $joinCondition) {
        return "JOIN `$joinTable` ON $joinCondition";
      }, $joinTables, $joinConditions));
    }

    $sql = "SELECT $columns FROM `$table`$joinClause$whereClause";
    return $this->executeAndFetch($sql, $whereConditions);
  }

  /**
   * Crée des données dans une table
   *
   * @param string $table Le nom de la table dans laquelle créer les données
   * @param array $data Les données à créer
   * @return array Retourne un tableau contenant les données créées
   */
  public function create(string $table, array $data): array
  {
    $columns = implode(', ', array_keys($data));
    $values = implode(', ', array_map(fn ($column) => ":$column", array_keys($data)));

    $sql = "INSERT INTO `$table` ($columns) VALUES ($values)";
    return $this->executeAndFetch($sql, $data);
  }

  /**
   * Met à jour des données dans une table
   *
   * @param string $table Le nom de la table dans laquelle mettre à jour les données
   * @param array $data Les données à mettre à jour
   * @param array $whereConditions Les conditions de mise à jour
   * @return array Retourne un tableau contenant les données mises à jour
   */
  public function update(string $table, array $data, array $whereConditions): array
  {
    $setClause = $this->buildSetClause($whereConditions, $table);
    $whereClause = $this->buildWhereClause($table, $data);

    $sql = "UPDATE `$table` $setClause $whereClause";
    return $this->executeAndFetch($sql, array_merge($data, $whereConditions));
  }

  /**
   * Supprime des données dans une table
   *
   * @param string $table Le nom de la table dans laquelle supprimer les données
   * @param array $whereConditions Les conditions de suppression
   * @return array Retourne un tableau contenant les données supprimées
   */
  public function delete(string $table, array $whereConditions): array
  {
    $whereClause = $this->buildWhereClause($table, $whereConditions);

    $sql = "DELETE FROM `$table` $whereClause";
    return $this->executeAndFetch($sql, $whereConditions);
  }

  /**
   * Retourne le nom de la base de données courante
   *
   * @return string Retourne le nom de la base de données courante
   */
  public function getDatabaseName(): string
  {
    return $this->current_db_name;
  }

  /**
   * Retourne la connexion PDO à la base de données
   *
   * @return PDO Retourne la connexion PDO à la base de données
   */
  public function getConnection(): PDO
  {
    return $this->pdo;
  }

  /**
   * Exécute une requête SQL
   *
   * @param string $sql La requête SQL à exécuter
   * @return void Retourne rien
   */
  private function executeStatement(string $sql)
  {
    $this->pdo->exec($sql);
  }

  /**
   * Vérifie si un tableau est associatif ou séquentiel
   * Le code de cette fonctions se base sur la fonction en PHP 8.1 array_is_list()
   *
   * @param array $array Le tableau à vérifier
   * @return boolean Retourne True si le tableau est associatif, sinon False
   */
  function isAssociativeArray(array $array): bool
  {
    if ($array === []) {
        return true;
    }
    return array_keys($array) === range(0, count($array) - 1);
  }


  /**
   * Construit la clause des colonnes à lire
   *
   * @param array $columns Les colonnes à lire
   * @return string Retourne la clause des colonnes à lire
   */
  private function buildColumns(array $columns): string
  {
    if ($this->isAssociativeArray($columns)) {
      return empty($columns) ? '*' : implode(', ', $columns);
    }

    $result = [];

    foreach ($columns as $table => $values) {
      if ($this->isAssociativeArray($columns[$table])) {
        foreach ($values as $value) {
          $result[] = "$table.$value";
        }
      }
    }

    return empty($result) ? '*' : implode(', ', $result);
  }

  /**
   * Construit la clause des conditions de lecture
   *
   * @param array $whereConditions Les conditions de lecture
   * @return string Retourne la clause des conditions de lecture
   */
  private function buildWhereClause(string $tableName, array $whereConditions): string
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
   * Construit la clause des données à mettre à jour
   *
   * @param array $data Les données à mettre à jour
   * @param string $table Le nom de la table dans laquelle mettre à jour les données
   * @return string Retourne la clause des données à mettre à jour
   */
  private function buildSetClause(array $data, string $table): string
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

  /**
   * Exécute une requête SQL et retourne les résultats
   *
   * @param string $sql La requête SQL à exécuter
   * @param array $params Les paramètres de la requête SQL
   * @return array Retourne un tableau contenant les résultats de la requête SQL
   */
  private function executeAndFetch(string $sql, array $params = []): array
  {
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}

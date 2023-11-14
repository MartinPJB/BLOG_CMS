<?php

namespace Model;

use Inc\DatabaseManager;

/**
 * Classe pour gérer les utilisateurs dans la base de données.
 */
class User extends DatabaseManager
{
  /**
   * Le nom de la table dans la base de données.
   *
   * @var string $tableName
   */
  private string $tableName = "users";

  /**
   * Constructeur de la classe User.
   *
   * @param array $database_credentials Les informations de connexion à la base de données.
   */
  public function __construct(array $database_credentials)
  {
    parent::__construct($database_credentials);
    $this->connectToDatabase($database_credentials['database']);
  }

  /**
   * Crée un nouvel utilisateur dans la base de données.
   *
   * @param array $content Les données de l'utilisateur à créer.
   *
   * @return array Les données de l'utilisateur créé
   */
  public function createUser(array $content): array
  {
    return $this->create($this->tableName, $content);
  }

  /**
   * Récupère un utilisateur de la base de données.
   *
   * @param array $id L'ID de l'utilisateur à récupérer.
   * @param array $conditions Les conditions de sélection (facultatives).
   *
   * @return array Les données de l'utilisateur récupéré
   */
  public function readUser(array $id = [], array $conditions = []): array
  {
    return $this->read($this->tableName, $id, $conditions);
  }

  /**
   * Met à jour un utilisateur dans la base de données.
   *
   * @param int $id L'ID de l'utilisateur à mettre à jour.
   * @param array $updates Les données de mise à jour.
   *
   * @return array Les données de l'utilisateur mis à jour
   */
  public function updateUser(int $id, array $updates): array
  {
    return $this->update($this->tableName, ['id' => $id], $updates);
  }

  /**
   * Supprime un utilisateur de la base de données.
   *
   * @param int $id L'ID de l'utilisateur à supprimer.
   *
   * @return array Les données de l'utilisateur supprimé
   */
  public function deleteUser(int $id): array
  {
    return $this->delete($this->tableName, ['id' => $id]);
  }
}

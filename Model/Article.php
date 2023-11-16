<?php

namespace Model;

use Includes\DatabaseManager;

/**
 * Classe pour gérer les éléments dans la base de données.
 */
class Article extends DatabaseManager
{
  /**
   * Le nom de la table dans la base de données.
   *
   * @var string $tableName
   */
  private string $tableName = "articles";

  /**
   * Constructeur de la classe Element.
   *
   * @param array $database_credentials Les informations de connexion à la base de données.
   */
  public function __construct(array $database_credentials)
  {
    parent::__construct($database_credentials);
    $this->connectToDatabase($database_credentials['database']);
  }

  /**
   * Crée un nouvel élément dans la base de données.
   *
   * @param array $content Les données de l'élément à créer.
   *
   * @return array Les données de l'élément créé
   */
  public function createElement(array $content): array
  {
    return $this->create($this->tableName, $content);
  }

  /**
   * Récupère un élément de la base de données.
   *
   * @param array $id L'ID de l'élément à récupérer.
   * @param array $conditions Les conditions de sélection (facultatives).
   *
   * @return array Les données de l'élément récupéré
   */
  public function readElement(array $id = [], array $conditions = []): array
  {
    return $this->read($this->tableName, $id, $conditions);
  }

  /**
   * Met à jour un élément dans la base de données.
   *
   * @param int $id L'ID de l'élément à mettre à jour.
   * @param array $updates Les données de mise à jour.
   *
   * @return array Les données de l'élément mis à jour
   */
  public function updateElement(int $id, array $updates): array
  {
    return $this->update($this->tableName, ['id' => $id], $updates);
  }

  /**
   * Supprime un élément de la base de données.
   *
   * @param int $id L'ID de l'élément à supprimer.
   *
   * @return array Les données de l'élément supprimé
   */
  public function deleteElement(int $id): array
  {
    return $this->delete($this->tableName, ['id' => $id]);
  }
}

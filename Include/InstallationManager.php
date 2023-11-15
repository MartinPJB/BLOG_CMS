<?php

namespace Include;

use Exception;
use PDOException;

/**
 * Gestionnaire d'installation.
 */
class InstallationManager
{
  /**
   * La classe nous permettant d'intérargir avec la base de données.
   *
   * @var DatabaseManager $database
   */
  private DatabaseManager $database;

  /**
   * Constructeur de la classe.
   *
   * @param array $database_credentials Les informations de connexion à la base de données.
   */
  public function __construct(array $database_credentials)
  {
    if (!isset($database_credentials) || empty($database_credentials)) {
      throw new Exception("Les identifiants de la base de données ne sont pas définis.");
    }
    $this->database = new DatabaseManager($database_credentials);
  }

  /**
   * Vérifie si l'application est déjà installée.
   *
   * @param string $db_name Le nom de la base de données à vérifier.
   *
   * @return bool True si l'application est installée, sinon False.
   */
  public function isInstalled(string $db_name): bool
  {
    $sql = "SHOW DATABASES LIKE '$db_name'";
    $stmt = $this->database->getConnection()->prepare($sql);
    $stmt->execute();

    return $stmt->rowCount() > 0;
  }

  /**
   * Initialise la base de données.
   *
   * @param string $db_name Le nom de la base de données à créer.
   *
   * @return void Retourne rien
   */
  public function initializeDatabase(string $db_name): void
  {
    try {
      $this->database->createDatabase($db_name);
      $this->database->connectToDatabase($db_name);
    } catch (PDOException $e) {
      throw new PDOException($e->getMessage(), (int)$e->getCode());
    }
  }

  /**
   * Initialise les tables de la base de données.
   *
   * @return void Retourne rien
   */
  public function initializeTables(): void
  {
    $this->createElementsTable();
  }

  /**
   * Crée la table "elements" et les autres dans la base de données.
   *
   * @return void Retourne rien
   */
  private function createElementsTable(): void
  {
    $this->database->createTable('site_settings', [
      'id' => 'INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY',
      'name' => 'VARCHAR(255) NOT NULL',
      'description' => 'TEXT NOT NULL',
    ]);
    echo "Table site_settings créée avec succès.<br>";

    $this->database->createTable('users', [
      'id' => 'INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY',
      'username' => 'VARCHAR(255) NOT NULL',
      'password' => 'VARCHAR(255) NOT NULL',
      'email' => 'VARCHAR(255) NOT NULL',
      'role' => 'VARCHAR(255) NOT NULL',
    ]);
    echo "Table users créée avec succès.<br>";

    $this->database->createTable('categories', [
      'id' => 'INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY',
      'name' => 'VARCHAR(255) NOT NULL',
    ]);
    echo "Table categories créée avec succès.<br>";

    $this->database->createTable('articles', [
      'id' => 'INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY',
      'title' => 'VARCHAR(255) NOT NULL',
      'description' => 'TEXT NOT NULL',
      'author_id' => 'INT(6) UNSIGNED NOT NULL',
      'date' => 'DATETIME NOT NULL',
      'image' => 'VARCHAR(255) NOT NULL',
      'category_id' => 'INT(6) UNSIGNED NOT NULL',
      'tags' => 'VARCHAR(255) NOT NULL',
      'draft' => 'BOOLEAN NOT NULL DEFAULT FALSE',
      'published' => 'BOOLEAN NOT NULL DEFAULT FALSE',
    ], [
      'FOREIGN KEY (author_id) REFERENCES users (id) ON DELETE CASCADE',
      'FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE CASCADE'
    ]);
    echo "Table articles créée avec succès.<br>";

    $this->database->createTable('medias', [
      'id' => 'INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY',
      'name' => 'VARCHAR(255) NOT NULL',
      'src' => 'VARCHAR(255) NOT NULL',
      'alt' => 'TEXT NOT NULL',
    ]);
    echo "Table medias créée avec succès.<br>";

    $this->database->createTable('elements', [
      'id' => 'INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY',
      'balise' => 'VARCHAR(255) NOT NULL',
      'text' => 'TEXT NOT NULL',
      'css' => 'TEXT NOT NULL',
      'src' => 'VARCHAR(255) NOT NULL',
      'alt' => 'TEXT NOT NULL',
      'href' => 'VARCHAR(255) NOT NULL',
      'article_id' => 'INT(6) UNSIGNED NOT NULL'
    ], [
      'FOREIGN KEY (article_id) REFERENCES articles (id) ON DELETE CASCADE'
    ]);
    echo "Table elements créée avec succès.<br>";
  }

  /**
   * Initialise le site avec des données de configuration.
   *
   * @param array $config Les informations de configuration.
   * @return void Retourne rien
   */
  public function initializeSite(array $config): void
  {
    try {
      // Création du compte administrateur
      $this->database->create('users', [
        'username' => $config['name'],
        'password' => password_hash($config['password'], PASSWORD_DEFAULT),
        'email' => $config['email'],
        'role' => 'admin',
      ]);

      // Création des catégories par défaut
      $this->database->create('categories', [
        'name' => 'Catégorie par défaut',
      ]);

      // Création des articles par défaut
      $this->database->create('articles', [
        'title' => 'Article d\'exemple',
        'description' => 'Description de l\'article d\'exemple.',
        'author_id' => 1,
        'date' => date('Y-m-d H:i:s'),
        'image' => 'https://picsum.photos/seed/'. rand() .'/400/250',
        'category_id' => 1,
        'tags' => 'Exemple, Article de fou, Incroyable',
      ]);

      // Création des éléments par défaut
      $this->database->create('elements', [
        'balise' => 'h1',
        'text' => 'Exemple de h1 stocké dans la bdd!',
        'css' => 'color: red; font-size: 2em;',
        'article_id' => 1,
      ]);

      $this->database->create('elements', [
        'balise' => 'p',
        'text' => 'Mais c\'est incroyable me direz-vous!',
        'css' => 'color: blue; font-size: 1.2em;',
        'article_id' => 1,
      ]);

      // Ajout des paramètres du site
      $this->database->create('site_settings', [
        'name' => $config['name'],
        'description' => $config['description'],
      ]);

    } catch (Exception $e) {
      throw new Exception($e->getMessage(), (int)$e->getCode());
    }
  }

  /**
   * Effectue l'installation complète de l'application.
   *
   * @param array $config Les informations de configuration.
   *
   * @return void Retourne rien
   */
  public function install(array $config): void
  {
    $this->initializeDatabase($config['database']['database']);
    $this->initializeTables();
    $this->initializeSite($config['admin']);
  }

  /**
   * Destructeur de la classe.
   *
   * @return void Retourne rien
   */
  public function __destruct()
  {
    $this->database->disconnect();
    unset($this->database);
  }

  /**
   * Annule l'installation en supprimant la base de données.
   *
   * @return void Retourne rien
   */
  public function cancel(string $db_name): void
  {
    $this->database->dropDatabase($db_name);
  }
}

<?php

namespace Core\Database;

use \Core\Database\Manager;

/**
 * Database installer | Handles all actions related to the database installation
 */
class Installer
{
  /**
   * Prevents direct instantiation of the class
   */
  private function __construct()
  {
  }

  /**
   * Install the CMS by creating the database and all needed tables
   *
   * @param array $config The configuration array
   */
  public static function install(array $config): void
  {
    self::createDatabase($config);
    self::createTables();
    self::initializeData($config);
  }

  /**
   * Uninstall the CMS by deleting the database
   */
  public static function uninstall(): void
  {
    Manager::dropDatabase(Manager::getCurrentDatabaseName());
  }

  /**
   * Create the database in order to use the CMS
   *
   * @param array $config The configuration array
   */
  private static function createDatabase(array $config): void
  {
    Manager::createDatabase($config['database']['name']);
    Manager::connectToDatabase($config['database']['name']);
  }

  /**
   * Create all needed tables in the database in order to use the CMS
   */
  private static function createTables(): void
  {
    // Site settings table
    Manager::createTable('site_settings', [
      'id' => 'INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY',
      'name' => 'VARCHAR(255) NOT NULL',
      'description' => 'TEXT NOT NULL',
      'theme' => 'VARCHAR(255) NOT NULL DEFAULT "default"',
    ]);

    // Users table
    Manager::createTable('users', [
      'id' => 'INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY',
      'username' => 'VARCHAR(255) NOT NULL',
      'password' => 'VARCHAR(255) NOT NULL',
      'email' => 'VARCHAR(255) NOT NULL',
      'role' => 'VARCHAR(255) NOT NULL',
    ]);

    // Categories table
    Manager::createTable('categories', [
      'id' => 'INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY',
      'name' => 'VARCHAR(255) NOT NULL',
    ]);

    // Media table
    Manager::createTable('media', [
      'id' => 'INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY',
      'name' => 'VARCHAR(255) NOT NULL',
      'type' => 'VARCHAR(255) NOT NULL',
      'size' => 'INT(6) UNSIGNED NOT NULL',
      'path' => 'VARCHAR(255) NOT NULL',
      'alt' => 'VARCHAR(255) NOT NULL',
      'uploaded_at' => 'DATETIME NOT NULL',
    ]);

    // Articles table
    Manager::createTable('articles', [
      'id' => 'INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY',
      'title' => 'VARCHAR(255) NOT NULL',
      'description' => 'TEXT NOT NULL',
      'author_id' => 'INT(6) UNSIGNED NOT NULL',
      'date' => 'DATETIME NOT NULL',
      'image' => 'INT(6) UNSIGNED NOT NULL',
      'category_id' => 'INT(6) UNSIGNED NOT NULL',
      'tags' => 'VARCHAR(255) NOT NULL',
      'draft' => 'BOOLEAN NOT NULL DEFAULT FALSE',
      'published' => 'BOOLEAN NOT NULL DEFAULT FALSE',
    ], [
      'FOREIGN KEY (author_id) REFERENCES users (id) ON DELETE CASCADE',
      'FOREIGN KEY (image) REFERENCES media (id) ON DELETE CASCADE',
      'FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE CASCADE'
    ]);

    // Blocks table
    Manager::createTable('blocks', [
      'id' => 'INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY',
      'name' => 'VARCHAR(255) NOT NULL',
      'json_content' => 'JSON NOT NULL',
      'article_id' => 'INT(6) UNSIGNED NOT NULL'
    ], [
      'FOREIGN KEY (article_id) REFERENCES articles (id) ON DELETE CASCADE'
    ]);
  }

  /**
   * Sets up all the needed data in the database in order to use the CMS
   *
   * @param array $config The configuration array
   */
  private static function initializeData(array $config)
  {
    // Site settings
    Manager::create('site_settings', [
      'name' => $config['site']['name'],
      'description' => $config['site']['description'],
    ]);

    // Admin user
    Manager::create('users', [
      'username' => $config['admin']['name'],
      'password' => password_hash($config['admin']['password'], PASSWORD_DEFAULT),
      'email' => $config['admin']['email'],
      'role' => 'admin',
    ]);

    // Categories
    Manager::create('categories', [
      'name' => 'Uncategorized',
    ]);

    // Media
    Manager::create('media', [
      'name' => 'Default',
      'type' => 'image/jpeg',
      'size' => 0,
      'path' => 'https://picsum.photos/seed/' . rand() . '/400/250',
      'alt' => 'Default image',
      'uploaded_at' => date('Y-m-d H:i:s'),
    ]);

    // Articles
    Manager::create('articles', [
      'title' => 'Welcome to CUEJ_CMS',
      'description' => 'This is your first article. You can edit it or delete it.',
      'author_id' => 1,
      'date' => date('Y-m-d H:i:s'),
      'image' => 1,
      'category_id' => 1,
      'tags' => 'welcome, first, article',
      'draft' => false,
      'published' => true,
    ]);
  }
}

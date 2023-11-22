<?php

namespace Model;

use \Core\Database\Manager;

/**
 * Categories model
 */
class Categories
{
  private int $id;
  private string $name;

  /**
   * Constructor for the Categories model
   *
   * @param integer $id Category ID
   * @param string $name Category name
   */
  public function __construct(
    int $id,
    string $name
  ) {
    $this->id = $id;
    $this->name = $name;
  }

  /**
   * Get all categories from the database
   *
   * @return array
   */
  public static function getAllCategories(): array
  {
    $categories = Manager::read('categories');
    $result = [];

    foreach ($categories as $key => $category) {
      $result[] = new self(
        $category['id'],
        $category['name']
      );
    }

    return $result;
  }

  /**
   * Get a category by its ID
   *
   * @param integer $id Category ID
   * @return self
   */
  public static function getCategoryById(int $id): self
  {
    $category = Manager::read('categories', [], ['id' => $id])[0];

    return new self(
      $category['id'],
      $category['name']
    );
  }

  /**
   * Create a category
   *
   * @param string $name Category name
   * @return void
   */
  public static function createCategory(string $name): void
  {
    Manager::create('categories', ['name' => $name]);
  }

  /**
   * Update a category
   *
   * @param integer $id Category ID
   * @param string $name Category name
   * @return void
   */
  public static function updateCategory(int $id, string $name): void
  {
    Manager::update('categories', ['name' => $name], ['id' => $id]);
  }

  /**
   * Delete a category
   *
   * @param integer $id Category ID
   * @return void
   */
  public static function deleteCategory(int $id): void
  {
    Manager::delete('categories', ['id' => $id]);
  }

  /**
   * Get the value of id
   *
   * @return integer
   */
  public function getId(): int
  {
    return $this->id;
  }

  /**
   * Get the value of name
   *
   * @return string
   */
  public function getName(): string
  {
    return $this->name;
  }
}
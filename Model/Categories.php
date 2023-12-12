<?php

namespace Model;

use \Core\Database\Manager;

/**
 * Categories model | Handles all actions related to categories
 */
class Categories
{
  private $id;
  private $name;

  /**
   * Constructor for the Categories model
   *
   * @param integer $id Category ID
   * @param string $name Category name
   */
  public function __construct(
    $id,
    $name
  ) {
    $this->id = $id;
    $this->name = $name;
  }

  /**
   * Get all categories from the database
   *
   * @return array Array of categories
   */
  public static function getAllCategories()
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
   * @return self Category
   */
  public static function getCategoryById($id)
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
   */
  public static function create($name)
  {
    Manager::create('categories', ['name' => $name]);
  }

  /**
   * Update a category
   *
   * @param integer $id Category ID
   * @param string $name Category name
   */
  public static function update($id, $name)
  {
    Manager::update('categories', ['name' => $name], ['id' => $id]);
  }

  /**
   * Delete a category
   *
   * @param integer $id Category ID
   */
  public static function delete($id)
  {
    Manager::delete('categories', ['id' => $id]);
  }

  /**
   * Get the value of id
   *
   * @return integer Category ID
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * Get the value of name
   *
   * @return string Category name
   */
  public function getName()
  {
    return $this->name;
  }
}
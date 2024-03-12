<?php

namespace Model;

use \Core\Database\Manager;
use \Model\Media;

/**
 * Categories model | Handles all actions related to categories
 */
class Categories
{
  private $id;
  private $name;
  private $description;
  private $image;

  /**
   * Constructor for the Categories model
   *
   * @param integer $id Category ID
   * @param string $name Category name
   * @param string $description Category description
   * @param int $image Category image id
   */
  public function __construct(
    $id,
    $name,
    $description,
    $image
  ) {
    $this->id = $id;
    $this->name = $name;
    $this->description = $description;
    $this->image = $image;
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
        $category['name'],
        $category['description'],
        $category['image']
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
      $category['name'],
      $category['description'],
      $category['image']
    );
  }

  /**
   * Get the articles of a category
   *
   * @param integer $id Category ID
   * @return array Array of articles
   */
  public function getArticles()
  {
    return Articles::getAllPublishedArticles($this->id);
  }

  /**
   * Create a category
   *
   * @param string $name Category name
   * @param string $description Category description
   * @param int $image Category image id
   */
  public static function create($name, $description, $image)
  {
    Manager::create('categories', ['name' => $name, 'description' => $description, 'image' => $image]);
  }

  /**
   * Update a category
   *
   * @param integer $id Category ID
   * @param string $name Category name
   * @param string $description Category description
   * @param int $image Category image id
   */
  public static function update($id, $name, $description, $image)
  {
    Manager::update('categories', ['name' => $name, 'description' => $description, 'image' => $image], ['id' => $id]);
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

  /**
   * Get the value of description
   *
   * @return string Category description
   */
  public function getDescription()
  {
    return $this->description;
  }

  /**
   * Get the value of image
   *
   * @return int Category image id
   */
  public function getImageId()
  {
    return $this->image;
  }

  /**
   * Get the value of image
   *
   * @return Medias Category image
   */
  public function getImage()
  {
    return Medias::getMediaById($this->image);
  }
}
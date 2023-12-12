<?php

namespace Model;

use \DateTime;
use \Core\Database\Manager;
use \Model\Categories;
use \Model\Medias;
use \Model\Users;

/**
 * Articles model | Handles all actions related to articles
 */
class Articles
{
  private $id;
  private $title;
  private $description;
  private $author_id;
  private $date;
  private $image;
  private $category_id;
  private $tags;
  private $is_draft;
  private $is_published;

  /**
   * Constructor for the Articles model
   *
   * @param integer $id Article ID
   * @param string $title Article title
   * @param string $description Article description
   * @param integer $author_id Author ID
   * @param string $date Article date
   * @param ?integer $image Article image ID
   * @param integer $category_id Category ID
   * @param array $tags Article tags
   * @param boolean $is_draft Is the article a draft
   * @param boolean $is_published Is the article published
   */
  public function __construct(
    $id,
    $title,
    $description,
    $author_id,
    $date,
    $image,
    $category_id,
    $tags,
    $is_draft,
    $is_published
  ) {
    $this->id = $id;
    $this->title = $title;
    $this->description = $description;
    $this->author_id = $author_id;
    $this->date = new DateTime($date);
    $this->image = $image;
    $this->category_id = $category_id;
    $this->tags = $tags;
    $this->is_draft = $is_draft;
    $this->is_published = $is_published;
  }

  /**
   * Get all published articles from the database
   *
   * @return array Array of articles
   */
  public static function getAllPublishedArticles()
  {
    $conditions = ['published' => 1, 'draft' => 0];
    $category_id ? $conditions = array_merge($conditions, ['category_id' => $category_id]) : null;
    $articles = Manager::read('articles', [], $conditions);

    $result = [];

    foreach ($articles as $key => $article) {
      $result[] = new self(
        $article['id'],
        $article['title'],
        $article['description'],
        $article['author_id'],
        $article['date'],
        $article['image'],
        $article['category_id'],
        explode(",", $article['tags']),
        $article['draft'],
        $article['published']
      );
    }

    return $result;
  }

  /**
   * Get all articles from the database
   *
   * @return array Array of articles
   */
  public static function getAllArticles()
  {
    $articles = Manager::read('articles');
    $result = [];

    foreach ($articles as $key => $article) {
      $result[] = new self(
        $article['id'],
        $article['title'],
        $article['description'],
        $article['author_id'],
        $article['date'],
        $article['image'],
        $article['category_id'],
        explode(",", $article['tags']),
        $article['draft'],
        $article['published']
      );
    }

    return $result;
  }

  /**
   * Get an article from the database by its ID
   *
   * @param integer $id Article ID
   * @return self Articles
   */
  public static function getArticle($id)
  {
    $article = Manager::read('articles', [], ['id' => $id])[0];

    return new self(
      $article['id'],
      $article['title'],
      $article['description'],
      $article['author_id'],
      $article['date'],
      $article['image'],
      $article['category_id'],
      explode(",", $article['tags']),
      $article['draft'],
      $article['published']
    );
  }

  /**
   * Create a new article in the database
   *
   * @param string $title Article title
   * @param string $description Article description
   * @param integer $author_id Author ID
   * @param string $image Article image
   * @param integer $category_id Category ID
   * @param array $tags Article tags
   * @param boolean $is_draft Is the article a draft
   * @param boolean $is_published Is the article published
   */
  public static function create(
    $title,
    $description,
    $author_id,
    $image,
    $category_id,
    $tags,
    $is_draft,
    $is_published
  ) {
    Manager::create('articles', [
      'title' => $title,
      'description' => $description,
      'author_id' => $author_id,
      'date' => date("Y-m-d H:i:s"),
      'image' => $image,
      'category_id' => $category_id,
      'tags' => implode(",", $tags),
      'draft' => $is_draft,
      'published' => $is_published
    ]);
  }

  /**
   * Update an article in the database
   *
   * @param integer $id Article ID
   * @param string $title Article title
   * @param string $description Article description
   * @param integer $author_id Author ID
   * @param string $image Article image
   * @param integer $category_id Category ID
   * @param array $tags Article tags
   * @param boolean $is_draft Is the article a draft
   * @param boolean $is_published Is the article published
   */
  public static function update(
    $id,
    $title,
    $description,
    $author_id,
    $image,
    $category_id,
    $tags,
    $is_draft,
    $is_published
  ) {
    Manager::update('articles', [
      'title' => $title,
      'description' => $description,
      'author_id' => $author_id,
      'date' => date("Y-m-d H:i:s"),
      'image' => $image,
      'category_id' => $category_id,
      'tags' => implode(",", $tags),
      'draft' => $is_draft,
      'published' => $is_published
    ], ['id' => $id]);
  }

  /**
   * Delete an article from the database
   *
   * @param integer $id Article ID
   */
  public static function delete($id)
  {
    Manager::delete('articles', ['id' => $id]);
  }

  /**
   * Get the value of id
   *
   * @return integer Article ID
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * Get the value of title
   *
   * @return string Article title
   */
  public function getTitle()
  {
    return $this->title;
  }

  /**
   * Get the value of description
   *
   * @return string Article description
   */
  public function getDescription()
  {
    return $this->description;
  }

  /**
   * Get the value of author_id
   *
   * @return integer Author ID
   */
  public function getAuthorId()
  {
    return $this->author_id;
  }

  /**
   * Get the author directly
   *
   * @return Users Author object
   */
  public function getAuthor()
  {
    return Users::getUser($this->author_id);
  }

  /**
   * Get the value of date
   *
   * @return DateTime Article date
   */
  public function getDate()
  {
    return $this->date;
  }

  /**
   * Get the id value of image
   *
   * @return int|null Article image
   */
  public function getImageId()
  {
    return $this->image;
  }

  /**
   * Get the image directly
   *
   * @return Medias|null Image object
   */
  public function getImage()
  {
    if ($this->image === null) {
      return null;
    }

    return Medias::getMediaById($this->image);
  }

  /**
   * Get the value of category_id
   *
   * @return integer Category ID
   */
  public function getCategoryId()
  {
    return $this->category_id;
  }

  /**
   * Get the category directly
   *
   * @return Categories Category object
   */
  public function getCategory()
  {
    return Categories::getCategoryById($this->category_id);
  }

  /**
   * Get the value of tags
   *
   * @return array Article tags
   */
  public function getTags()
  {
    return $this->tags;
  }

  /**
   * Get the value of is_draft
   *
   * @return boolean Is the article a draft
   */
  public function isDraft()
  {
    return $this->is_draft;
  }

  /**
   * Get the value of is_published
   *
   * @return boolean Is the article published
   */
  public function isPublished()
  {
    return $this->is_published;
  }
}

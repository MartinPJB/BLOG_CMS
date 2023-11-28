<?php

namespace Model;

use \Core\Database\Manager;
use \Model\Articles;

/**
 * Blocks model | Handles all actions related to blocks
 */
class Blocks
{
  private int $id;
  private string $name;
  private mixed $json_content;
  private int $article_id;
  private string $type;
  private int $weight;

  /**
   * Constructor for the Blocks model
   *
   * @param integer $id Block ID
   * @param string $name Block name
   * @param mixed $json_content Block content
   * @param integer $article_id Article ID
   * @param string $type Block type
   * @param integer $weight Block weight
   */
  public function __construct(
    int $id,
    string $name,
    mixed $json_content,
    int $article_id,
    string $type,
    int $weight
  ) {
    $this->id = $id;
    $this->name = $name;
    $this->json_content = json_decode($json_content);
    $this->article_id = $article_id;
    $this->type = $type;
    $this->weight = $weight;
  }

  /**
   * Get all blocks created in the database no matter the article
   *
   * @return array Array of Blocks objects
   */
  public static function getAllBlocks(): array
  {
    $blocks = Manager::read('blocks');
    $result = [];

    foreach ($blocks as $block) {
      $result[] = new self(
        $block['id'],
        $block['name'],
        $block['json_content'],
        $block['article_id'],
        $block['type'],
        $block['weight']
      );
    }

    return $result;
  }

  /**
   * Get all blocks created in the database for a specific article
   *
   * @param integer $article_id Article ID
   * @return array Array of Blocks objects
   */
  public static function getBlocksByArticle(int $article_id): array
  {
    $blocks = Manager::read('blocks', [], ['article_id' => $article_id]);
    $result = [];

    foreach ($blocks as $block) {
      $result[] = new self(
        $block['id'],
        $block['name'],
        $block['json_content'],
        $block['article_id'],
        $block['type'],
        $block['weight']
      );
    }

    return $result;
  }

  /**
   * Get a block by its ID
   *
   * @param integer $id Block ID
   * @return Blocks Block object
   */
  public static function getBlock(int $id): Blocks
  {
    $block = Manager::read('blocks', [], ['id' => $id])[0];

    return new self(
      $block['id'],
      $block['name'],
      $block['json_content'],
      $block['article_id'],
      $block['type'],
      $block['weight']
    );
  }

  /**
   * Create a new block in the database
   *
   * @param string $name Block name
   * @param mixed $json_content Block content
   * @param integer $article_id Article ID
   * @param string $type Block type
   * @param integer $weight Block weight
   */
  public static function createBlock(
    string $name,
    mixed $json_content,
    int $article_id,
    string $type,
    int $weight
  ): void {
    Manager::create('blocks', [
      'name' => $name,
      'json_content' => $json_content,
      'article_id' => $article_id,
      'type' => $type,
      'weight' => $weight
    ]);
  }

  /**
   * Update a block in the database
   *
   * @param integer $id Block ID
   * @param string $name Block name
   * @param mixed $json_content Block content
   * @param integer $article_id Article ID
   * @param string $type Block type
   * @param integer $weight Block weight
   */
  public static function updateBlock(
    int $id,
    string $name,
    mixed $json_content,
    int $article_id,
    string $type,
    int $weight
  ): void {
    Manager::update('blocks', [
      'name' => $name,
      'json_content' => $json_content,
      'article_id' => $article_id,
      'type' => $type,
      'weight' => $weight
    ], ['id' => $id]);
  }

  /**
   * Delete a block from the database
   *
   * @param integer $id Block ID
   */
  public static function deleteBlock(int $id): void
  {
    Manager::delete('blocks', ['id' => $id]);
  }

  /**
   * Get the value of id
   *
   * @return integer Block ID
   */
  public function getId(): int
  {
    return $this->id;
  }

  /**
   * Get the value of name
   *
   * @return string Block name
   */
  public function getName(): string
  {
    return $this->name;
  }

  /**
   * Get the value of json_content
   *
   * @return mixed Block content (JSON decoded)
   */
  public function getJsonContent(): mixed
  {
    return $this->json_content;
  }

  /**
   * Get the value of article_id
   *
   * @return integer Article ID
   */
  public function getArticleId(): int
  {
    return $this->article_id;
  }

  /**
   * Get the article directly
   *
   * @return Articles Article object
   */
  public function getArticle(): Articles
  {
    return Articles::getArticle($this->article_id);
  }

  /**
   * Get the value of type
   *
   * @return string Block type
   */
  public function getType(): string
  {
    return $this->type;
  }

  /**
   * Get the value of weight
   *
   * @return integer Block weight
   */
  public function getWeight(): int
  {
    return $this->weight;
  }
}
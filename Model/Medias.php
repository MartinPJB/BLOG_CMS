<?php

namespace Model;

use \DateTime;
use \Core\Database\Manager;

/**
 * Medias model | Handles all actions related to medias
 */
class Medias
{
  private int $id;
  private string $name;
  private string $type;
  private int $size;
  private string $path;
  private string $alt;
  private DateTime $uploaded_at;

  /**
   * Constructor for the Medias model
   *
   * @param integer $id Media ID
   * @param string $name Media name
   * @param string $type Media type
   * @param integer $size Media size
   * @param string $path Media path
   * @param string $alt Media alt
   * @param string $uploaded_at Media uploaded_at
   */
  public function __construct(
    int $id,
    string $name,
    string $type,
    int $size,
    string $path,
    string $alt,
    string $uploaded_at
  ) {
    $this->id = $id;
    $this->name = $name;
    $this->type = $type;
    $this->size = $size;
    $this->path = $path;
    $this->alt = $alt;
    $this->uploaded_at = new DateTime($uploaded_at);
  }

  /**
   * Get all medias from the database
   *
   * @return array Array of medias
   */
  public static function getAllMedias(): array
  {
    $medias = Manager::read('media');
    $result = [];

    foreach ($medias as $key => $media) {
      $result[] = new self(
        $media['id'],
        $media['name'],
        $media['type'],
        $media['size'],
        $media['path'],
        $media['alt'],
        $media['uploaded_at']
      );
    }

    return $result;
  }

  /**
   * Get a media by its ID
   *
   * @param integer $id Media ID
   * @return self Media
   */
  public static function getMediaById(int $id): self
  {
    $media = Manager::read('media', [], ['id' => $id])[0];

    return new self(
      $media['id'],
      $media['name'],
      $media['type'],
      $media['size'],
      $media['path'],
      $media['alt'],
      $media['uploaded_at']
    );
  }

  /**
   * Get a media by its type
   *
   * @param string $type Media type
   * @return self Media
   */
  public static function getMediaByType(string $type): self
  {
    $media = Manager::read('media', [], ['type' => $type])[0];

    return new self(
      $media['id'],
      $media['name'],
      $media['type'],
      $media['size'],
      $media['path'],
      $media['alt'],
      $media['uploaded_at']
    );
  }

  /**
   * Create a media
   *
   * @param string $name Media name
   * @param string $type Media type
   * @param integer $size Media size
   * @param string $path Media path
   * @param string $alt Media alt
   * @param string $uploaded_at Media uploaded_at
   */
  public static function create(
    string $name,
    string $type,
    int $size,
    string $path,
    string $alt,
    string $uploaded_at
  ): void {
    Manager::create('media', [
      'name' => $name,
      'type' => $type,
      'size' => $size,
      'path' => $path,
      'alt' => $alt,
      'uploaded_at' => $uploaded_at
    ]);
  }

  /**
   * Update a media
   *
   * @param integer $id Media ID
   * @param string $name Media name
   * @param string $type Media type
   * @param integer $size Media size
   * @param string $path Media path
   * @param string $alt Media alt
   * @param string $uploaded_at Media uploaded_at
   */
  public static function update(
    int $id,
    string $name,
    string $type,
    int $size,
    string $path,
    string $alt,
    string $uploaded_at
  ): void {
    Manager::update('media', [
      'name' => $name,
      'type' => $type,
      'size' => $size,
      'path' => $path,
      'alt' => $alt,
      'uploaded_at' => $uploaded_at
    ], ['id' => $id]);
  }

  /**
   * Delete a media
   *
   * @param integer $id Media ID
   */
  public static function delete(int $id): void
  {
    Manager::delete('media', ['id' => $id]);
  }

  /**
   * Get the value of id
   *
   * @return integer Media ID
   */
  public function getId(): int
  {
    return $this->id;
  }

  /**
   * Get the value of name
   *
   * @return string Media name
   */
  public function getName(): string
  {
    return $this->name;
  }

  /**
   * Get the value of type
   *
   * @return string Media type
   */
  public function getType(): string
  {
    return $this->type;
  }

  /**
   * Get the value of size
   *
   * @return integer Media size
   */
  public function getSize(): int
  {
    return $this->size;
  }

  /**
   * Get the value of path
   *
   * @return string Media path
   */
  public function getPath(): string
  {
    return $this->path;
  }

  /**
   * Get the value of alt
   *
   * @return string Media alt
   */
  public function getAlt(): string
  {
    return $this->alt;
  }

  /**
   * Get the value of uploaded_at
   *
   * @return DateTime Media uploaded_at
   */
  public function getUploadedAt(): DateTime
  {
    return $this->uploaded_at;
  }
}
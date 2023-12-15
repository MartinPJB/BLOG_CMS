<?php

namespace Model;

use \DateTime;
use \Core\Database\Manager;
use \JsonSerializable;

/**
 * Medias model | Handles all actions related to medias
 */
class Medias implements JsonSerializable
{
  private $id;
  private $name;
  private $type;
  private $size;
  private $path;
  private $alt;
  private $uploaded_at;
  private $hash;

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
   * @param string $hash Media hash
   */
  public function __construct(
    $id,
    $name,
    $type,
    $size,
    $path,
    $alt,
    $uploaded_at,
    $hash
  ) {
    $this->id = $id;
    $this->name = $name;
    $this->type = $type;
    $this->size = $size;
    $this->path = $path;
    $this->alt = $alt;
    $this->uploaded_at = new DateTime($uploaded_at);
    $this->hash = $hash;
  }

  /**
   * Get all medias from the database
   *
   * @return array Array of medias
   */
  public static function getAllMedias()
  {
    $medias = Manager::read('medias');
    $result = [];

    foreach ($medias as $key => $media) {
      $result[] = new self(
        $media['id'],
        $media['name'],
        $media['type'],
        $media['size'],
        $media['path'],
        $media['alt'],
        $media['uploaded_at'],
        $media['hash']
      );
    }

    return $result;
  }

  /**
   * Get a media by its ID
   *
   * @param integer $id Media ID
   * @return self|null Media
   */
  public static function getMediaById($id)
  {
    $media = Manager::read('medias', [], ['id' => $id]);
    if (empty($media)) return null;
    $media = $media[0];

    return new self(
      $media['id'],
      $media['name'],
      $media['type'],
      $media['size'],
      $media['path'],
      $media['alt'],
      $media['uploaded_at'],
      $media['hash']
    );
  }

  /**
   * Get all medias by type
   *
   * @param string $type Media type
   * @return array Media
   */
  public static function getMediasByType($type)
  {
    $medias = Manager::read('medias', [], ['type' => $type])[0];
    $result = [];

    foreach ($medias as $key => $media) {
      $result[] = new self(
        $media['id'],
        $media['name'],
        $media['type'],
        $media['size'],
        $media['path'],
        $media['alt'],
        $media['uploaded_at'],
        $media['hash']
      );
    }

    return $result;
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
   *
   * @return self Media
   */
  public static function create(
    $name,
    $type,
    $size,
    $path,
    $alt,
    $uploaded_at,
    $hash
  ) {
    Manager::create('medias', [
      'name' => $name,
      'type' => $type,
      'size' => $size,
      'path' => $path,
      'alt' => $alt,
      'uploaded_at' => $uploaded_at,
      'hash' => $hash
    ]);

    return new self(
      Manager::getLastInsertedId(),
      $name,
      $type,
      $size,
      $path,
      $alt,
      $uploaded_at,
      $hash
    );
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
   * @param string $hash Media hash
   */
  public static function update(
    $id,
    $name,
    $type,
    $size,
    $path,
    $alt,
    $uploaded_at,
    $hash
  ) {
    Manager::update('medias', [
      'name' => $name,
      'type' => $type,
      'size' => $size,
      'path' => $path,
      'alt' => $alt,
      'uploaded_at' => $uploaded_at,
      'hash' => $hash
    ], ['id' => $id]);
  }

  /**
   * Delete a media
   *
   * @param integer $id Media ID
   */
  public static function delete($id)
  {
    Manager::delete('medias', ['id' => $id]);
  }

  /**
   * Unassign a media from a table
   *
   * @param string $table Table name
   * @param string $column Column name
   * @param integer $id Media ID
   */
  public function unassignFrom($table, $column, $id)
  {
    Manager::update($table, [$column => null], ['id' => $id]);
  }

  /**
   * Get the value of id
   *
   * @return integer Media ID
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * Get the value of name
   *
   * @return string Media name
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * Get the value of type
   *
   * @return string Media type
   */
  public function getType()
  {
    return $this->type;
  }

  /**
   * Get the value of size
   *
   * @return integer Media size
   */
  public function getSize()
  {
    return $this->size;
  }

  /**
   * Get the value of path
   *
   * @return string Media path
   */
  public function getPath()
  {
    return $this->path;
  }

  /**
   * Get the value of alt
   *
   * @return string Media alt
   */
  public function getAlt()
  {
    return $this->alt;
  }

  /**
   * Get the value of uploaded_at
   *
   * @return DateTime Media uploaded_at
   */
  public function getUploadedAt()
  {
    return $this->uploaded_at;
  }

  /**
   * Get the value of hash
   *
   * @return string Media hash
   */
  public function getHash()
  {
    return $this->hash;
  }

  /**
   * JSON serialize method
   *
   * @return array Media
   */
  public function jsonSerialize()
  {
    // Convert private properties to an array
    return [
      'id' => $this->id,
      'name' => $this->name,
      'type' => $this->type,
      'size' => $this->size,
      'path' => $this->path,
      'alt' => $this->alt,
      'uploaded_at' => $this->uploaded_at->format('Y-m-d H:i:s'),
      'hash' => $this->hash,
    ];
  }
}

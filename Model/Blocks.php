<?php

namespace Model;

use \Core\Database\Manager;
use \Model\Articles;
use \Model\SiteSettings;
use \Model\Medias;

/**
 * Blocks model | Handles all actions related to blocks
 */
class Blocks
{
  private $id;
  private $name;
  private $json_content;
  private $article_id;
  private $type;
  private $weight;
  private $media;

  /**
   * Constructor for the Blocks model
   *
   * @param integer $id Block ID
   * @param string $name Block name
   * @param mixed $json_content Block content
   * @param integer $article_id Article ID
   * @param string $type Block type
   * @param integer $weight Block weight
   * @param integer $media Media ID
   */
  public function __construct(
    $id,
    $name,
    $json_content,
    $article_id,
    $type,
    $weight,
    $media
  ) {
    $this->id = $id;
    $this->name = $name;
    $this->json_content = json_decode($json_content);
    $this->article_id = $article_id;
    $this->type = $type;
    $this->weight = $weight;
    $this->media = $media;
  }

  /**
   * Get all blocks created in the database no matter the article
   *
   * @return array Array of Blocks objects
   */
  public static function getAllBlocks()
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
        $block['weight'],
        $block['media']
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
  public static function getBlocksByArticle($article_id)
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
        $block['weight'],
        $block['media']
      );
    }

    // Order result by weight
    usort($result, function ($a, $b) {
      return $a->getWeight() - $b->getWeight();
    });

    return $result;
  }

  /**
   * Get all available blocks by reading the templates folder
   *
   * @return array Array of blocks name
   */
  public static function getAvailableBlocks()
  {
    $siteSettings = SiteSettings::getSiteSettings();
    $location = dirname(__DIR__) . '/Themes/' . $siteSettings->getTheme() . '/Front/templates/blocks/';
    $blocks = scandir($location);
    $result = [];

    foreach ($blocks as $block) {
      if ($block !== '.' && $block !== '..') {
        $withoutExtension = explode('.', $block)[0];
        $content = file_get_contents($location . $block);
        $result[] = [
          'name' => $withoutExtension,
          'fields' => self::extractFieldsFromTwig($content)
        ];
      }
    }

    return $result;
  }

  private static function extractFieldsFromTwig($content)
  {
    $matches = [];
    preg_match_all('/\{\#(.+?)\#\}/s', $content, $matches);

    $fields = [];

    if (!empty($matches[1])) {
      foreach ($matches[1] as $match) {
        $fieldLines = explode("\n", $match);

        foreach ($fieldLines as $line) {
          $line = trim($line);

          // Check if the line contains a field definition
          if (strpos($line, 'Fields:') !== false) {
            continue; // Skip the "Fields:" line
          }

          // Extract field name, type, and attributes
          if (preg_match('/- (\w+) \((\w+)\): \{(.+?)\}/', $line, $fieldMatch)) {
            $name = $fieldMatch[1];
            $type = $fieldMatch[2];
            $attributes = $fieldMatch[3];

            // Parse attributes (and remove spaces at the beginning and end)
            $attributeParts = preg_split('/,\s*(?=(?:(?:[^\'"]*[\'"]){2})*[^\'"]*$)/', $attributes);
            foreach ($attributeParts as &$part) {
              $part = trim($part, " \t\n\r\0\x0B'\"");
            }

            $fieldOptions = [];
            foreach ($attributeParts as $attribute) {
              // Split attribute into key and value
              list($key, $value) = explode(':', $attribute, 2);

              // Remove spaces at the beginning and end of key and value
              $key = trim($key);
              $value = trim($value, " \t\n\r\0\x0B'\"");

              // Handles if the value is a number
              if (is_numeric($value)) {
                $value = (int)$value;
              }

              // Add to fieldOptions array
              $fieldOptions[$key] = $value;
            }

            $fields[$name] = array_merge(['type' => $type], $fieldOptions);
          }
        }
      }
    }

    return $fields;
  }


  /**
   * Get a block by its ID
   *
   * @param integer $id Block ID
   * @return Blocks Block object
   */
  public static function getBlock($id)
  {
    $block = Manager::read('blocks', [], ['id' => $id])[0];

    return new self(
      $block['id'],
      $block['name'],
      $block['json_content'],
      $block['article_id'],
      $block['type'],
      $block['weight'],
      $block['media']
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
  public static function create(
    $name,
    $json_content,
    $article_id,
    $type,
    $weight,
    $media
  ) {
    Manager::create('blocks', [
      'name' => $name,
      'json_content' => json_encode($json_content),
      'article_id' => $article_id,
      'type' => $type,
      'weight' => $weight,
      'media' => $media
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
  public static function update(
    $id,
    $name,
    $json_content,
    $article_id,
    $type,
    $weight,
    $media
  ) {
    Manager::update('blocks', [
      'name' => $name,
      'json_content' => json_encode($json_content),
      'article_id' => $article_id,
      'type' => $type,
      'weight' => $weight,
      'media' => $media
    ], ['id' => $id]);
  }

  /**
   * Delete a block from the database
   *
   * @param integer $id Block ID
   */
  public static function delete($id)
  {
    Manager::delete('blocks', ['id' => $id]);
  }

  /**
   * Get the value of id
   *
   * @return integer Block ID
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * Get the value of name
   *
   * @return string Block name
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * Get the value of json_content
   *
   * @return mixed Block content (JSON decoded)
   */
  public function getJsonContent()
  {
    return json_decode($this->json_content);
  }

    /**
   * Parses the string to reverse the parseStringPublish method
   *
   * @param string $field The field to parse
   * @return string The parsed field
   */
  protected function reverseParseString($field) {
    // Replaces html codes by their corresponding characters
    $field = html_entity_decode($field);

    // Replaces non breaking spaces by double percent signs
    $field = str_replace('&amp;nbsp;', '%%', $field);
    $field = str_replace('&nbsp;', '%%', $field);

    // Replaces french quotes (« ») by english quotes (" ") (First quote is opening, second is closing) RegEx
    $field = str_replace('&amp;quot;', '"', $field);
    $field = str_replace('&quot;', '"', $field);

    // Replaces <i></i> by underscores
    $field = str_replace('<i>', '__', $field);
    $field = str_replace('</i>', '__', $field);
    $field = str_replace('&lt;i&gt;', '__', $field);
    $field = str_replace('&lt;/i&gt;', '__', $field);

    // Replaces quotes html entities by quotes
    $field = str_replace('&amp;#039;', "'", $field);
    $field = str_replace('&#039;', "'", $field);

    // Replaces <br> by double ~
    $field = str_replace('<br>', '~~', $field);
    $field = str_replace('&lt;br&gt;', '~~', $field);

    return $field;
  }

  /**
   * Get the value of json_content as a string
   *
   * @return mixed Block content (JSON encoded)
   */
  public function getJsonContentString()
  {
    $fieldUnparse = json_decode($this->json_content);
    $fieldUnparse = (array)$fieldUnparse;

    foreach ($fieldUnparse as $key => $value) {
      if (is_string($value)) {
        $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        $fieldUnparse[$key] = $this->reverseParseString($value);
      }
    }

    $fieldUnparse = json_encode($fieldUnparse);
    return (string)$fieldUnparse;
  }

  /**
   * Get the value of article_id
   *
   * @return integer Article ID
   */
  public function getArticleId()
  {
    return $this->article_id;
  }

  /**
   * Get the article directly
   *
   * @return Articles Article object
   */
  public function getArticle()
  {
    return Articles::getArticle($this->article_id);
  }

  /**
   * Get the value of type
   *
   * @return string Block type
   */
  public function getType()
  {
    return $this->type;
  }

  /**
   * Get the value of weight
   *
   * @return integer Block weight
   */
  public function getWeight()
  {
    return $this->weight;
  }

  /**
   * Get the value of media
   *
   * @return Medias Media object
   */
  public function getMedia()
  {
    return Medias::getMediaById($this->media);
  }

  /**
   * Get the value of media_id
   *
   * @return integer Media ID
   */
  public function getMediaId()
  {
    return $this->media;
  }
}

<?php

namespace Model;

use finfo;
use \Core\Config;

/**
 * Public model | Handles all actions related to public pages
 */
class Files
{
  /**
   * Search for a file in the public desired directory of the theme
   *
   * @param string $directory Directory to search in (Front or Back)
   * @param string $file_name File name to search for
   * @param string $frontOrBack Front or back office ('Front' or 'Back', default: Front)
   * @return ?string File path if found, null otherwise
   */
  public static function findFile(string $directory, string $file_name): ?string
  {
    $directory = rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    if (!is_dir($directory)) {
      return null;
    }

    $file_path = $directory . $file_name;

    return file_exists($file_path) ? $file_path : null;
  }


  /**
   * Get the content to the desired file
   *
   * @param string $path File path
   * @return ?array File details if found, null otherwise
   */
  public static function getFileContent(string $path): ?array
  {
    if (!$path || is_dir($path)) {
      return null;
    }

    $file_infos = finfo_open(FILEINFO_MIME_TYPE);
    $file_type = finfo_file($file_infos, $path);
    finfo_close($file_infos);

    $file_content = file_get_contents($path);
    if (is_null($file_content) || empty($file_type)) {
      return null;
    }

    $file_type = $file_type ?: 'text/plain';

    return [
      'content' => $file_content,
      'type' => $file_type
    ];
  }
}

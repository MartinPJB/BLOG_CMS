<?php

namespace Model;

use finfo;
use \Model\SiteSettings;
use \Core\Config;

/**
 * Public model | Handles all actions related to public pages
 */
class PublicFiles
{
  /**
   * Search for a file in the public directory of the theme
   *
   * @param string $directory Directory to search in (Front or Back)
   * @param string $file_name File name to search for
   * @param string $frontOrBack Front or back office ('Front' or 'Back', default: Front)
   * @return ?string File path if found, null otherwise
   */
  public static function findFileRecursively(string $directory, string $file_name): ?string
  {
    $theme = SiteSettings::getSiteSettings()->getTheme();
    $parent_directory = dirname(__DIR__) . '/Themes/';
    $public_directory = $parent_directory . "$theme/$directory/public/";

    // Check if directory exists
    if (!is_dir($public_directory)) return null;

    $iterator = new \RecursiveIteratorIterator(
      new \RecursiveDirectoryIterator($public_directory),
      \RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($iterator as $file) {
      if ($file->isFile() && $file->getFilename() === $file_name) {
        return $file->getPathname();
      }
    }

    return null;
  }

  /**
   * Get the content to the desired file
   *
   * @param string $path File path
   * @return ?array File details if found, null otherwise
   */
  public static function getFileContent(string $path): ?array
  {
    if (!$path) return null;

    $file_infos = finfo_open(FILEINFO_MIME_TYPE);
    $file_type = finfo_file($file_infos, $path);
    finfo_close($file_infos);

    $file_content = file_get_contents($path);

    if (empty($file_type)) return null;
    if (empty($file_content)) $file_type = 'text/plain';

    return [
      'content' => $file_content ?? '',
      'type' => $file_type
    ];
  }
}

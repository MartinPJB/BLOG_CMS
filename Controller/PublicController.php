<?php

/**
 * @file PublicController.php
 * @brief Controller un peu spécial car celui-ci nous permet de récupérer les fichiers publics (css, js, images, etc.) en intégrant directement les thèmes
 */

namespace Controller;

use Controller\ControllerBase;
use Controller\ControllerInterface;
use Model\Base;
use Includes\TypeEscaper;

class PublicController extends ControllerBase implements ControllerInterface
{
  const ACTION_CSS = 'css';
  const ACTION_JS = 'js';
  const ACTION_IMG = 'img';
  const ACTION_AUDIO = 'audio';
  const ACTION_VIDEO = 'video';

  private Base $model;
  private string $theme;
  private string $themeFolder = 'Themes/';

  private bool $admin_only = false;

  /**
   * @inheritDoc
   */
  public function __construct(array $database_credentials, $twig)
  {
    parent::__construct('public', $twig, $this->admin_only);
    $this->model = new Base($database_credentials, 'site_settings');
    $this->theme = $this->model->readElement(['theme'])[0]['theme'];
    $this->themeFolder .= "{$this->theme}/public/";
    $this->initializeSubRoutes();
  }

  /**
   * @inheritDoc
   */
  public function initializeSubRoutes(): void
  {
    // Add GET routes
    $this->addSubRoute(self::ACTION_CSS, 'content.html.twig', [$this, 'GET_css'], 'GET', 0);
    $this->addSubRoute(self::ACTION_JS, 'content.html.twig', [$this, 'GET_js'], 'GET', 0);
    $this->addSubRoute(self::ACTION_IMG, 'content.html.twig', [$this, 'GET_img'], 'GET', 0);
    $this->addSubRoute(self::ACTION_AUDIO, 'content.html.twig', [$this, 'GET_audio'], 'GET', 0);
    $this->addSubRoute(self::ACTION_VIDEO, 'content.html.twig', [$this, 'GET_video'], 'GET', 0);
  }

  /**
   * @return array Cherche un fichier dans le dossier du thème et le retourne
   *
   * @param string $directory Le dossier dans lequel chercher
   * @param string $filename Le nom du fichier à chercher
   * @return string|null Retourne le chemin du fichier si trouvé, sinon null
   */
  public function findFileRecursively(string $directory, string $filename): ?string
  {
    $iterator = new \RecursiveIteratorIterator(
      new \RecursiveDirectoryIterator($directory),
      \RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($iterator as $file) {
      if ($file->isFile() && $file->getFilename() === $filename) {
        return $file->getPathname();
      }
    }

    return null;
  }

  /**
   * @return array Retourne un array contenant le contenu du fichier CSS demandé
   */
  public function GET_css(): array
  {
    return $this->displayFileContent($_GET['id'], 'css/');
  }

  public function GET_js(): array
  {
    return $this->displayFileContent($_GET['id'], 'js/');
  }

  public function GET_img(): array
  {
    return $this->displayFileContent($_GET['id'], 'img/');
  }

  public function GET_audio(): array
  {
    return $this->displayFileContent($_GET['id'], 'audio/');
  }

  public function GET_video(): array
  {
    return $this->displayFileContent($_GET['id'], 'video/');
  }

  /**
   * Permet d'afficher le contenu d'un fichier
   *
   * @param string $filename Le nom du fichier à afficher
   * @param string $subfolder Le sous-dossier dans lequel chercher
   * @return array Retourne un array contenant le contenu du fichier demandé
   */
  private function displayFileContent(string $filename, string $subfolder)
  {
    if (!isset($filename) || empty($filename)) {
      return [];
    }

    $filename = TypeEscaper::escapeString($filename);
    $getFile = $this->findFileRecursively($this->themeFolder . $subfolder . '/', $filename);

    if (!$getFile || !file_exists($getFile)) {
      return [];
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $getFile);
    finfo_close($finfo);

    if (empty($mime_type)) {
      return [];
    }

    $content = file_get_contents($getFile);

    if ($content === false) {
      return [];
    }

    header("Content-type: $mime_type");
    echo $content;

    return [
      'content' => $content,
    ];
  }
}

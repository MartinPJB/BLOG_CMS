<?php

/*
  This Controller works a bit differently than the others.
  It's not a Controller for a specific Model, but rather a Controller for a specific directory.
  It's used to display files from the theme's public directory (Front or Back office).
*/

namespace Controller;

use \Core\Controller\ControllerInterface;
use \Core\Controller\ControllerBase;
use \Core\RequestContext;
use \Model\PublicFiles;

/**
 * Public controller | Handles all requests related to public files
 */
class PublicController extends ControllerBase implements ControllerInterface
{
  public string $name = 'Public';
  public string $description = 'Handles all requests related to public files.';

  /**
   * {@inheritDoc}
   */
  public function __construct(RequestContext $requestContext)
  {
    parent::__construct($requestContext);
  }

  /**
   * {@inheritDoc}
   */
  public function index(array $params): void
  {
    $this->redirect('articles');
  }

  /**
   * Get a public file from the theme Front or Back directory
   *
   * @param string $directory The directory to search in (Front or Back)
   * @param string $file_name The file name to search for
   * @return ?array File details if found, null otherwise
   */
  private function getFileContent(string $directory, string $file_name): ?array
  {
    $file_path = PublicFiles::findFileRecursively($directory, $file_name);

    if (!$file_path) return null;

    $file_content = PublicFiles::getFileContent($file_path);

    if (!$file_content) $file_content['content'] = '';

    return [
      'content' => $file_content['content'],
      'type' => $file_content['type'],
    ];
  }

  /**
   * Get a public file from the theme Front directory
   *
   * @param string $directory The directory to search in (Front or Back)
   */
  public function displayFileContent($directory): void
  {
    $file_name = $this->requestContext->id;
    if (!$file_name) {
      header('HTTP/1.0 404 Not Found');
      return;
    }

    // Get the file content
    $file_content = $this->getFileContent($directory, $file_name);

    // No need for templates!
    if (!$file_content) {
      header('HTTP/1.0 404 Not Found');
      return;
    }

    header("Content-Type: {$file_content['type']}");
    echo $file_content['content'];
  }

  /**
   * Get a public file from the theme Front directory
   */
  public function front(): void
  {
    $this->displayFileContent('Front');
  }

  /**
   * Get a public file from the theme Back directory
   */
  public function back(): void
  {
    $this->displayFileContent('Back');
  }
}
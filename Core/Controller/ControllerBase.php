<?php

namespace Core\Controller;

use \Core\Routing\RequestContext;

/**
 * Base controller class
 */
class ControllerBase
{
  protected mixed $twigEngine;
  protected RequestContext $requestContext;

  /**
   * Constructor
   *
   * @param string $themeName = 'Front/default' Theme name (folder name in /Themes/)
   * @return void
   */
  protected function __construct(RequestContext $requestContext, string $themeName = 'Front/default')
  {
    $this->requestContext = $requestContext;
    $this->twigEngine = $this->initializeTwig(__DIR__ . '/../..', $themeName);
  }

  /**
   * Initialize the twig engine
   *
   * @param string $directory
   * @param string $themeName
   * @return \Twig\Environment
   */
  private function initializeTwig(string $directory, string $themeName): \Twig\Environment
  {
    $loader = new \Twig\Loader\FilesystemLoader("$directory/Themes/$themeName/templates");
    $twig = new \Twig\Environment($loader, [
      'debug' => true,
    ]);

    $twig->addExtension(new \Twig\Extension\DebugExtension());

    return $twig;
  }

  /**
   * Render a twig view
   *
   * @param string $view View (folder +) name (without the extension)
   * @param array $variables = [] Variables to pass to the view
   * @return void
   */
  protected function render(string $view, array $variables = []): void
  {
    $template = $this->twigEngine->load("$view.html.twig");
    echo $template->render($variables);
  }
}

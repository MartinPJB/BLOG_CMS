<?php

namespace Core\Controller;

use \Core\Config;
use Core\Database\Manager;
use \Core\RequestContext;

/**
 * Base controller class | All controllers must extend this class
 */
class ControllerBase
{
  protected mixed $twigEngine;
  protected RequestContext $requestContext;

  /**
   * Constructor of the ControllerBase class
   *
   * @param string $themeName = 'default/Front' Theme name (folder name in /Themes/)
   */
  protected function __construct(RequestContext $requestContext, string $themeName = 'default/Front')
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
    $loader = new \Twig\Loader\FilesystemLoader("$directory/Themes/$themeName/templates/");
    $twig = new \Twig\Environment($loader, [
      'debug' => true,
    ]);

    $twig->addExtension(new \Twig\Extension\DebugExtension());

    $site_name = Manager::read('site_settings', ['name'])[0]['name'];
    $site_description = Manager::read('site_settings', ['description'])[0]['description'];

    // Add global variables
    $twig->addGlobal('site_root', Config::get('site_root'));
    $twig->addGlobal('site_default_route', Config::get('site_default_route'));
    $twig->addGlobal('site_name', $site_name);
    $twig->addGlobal('site_description', $site_description);
    $twig->addGlobal('site_language', Config::get('site_language'));
    $twig->addGlobal('session', $_SESSION ?? []);

    return $twig;
  }

  /**
   * Render a twig view
   *
   * @param string $view View (folder +) name (without the extension)
   * @param array $variables = [] Variables to pass to the view
   */
  protected function render(string $view, array $variables = []): void
  {
    $template = $this->twigEngine->load("pages/$view.html.twig");
    echo $template->render($variables);
  }

  /**
   * Redirect to a route
   *
   * @param string $route Route to redirect to
   */
  protected function redirect(string $route): void
  {
    $redirection = Config::get('site_root') . $route;
    header("Location: $redirection");
    exit;
  }
}

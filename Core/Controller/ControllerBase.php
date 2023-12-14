<?php

namespace Core\Controller;

use \Core\Config;
use \Core\RequestContext;
use \Model\SiteSettings;

/**
 * Base controller class | All controllers must extend this class
 */
class ControllerBase
{
  protected $twigEngine;
  protected $requestContext;
  protected $siteSettings;

  /**
   * Constructor of the ControllerBase class
   *
   * @param string $themePart = '/Front' Theme part you want to use (default: Front)
   */
  protected function __construct(RequestContext $requestContext, $themePart = 'Front')
  {
    $this->siteSettings = SiteSettings::getSiteSettings();
    $this->requestContext = $requestContext;
    $this->twigEngine = $this->initializeTwig(__DIR__ . '/../..', $this->siteSettings->getTheme() . '/' . $themePart);
  }

  /**
   * Initialize the twig engine
   *
   * @param string $directory
   * @param string $themePart
   * @return \Twig\Environment|\Twig_Environment
   */
  private function initializeTwig($directory, $themePart)
  {
    $loader = new \Twig\Loader\FilesystemLoader("$directory/Themes/$themePart/templates/");
    $twig = new \Twig\Environment($loader, [
      'debug' => true,
    ]);

    $twig->addExtension(new \Twig\Extension\DebugExtension());


    // Grabs the site settings from the database
    $site_name = $this->siteSettings->getName();
    $site_description = $this->siteSettings->getDescription();
    $site_language = $this->siteSettings->getSiteLanguage();
    $site_default_route = $this->siteSettings->getDefaultRoute();

    // Add global variables
    $twig->addGlobal('site_root', Config::get('site_root'));
    $twig->addGlobal('site_default_route', $site_default_route);
    $twig->addGlobal('site_name', $site_name);
    $twig->addGlobal('site_description', $site_description);
    $twig->addGlobal('site_language', $site_language);
    $twig->addGlobal('session', isset($_SESSION) ? $_SESSION : []);

    return $twig;
  }

  /**
   * Render a twig view
   *
   * @param string $view View (folder +) name (without the extension)
   * @param array $variables = [] Variables to pass to the view
   */
  protected function render($view, $variables = [])
  {
    $template = $this->twigEngine->load("pages/$view.html.twig");
    echo $template->render($variables);
  }

  /**
   * Redirect to a route
   *
   * @param string $route Route to redirect to
   */
  protected function redirect($route)
  {
    $redirection = Config::get('site_root') . $route;
    header("Location: $redirection");
    exit;
  }

  /**
   * Redirect to an error page
   *
   * @param int $errorCode HTTP error code
   * @param RequestContext $requestContext The request context
   */
  public static function renderError($errorCode, $requestContext) {
    $variables = [
      'errorCode' => $code,
      'controllerType' => $requestContext->getRoute()
    ];

    switch ($errorCode) {
      case 404:
        header('HTTP/1.0 404 Not Found');
        break;
      case 403:
        header('HTTP/1.0 403 Forbidden');
        break;
      case 405:
        header('HTTP/1.0 405 Method Not Allowed');
        break;
      default:
        header('HTTP/1.0 500 Internal Server Error');
        break;
    }

    $controller = new self($requestContext);
    $controller->render('error', $variables);
    exit;
  }
}

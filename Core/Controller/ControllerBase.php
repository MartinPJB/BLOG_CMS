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
  protected mixed $twigEngine;
  protected RequestContext $requestContext;
  protected SiteSettings $siteSettings;

  /**
   * Constructor of the ControllerBase class
   *
   * @param string $themePart = '/Front' Theme part you want to use (default: Front)
   */
  protected function __construct(RequestContext $requestContext, string $themePart = 'Front')
  {
    $this ->siteSettings = SiteSettings::getSiteSettings();
    $this->requestContext = $requestContext;
    $this->twigEngine = $this->initializeTwig(__DIR__ . '/../..', $this->siteSettings->getTheme() . '/' . $themePart);
  }

  /**
   * Initialize the twig engine
   *
   * @param string $directory
   * @param string $themePart
   * @return \Twig\Environment
   */
  private function initializeTwig(string $directory, string $themePart): \Twig\Environment
  {
    $loader = new \Twig\Loader\FilesystemLoader("$directory/Themes/$themePart/templates/");
    $twig = new \Twig\Environment($loader, [
      'debug' => true,
    ]);

    $twig->addExtension(new \Twig\Extension\DebugExtension());

    // Grabs the site settings from the database
    $site_name = $this->siteSettings ->getName();
    $site_description = $this->siteSettings ->getDescription();
    $site_language = $this->siteSettings ->getSiteLanguage();
    $site_default_route = $this->siteSettings ->getDefaultRoute();

    // Add global variables
    $twig->addGlobal('site_root', Config::get('site_root'));
    $twig->addGlobal('site_default_route', $site_default_route);
    $twig->addGlobal('site_name', $site_name);
    $twig->addGlobal('site_description', $site_description);
    $twig->addGlobal('site_language', $site_language);
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

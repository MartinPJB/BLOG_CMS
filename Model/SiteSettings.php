<?php

namespace Model;

use \Core\Database\Manager;

/**
 * Site manager model | Handles all actions related to the site settings
 */
class SiteSettings {
  private $name;
  private $description;
  private $theme;
  private $site_language;
  private $default_route;

  /**
   * Constructor for the SiteSettings model
   *
   * @param string $name Site name
   * @param string $description Site description
   * @param string $theme Site theme
   */
  public function __construct(
    $name,
    $description,
    $theme,
    $site_language,
    $default_route
  ) {
    $this->name = $name;
    $this->description = $description;
    $this->theme = $theme;
    $this->site_language = $site_language;
    $this->default_route = $default_route;
  }

  /**
   * Get the site settings from the database
   *
   * @return SiteSettings SiteSettings object
   */
  public static function getSiteSettings() {
    $site_settings = Manager::read('site_settings')[0];

    return new self(
      $site_settings['name'],
      $site_settings['description'],
      $site_settings['theme'],
      $site_settings['site_language'],
      $site_settings['default_route']
    );
  }

  /**
   * Update the site settings in the database
   *
   * @param string $name Site name
   * @param string $description Site description
   * @param string $theme Site theme
   * @param string $site_language Site language
   * @param string $default_route Site default route
   * @return void
   */
  public static function update(
    $name,
    $description,
    $theme,
    $site_language,
    $default_route
  ) {
    Manager::update('site_settings', [
      'name' => $name,
      'description' => $description,
      'theme' => $theme,
      'site_language' => $site_language,
      'default_route' => $default_route,
    ], [
      'id' => 1
    ]);
  }

  /**
   * Get the site name
   *
   * @return string Site name
   */
  public function getName() {
    return $this->name;
  }

  /**
   * Get the site description
   *
   * @return string Site description
   */
  public function getDescription() {
    return $this->description;
  }

  /**
   * Get the site theme
   *
   * @return string Site theme
   */
  public function getTheme() {
    return $this->theme;
  }

  /**
   * Get the site language
   *
   * @return string Site language
   */
  public function getSiteLanguage() {
    return $this->site_language;
  }

  /**
   * Get the site default route
   *
   * @return string Site default route
   */
  public function getDefaultRoute() {
    return $this->default_route;
  }
}
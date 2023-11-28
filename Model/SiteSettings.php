<?php

namespace Model;

use \Core\Database\Manager;

/**
 * Site manager model | Handles all actions related to the site settings
 */
class SiteSettings {
  private string $name;
  private string $description;
  private string $theme;
  private string $site_language;
  private string $default_route;

  /**
   * Constructor for the SiteSettings model
   *
   * @param string $name Site name
   * @param string $description Site description
   * @param string $theme Site theme
   */
  public function __construct(
    string $name,
    string $description,
    string $theme,
    string $site_language,
    string $default_route
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
  public static function getSiteSettings(): SiteSettings {
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
  public static function updateSiteSettings(
    string $name,
    string $description,
    string $theme,
    string $site_language,
    string $default_route
  ): void {
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
  public function getName(): string {
    return $this->name;
  }

  /**
   * Get the site description
   *
   * @return string Site description
   */
  public function getDescription(): string {
    return $this->description;
  }

  /**
   * Get the site theme
   *
   * @return string Site theme
   */
  public function getTheme(): string {
    return $this->theme;
  }

  /**
   * Get the site language
   *
   * @return string Site language
   */
  public function getSiteLanguage(): string {
    return $this->site_language;
  }

  /**
   * Get the site default route
   *
   * @return string Site default route
   */
  public function getDefaultRoute(): string {
    return $this->default_route;
  }
}
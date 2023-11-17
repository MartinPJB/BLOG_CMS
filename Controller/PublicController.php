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
  const ACITON_IMG = 'img';

  private Base $model;

  private bool $admin_only = false;

  /**
   * @inheritDoc
   */
  public function __construct(array $database_credentials, $twig)
  {
    parent::__construct('public', $twig, $this->admin_only);
    $this->model = new Base($database_credentials, 'site_settings');
    $this->initializeSubRoutes();
  }

  /**
   * @inheritDoc
   */
  public function initializeSubRoutes(): void
  {
    // Add GET routes
    // $this->addSubRoute(self::ACTION_CSS, 'content.html.twig', [$this, 'GET_css'], 'GET');
  }
}

<?php

namespace Controller;

/**
 * Controller interface
 *
 * @package CUEJ_CMS\Controller
 * @version 0.0.1
 * @since 0.0.1
 * @property string $name Controller name
 * @property string $description Controller description
 */
interface ControllerInterface {
  public function __construct();
}
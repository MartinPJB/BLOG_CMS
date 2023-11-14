<?php

namespace Controller;

/**
 * Interface ControllerInterface
 * @package Controller
 * @property mixed $model Le modèle associé au controller
 * @property bool $admin_only Si l'action est réservée aux administrateurs
 */
interface ControllerInterface
{
  /**
   * @param array $database_credentials Les identifiants de connexion à la base de données
   * @param $twig L'instance de Twig
   */
  public function __construct(array $database_credentials, $twig);

  /**
   * Permet d'initialiser les sous-routes.
   *
   * @return void Retourne rien
   */
  public function initializeSubRoutes(): void;
}
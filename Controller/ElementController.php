<?php

namespace Controller;

use Controller\ControllerBase;
use Controller\ControllerInterface;
use Model\Article;
use Model\Element;
use Includes\TypeEscaper;

class ElementController extends ControllerBase implements ControllerInterface
{
  const ACTION_LIST = 'list';
  const ACTION_ADD = 'add';
  const ACTION_EDIT = 'edit';
  const ACTION_DELETE = 'delete';

  private Element $model;
  private Article $articleModel;
  private bool $admin_only = true;

  /**
   * @inheritDoc
   */
  public function __construct(array $database_credentials, $twig)
  {
    parent::__construct('element', $twig, $this->admin_only);
    $this->model = new Element($database_credentials);
    $this->articleModel = new Article($database_credentials);
    $this->initializeSubRoutes();
  }

  /**
   * @inheritDoc
   */
  public function initializeSubRoutes(): void
  {
    // Pas d'article sélectionné
    if (!isset($_GET['article']) || empty($_GET['article'])) {
      $this->redirectToRoute('articles', ArticlesController::ACTION_LIST);
    }

    // L'article sélectionné n'existe pas
    $article_id = TypeEscaper::escapeInt($_GET['article']);
    $article = $this->articleModel->readElement([], ['id' => $article_id]);
    if (empty($article)) {
      $this->redirectToRoute('articles', ArticlesController::ACTION_LIST);
    }

    // Add GET routes
    $this->addSubRoute(self::ACTION_LIST, 'list.html.twig', [$this, 'GET_list'], 'GET');
    $this->addSubRoute(self::ACTION_ADD, 'add.html.twig', [$this, 'GET_add'], 'GET');
    $this->addSubRoute(self::ACTION_EDIT, 'edit.html.twig', [$this, 'GET_edit'], 'GET');
    $this->addSubRoute(self::ACTION_DELETE, 'delete.html.twig', [$this, 'GET_delete'], 'GET');

    // Add POST routes
    $this->addSubRoute(self::ACTION_ADD, 'add.html.twig', [$this, 'POST_add'], 'POST');
    $this->addSubRoute(self::ACTION_EDIT, 'edit.html.twig', [$this, 'POST_edit'], 'POST');
    $this->addSubRoute(self::ACTION_DELETE, 'delete.html.twig', [$this, 'POST_delete'], 'POST');
  }

  /**
   * @return array Retourne un array contenant les éléments
   */
  public function GET_list(): array
  {
    return [
      'elements' => $this->model->readElement([], [
        'article_id' => TypeEscaper::escapeInt($_GET['article'])
      ])
    ];
  }

  /**
   * @return array Retourne un array contenant les balises HTML
   */
  public function GET_add(): array
  {
    return [
      'tags' => [
        'h1',
        'h2',
        'h3',
        'h4',
        'h5',
        'h6',
        'p',
        'img',
        'video',
        'span',
        'audio',
        'a'
      ]
    ];
  }

  /**
   * @return array Retourne un array contenant tous les éléments / un élément si son id est spécifié
   */
  public function GET_edit(): array
  {
    if (!isset($_GET['id'])) {
      return [
        'elements' => $this->model->readElement([], [
          'article_id' => TypeEscaper::escapeInt($_GET['article'])
        ])
      ];
    } else {
      $element_id = $_GET['id'];
      return [
        'element' => $this->model->readElement([], [
          'id' => TypeEscaper::escapeInt($element_id),
          'article_id' => TypeEscaper::escapeInt($_GET['article'])
        ])[0]
      ];
    }
  }

  /**
   * @return array Retourne un array contenant tous les éléments
   */
  public function GET_delete(): array
  {
    return [
      'elements' => $this->model->readElement([], [
        'article_id' => TypeEscaper::escapeInt($_GET['article'])
      ])
    ];
  }

  /**
   * @return ?array Retourne un array / rien
   */
  public function POST_add(): ?array
  {
    $erreurs = [];

    if (isset($_POST['add_submit'])) {

      if (!isset($_POST['balise']) || empty($_POST['balise'])) {
        $erreurs[] = 'Aucune balise n\'a été sélectionnée.';
      } else {
        $newElement = [];

        foreach ($_POST as $key => $value) {
          if ($key == 'add_submit') continue;
          if (empty($value)) continue;

          $key = TypeEscaper::escapeString($key);
          $newElement[$key] = TypeEscaper::escapeString($value);
        }
        $newElement['article_id'] = TypeEscaper::escapeInt($_GET['article']);

        if (empty($erreur)) {
          $this->model->createElement($newElement);
          $this->redirectToSubroute(self::ACTION_LIST, [
            'article' => TypeEscaper::escapeInt($_GET['article'])
          ]);
          return NULL;
        }
      }
    }

    return array_merge($this->GET_add(), ['erreurs' => $erreurs]);
  }

  /**
   * @return ?array Retourne un array / rien
   */
  public function POST_edit(): ?array
  {
    $erreurs = [];

    if (isset($_POST['edit_submit'])) {

      // Vérifie si aucun élément n'est sélectionné dans le formulaire et qu'aucun ID n'est présent dans l'URL
      if (!isset($_POST['element_id']) && !isset($_GET['id'])) {
        $erreur[] = 'Veuillez choisir un élément à modifier';
      }

      // Vérifie si un élément est sélectionné dans le formulaire mais qu'aucun ID n'est présent dans l'URL
      elseif (isset($_POST['element_id']) && !isset($_GET['id'])) {
        $element_id = TypeEscaper::escapeInt($_POST['element_id']);
        $this->redirectToSubroute(self::ACTION_EDIT, [
          'id' => $element_id,
          'article' => TypeEscaper::escapeInt($_GET['article'])
        ]);
        return NULL;
      }

      // Vérifie si un élément est sélectionné dans l'URL et que l'ID est identique à celui du formulaire
      elseif (isset($_POST['element_id']) && isset($_GET['id']) && $_GET['id'] === $_POST['element_id']) {
        $element_id = TypeEscaper::escapeInt($_GET['id']);

        // Vérifie si l'élément existe dans la base de données
        $element = $this->model->readElement([], ['id' => $element_id]);
        if (!empty($element)) {
          $updates = [];

          // Met à jour les champs non videsde l'élément
          foreach ($_POST as $key => $value) {
            if ($key == 'edit_submit' || $key == 'element_id' || empty($value)) {
              continue;
            }

            $key = TypeEscaper::escapeString($key);
            $updates[$key] = TypeEscaper::escapeString($value);
          }

          // Aucune erreur, on maj et on redirige.
          if (empty($erreur)) {
            $this->model->updateElement($element_id, $updates);
            $this->redirectToSubroute(self::ACTION_LIST, [
              'article' => TypeEscaper::escapeInt($_GET['article'])
            ]);
            return NULL;
          }
        } else {
          $erreurs[] = 'L\'élément n\'existe pas';
        }
      }

      // Sinon une erreur est survenue
      else {
        $erreurs[] = 'Erreur dans le traitement de la requête';
      }
    }
    return array_merge($this->GET_edit(), ['erreurs' => $erreurs]);
  }

  /**
   * @return ?array Retourne un array / rien
   */
  public function POST_delete(): ?array
  {
    $erreurs = [];

    if (isset($_POST['delete_submit'])) {

      // Vérifie si un élément est sélectionné dans le formulaire
      if (isset($_POST['element_id'])) {
        $element_id = TypeEscaper::escapeInt($_POST['element_id']);

        // Vérifie si l'élément existe dans la base de données
        $element = $this->model->readElement([], [
          'id' => $element_id,
          'article_id' => TypeEscaper::escapeInt($_GET['article'])
        ]);

        if (!empty($element)) {
          $this->model->deleteElement($element_id);
          $this->redirectToSubroute(self::ACTION_LIST, [
            'article' => TypeEscaper::escapeInt($_GET['article'])
          ]);
          return NULL;
        } else {
          $erreurs[] = 'L\'élément n\'existe pas';
        }
      }
    }

    return array_merge($this->GET_delete(), ['erreurs' => $erreurs]);
  }
}

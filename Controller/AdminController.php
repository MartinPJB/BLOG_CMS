<?php

namespace Controller;

use \Core\Controller\ControllerInterface;
use \Core\Controller\ControllerBase;
use \Core\RequestContext;
use \Model\Articles;

/**
 * Admin controller | Handles all requests related to the admin page
 */
class AdminController extends ControllerBase implements ControllerInterface
{
  public string $name = 'Admin';
  public string $description = 'Handles all requests related to the admin page.';

  /**
   * {@inheritDoc}
   */
  public function __construct(RequestContext $requestContext)
  {
    parent::__construct($requestContext, 'Back');
  }

  /**
   * {@inheritDoc}
   */
  public function index(array $params): void
  {
    $this->render('Admin/index');
  }

  /**
   * Parse the optional parameter and allows to get the different actions and ID needed
   *
   * @return array The different actions and ID needed
   */
  private function parseOptParam(): array
  {
    $opt_param = $this->requestContext->getOptParam();
    $opt_param = explode('/', $opt_param);

    return [
      'action' => !empty($opt_param[0]) ? $opt_param[0] : 'list',
      'id' => $opt_param[1] ?? null,
    ];
  }

  /**
   * The articles method, will handle the creation, edition and deletion of articles
   *
   * @param array $params The parameters passed to the controller
   */
  public function articles(array $params): void
  {
    $additional_params = $this->parseOptParam();
    var_dump($additional_params);

    $action = $additional_params['action'];
    $article_id = $additional_params['id'];

    switch ($action) {
      case 'create':
        $this->render('Articles/create');
        break;
      case 'edit':
        $this->render('Articles/edit', [
          'article_id' => $article_id,
        ]);
        break;
      case 'delete':
        $this->render('Articles/delete', [
          'article_id' => $article_id,
        ]);
        break;
      default:
        $this->render('Articles/list', [
          'articles' => Articles::getAllArticles(),
        ]);
        break;
    }
  }
}
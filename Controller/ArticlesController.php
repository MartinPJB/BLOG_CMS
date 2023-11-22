<?php

namespace Controller;

use \Core\Controller\ControllerInterface;
use \Core\Controller\ControllerBase;
use \Core\Routing\RequestContext;
use \Model\Articles;

/**
 * Article controller
 */
class ArticlesController extends ControllerBase implements ControllerInterface
{
  public string $name = 'Article';
  public string $description = 'Handles all requests related to articles.';

  /**
   * Constructor
   *
   * @param RequestContext $requestContext
   * @return void
   */
  public function __construct(RequestContext $requestContext)
  {
    parent::__construct($requestContext);
  }

  /**
   * Index action
   *
   * @return void
   */
  public function index(): void
  {
    $articles = Articles::getAllArticles();

    $this->render('Articles/index', [
      'articles' => $articles,
    ]);
  }
}
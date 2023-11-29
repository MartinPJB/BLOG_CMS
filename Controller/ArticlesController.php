<?php

namespace Controller;

use \Core\Controller\ControllerInterface;
use \Core\Controller\ControllerBase;
use \Core\RequestContext;
use \Model\Articles;

/**
 * Article controller | Handles all requests related to articles
 */
class ArticlesController extends ControllerBase implements ControllerInterface
{
  public string $name = 'Article';
  public string $description = 'Handles all requests related to articles.';

  /**
   * {@inheritDoc}
   */
  public function __construct(RequestContext $requestContext)
  {
    parent::__construct($requestContext);
  }

  /**
   * {@inheritDoc}
   */
  public function index(array $params): void
  {
    $articles = Articles::getAllArticles();
    $this->render('Articles/index', [
      'articles' => $articles,
    ]);
  }

  /**
   * List has the same behavior as index
   *
   * @param array $params The parameters passed to the controller
   */
  public function list(array $params): void
  {
    $this->index($params);
  }

  /**
   * See a specific article
   *
   * @param array $params The parameters passed to the controller
   */
  public function see(array $params): void
  {
    $article_id = intval($this->requestContext->getOptParam());

    if (!isset($article_id) || $article_id === 0) {
      $this->redirect('articles');
    }

    $article = Articles::getArticle($article_id);
    $this->render('Articles/see', [
      'article' => $article,
    ]);
  }
}
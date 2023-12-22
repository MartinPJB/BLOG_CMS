<?php

namespace Controller;

use \Core\Controller\ControllerInterface;
use \Core\Controller\ControllerBase;
use \Core\RequestContext;
use \Model\Articles;
use \Model\Categories;
use \Model\Users;
use \Model\Blocks;

/**
 * Article controller | Handles all requests related to articles
 */
class ArticlesController extends ControllerBase implements ControllerInterface
{
  public $name = 'Article';
  public $description = 'Handles all requests related to articles.';

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
  public function index($params)
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
  public function all(array $params)
  {
    $this->index($params);
  }

  /**
   * See a specific article
   *
   * @param array $params The parameters passed to the controller
   */
  public function see(array $params)
  {
    $article_id = intval($this->requestContext->getOptParam());
    $article = $article_id ? Articles::getArticle($article_id, Users::isAdmin()) : null;
    if (!$article) {
      ControllerBase::renderError(404, $this->requestContext);
    }
    $categoryArticles = $this->siteSettings->getNavigation()[
      $article->getCategory()->getName()
    ];

    if (is_array($categoryArticles)) {
      foreach ($categoryArticles as $i => $art) {
        if ($art[1] === $article_id) {
          $previous = isset($categoryArticles[$i - 1]) ? $categoryArticles[$i - 1] : null;
          $next = isset($categoryArticles[$i + 1]) ? $categoryArticles[$i + 1] : null;
        }
      }
    }

    $blocks = Blocks::getBlocksByArticle($article_id);

    $this->render('Articles/see', [
      'article' => $article,
      'blocks' => $blocks,
      'sibbling' => [
        'previous' => $previous,
        'next' => $next
      ]
    ]);
  }
}

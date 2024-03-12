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
    $articles = Articles::getAllPublishedArticles();
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

    /* Gets siblings, could be optimised */
    $tree = $this->siteSettings->getNavigation();
    $previous = $next = null;
    $keys = array_keys($tree);
    $nb_cats = count($tree);

    for ($i = 0; $i < $nb_cats; $i++) {
      $nb_arts = count($tree[$keys[$i]]['articles']);
      for ($j = 0; $j < $nb_arts; $j++) {
        if ($tree[$keys[$i]]['articles'][$j][1] == $article_id) {
          if ($j > 0 && isset($tree[$keys[$i]]['articles'][$j - 1][1])) {
            $previous = $tree[$keys[$i]]['articles'][$j - 1];
          } elseif ($i > 0 && isset($tree[$keys[$i - 1]]['articles'])) {
            $previous = [$keys[$i - 1] => end($tree[$keys[$i - 1]]['articles'])];
          }

          if ($j < $nb_arts - 1 && isset($tree[$keys[$i]]['articles'][$j + 1][1])) {
            $next = $tree[$keys[$i]]['articles'][$j + 1];
          } elseif ($i < $nb_cats - 1 && isset($tree[$keys[$i + 1]]['articles'])) {
            $next = [$keys[$i + 1] => $tree[$keys[$i + 1]]['articles'][0]];
          }
          break 2;
        }
      }
    }

    $previous = $this->formatSibling($previous);
    $next = $this->formatSibling($next);

    $blocks = Blocks::getBlocksByArticle($article_id);
    $this->render('Articles/see', [
      'article' => $article,
      'blocks' => $blocks,
      'sibling' => [
        'previous' => $previous,
        'next' => $next
      ]
    ]);
  }

  /**
   * Format a sibling
   *
   * @param array $sibling The sibling that need to be formatted
   */
  private function formatSibling($sibling) {
    if(!$sibling) return false;
    $len = count($sibling);
    if ($len) {
      if ($len > 1) {
        return ['type' => 'to the article', 'name' => $sibling[0], 'id' => $sibling[1]];
      }
      $cat_name = key($sibling);
      return ['type' => 'to the category', 'name' => $cat_name, 'id' => $sibling[$cat_name][1]];
    }
  }
}

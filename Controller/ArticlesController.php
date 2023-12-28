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

    $tree = $this->siteSettings->getNavigation();
    $previous = $next = null;
    $keys = array_keys($tree);
    $nb_cats = count($tree);


    for ($i = 0; $i < $nb_cats; $i++) {
      $nb_arts = count($tree[$keys[$i]]);
      for ($j = 0; $j < $nb_arts; $j++) {
        if ($tree[$keys[$i]][$j][1] == $article_id) {
          if (isset($tree[$keys[$i]][$j - 1][1])) {
            $previous = $tree[$keys[$i]][$j - 1];
          } elseif (isset($keys[$i - 1]) && isset($tree[$keys[$i - 1]])) {
            $previous = [$keys[$i - 1] => $tree[$keys[$i - 1]]];
          }
          if (isset($tree[$keys[$i]][$j + 1][1])) {
            $next = $tree[$keys[$i]][$j + 1];
          } elseif (isset($keys[$i + 1]) && isset($tree[$keys[$i + 1]])) {
            $next = [$keys[$i + 1] => $tree[$keys[$i + 1]]];
          }
          break 2;
        }
      }
    }

    $previous = $this->formatSibblings($previous);
    $next = $this->formatSibblings($next);

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

  /**
   * Format a sibbling
   *
   * @param array $sibbling The sibbling that need to be formatted
   */
  private function formatSibblings($sibbling) {
    if(!$sibbling) return false;
    $len = count($sibbling);
    if ($len) {
      if ($len > 1) {
        return ['type' => 'à l\'article', 'name' => $sibbling[0], 'id' => $sibbling[1]];
      }
      $cat_name = key($sibbling);
      return ['type' => 'au chapitre', 'name' => $cat_name, 'id' => $sibbling[$cat_name][0][1]];
    }
  }
}

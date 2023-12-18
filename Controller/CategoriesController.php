<?php

namespace Controller;

use \Core\Controller\ControllerInterface;
use \Core\Controller\ControllerBase;
use \Core\RequestContext;
use \Model\Categories;
use \Model\Articles;

/**
 * Category controller | Handles all requests related to categories
 */
class CategoriesController extends ControllerBase implements ControllerInterface
{
  public $name = 'Category';
  public $description = 'Handles all requests related to categories.';

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
    $categories = Categories::getAllCategories();
    $this->render('Categories/index', [
      'categories' => $categories,
    ]);
  }

  /**
   * List has the same behavior as index
   *
   * @param array $params The parameters passed to the controller
   */
  public function all($params)
  {
    $this->index($params);
  }

  /**
   * See a specific category
   *
   * @param array $params The parameters passed to the controller
   */
  public function see($params)
  {
    $category_id = intval($this->requestContext->getOptParam());

    if (!isset($category_id) || $category_id === 0) {
      $this->redirect('categories');
    }
    $category = Categories::getCategoryById($category_id);
    $articles = Articles::getAllPublishedArticles($category_id);
    $this->render('Categories/see', [
      'category' => $category,
      'articles' => $articles,
    ]);
  }
}
<?php

namespace Controller\AdminSubControllers;

use Controller\AdminController;
use Core\FieldChecker;
use Model\Medias;
use Model\Categories;

/**
 * AdminCategoriesController | Manage categories in the admin panel
 */
class AdminCategoriesController extends AdminController
{
  public $name = 'Admin - Categories';
  public $description = 'Handles all requests related to categories in the admin panel.';

  /**
   * Validates the fields of a category.
   *
   * @param string $name
   * @param string $description
   * @param Medias $media
   *
   * @throws \Exception
   */
  private function validateCategoryFields($name, $description, $media)
  {
    if (strlen($name) < 5) {
      throw new \Exception('The name must be at least 5 characters long');
    }

    if (strlen($description) < 10) {
      throw new \Exception('The description must be at least 10 characters long');
    }

    if (!isset($media) || !$media->getId()) {
      throw new \Exception('The image is required');
    }
  }

  /**
   * Handles various actions related to categories (create, edit, delete, list).
   *
   * @param array $params
   */
  public function categories($params)
  {
    $additionalParams = $this->parseOptParam();

    $action = $additionalParams['action'];
    $categoryId = $additionalParams['id'];

    switch ($action) {
      case 'create':
        $this->render('Categories/create');
        break;
      case 'edit':
        $this->handleCategoryAction('edit', $categoryId);
        break;
      case 'delete':
        $this->handleCategoryAction('delete', $categoryId);
        break;
      default:
        $this->render('Categories/list', ['categories' => Categories::getAllCategories()]);
        break;
    }
  }

  /**
   * Handles common actions for categories (edit, delete).
   *
   * @param string $action
   * @param int $categoryId
   */
  private function handleCategoryAction($action, $categoryId)
  {
    $this->requiresValidID('categories');
    $category = $this->getCategoryById($categoryId);
    $this->render("Categories/$action", ['category' => $category]);
  }

  /**
   * Gets a category by its ID.
   *
   * @param int $categoryId
   * @return Categories
   *
   * @throws \Exception
   */
  private function getCategoryById($categoryId)
  {
    $this->requiresValidID('categories');
    return Categories::getCategoryById($categoryId);
  }

  /**
   * Handles the creation or edition of categories.
   *
   * @param array $params
   * @param string $action
   */
  private function handleCreateOrEdit($params, $action)
  {
    $categoryId = FieldChecker::cleanInt($this->requestContext->getOptParam());

    try {
      $processed = $this->process_fields();

      if ((isset($_FILES['image']) && !empty($_FILES['image']['tmp_name'])) && !isset($processed['media_id'])) {
        $newMediaId = $this->upload_file($_FILES['image']);
      }

      else if (isset($processed['media_id'])) {
        $newMediaId = $processed['media_id'];
      }

      $media = Medias::getMediaById($newMediaId);

      $this->validateCategoryFields($processed['name'], $processed['description'], $media);

      if ($action === 'create') {
        Categories::create($processed['name'], $processed['description'], $media->getId());
      } elseif ($action === 'edit') {
        Categories::update($categoryId, $processed['name'], $processed['description'], $media->getId());
      }

      $this->redirect('admin/categories');
    } catch (\Exception $e) {
      $this->render("Categories/$action", ['errors' => [$e->getMessage()], 'category' => $categoryId != NULL ? Categories::getCategoryById($categoryId) : NULL]);
    }
  }

  /**
   * Handles the creation of categories.
   *
   * @param array $params
   */
  public function create_category($params)
  {
    $this->handleCreateOrEdit($params, 'create');
  }

  /**
   * Handles the edition of categories.
   *
   * @param array $params
   */
  public function edit_category($params)
  {
    $this->handleCreateOrEdit($params, 'edit');
  }

  /**
   * Handles the deletion of categories.
   *
   * @param array $params
   */
  public function delete_category($params)
  {
    $categoryId = FieldChecker::cleanInt($this->requestContext->getOptParam());

    try {
      Categories::delete($categoryId);
      $this->redirect('admin/categories');
    } catch (\Exception $e) {
      $this->render('Categories/list', ['categories' => Categories::getAllCategories(), 'errors' => [$e->getMessage()]]);
    }
  }
}

<?php

namespace Controller\AdminSubControllers;

use Controller\AdminController;
use Core\FieldChecker;
use Model\Medias;
use Model\Categories;
use Model\Users;

/**
 * AdminCategoriesController | Manage categories in the admin panel
 */
class AdminCategoriesController extends AdminController
{
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
  public function categories(array $params)
  {
    $additionalParams = $this->parseOptParam();

    $action = $additionalParams['action'];
    $categoryId = $additionalParams['id'];

    switch ($action) {
      case 'create':
        $this->render('Categories/create');
        break;
      case 'edit':
        $category = $this->getCategoryById($categoryId);
        $this->render('Categories/edit', ['category' => $category]);
        break;
      case 'delete':
        $category = $this->getCategoryById($categoryId);
        $this->render('Categories/delete', ['category' => $category]);
        break;
      default:
        $this->render('Categories/list', ['categories' => Categories::getAllCategories()]);
        break;
    }
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
   * Handles the creation of categories.
   *
   * @param array $params
   */
  public function create_category(array $params)
  {
    try {
      $processed = $this->process_fields();
      $newMediaId = $this->upload_file($_FILES['image']);
      $media = Medias::getMediaById($newMediaId);

      $this->validateCategoryFields($processed['name'], $processed['description'], $media);

      Categories::create(
        $processed['name'],
        $processed['description'],
        $media->getId()
      );

      $this->redirect('admin/categories');
    } catch (\Exception $e) {
      $this->render('Categories/create', ['errors' => [$e->getMessage()]]);
    }
  }

  /**
   * Handles the edition of categories.
   *
   * @param array $params
   */
  public function edit_category(array $params)
  {
    $categoryId = FieldChecker::cleanInt($this->requestContext->getOptParam());
    try {
      $processed = $this->process_fields();
      $media = Categories::getCategoryById($categoryId)->getImage();

      if (isset($_FILES['image'])) {
        $media = Medias::getMediaById($this->upload_file($_FILES['image']));
      }

      $this->validateCategoryFields($processed['name'], $processed['description'], $media);

      Categories::update(
        $categoryId,
        $processed['name'],
        $processed['description'],
        $media->getId()
      );

      $this->redirect('admin/categories');
    } catch (\Exception $e) {
      $this->render('Categories/edit', ['article_id' => $categoryId, 'errors' => [$e->getMessage()]]);
    }
  }

  /**
   * Handles the deletion of categories.
   *
   * @param array $params
   */
  public function delete_category(array $params)
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

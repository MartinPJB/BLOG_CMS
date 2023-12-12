<?php

namespace Controller\AdminSubControllers;

use \Controller\AdminController;
use \Core\FieldChecker;
use \Model\Medias;
use \Model\Categories;
use \Model\Users;

/**
 * AdminCategoriesController | Manage categories in the admin panel
 */
class AdminCategoriesController extends AdminController
{
  /**
   * The articles method, will handle the creation, edition and deletion of articles
   *
   * @param array $params The parameters passed to the controller
   */
  public function categories(array $params)
  {
    $additional_params = $this->parseOptParam();

    $action = $additional_params['action'];
    $category_id = $additional_params['id'];

    switch ($action) {
      case 'create':
        $this->render('Categories/create');
        break;
      case 'edit':
        $category = Categories::getCategoryById($category_id);
        $this->render('Categories/edit', [
          'category' => $category,
        ]);
        break;
      case 'delete':
        $category = Categories::getCategoryById($category_id);
        $this->render('Categories/delete', [
          'category' => $category,
        ]);
        break;
      default:
        $this->render('Categories/list', [
          'categories' => Categories::getAllCategories(),
        ]);
        break;
    }
  }

  /**
   * The process create method, will handle the creation of categories
   *
   * @param array $params The parameters passed to the controller
   */
  public function create_category(array $params)
  {
    try {
      $processed = $this->process_fields();
      $new_media_id = $this->upload_file($_FILES['image']);
      $media = Medias::getMediaById($new_media_id);

      // Field verification
      if (strlen($processed['name']) < 5) {
        throw new \Exception('The name must be at least 5 characters long');
      }

      if (strlen($processed['description']) < 10) {
        throw new \Exception('The description must be at least 10 characters long');
      }

      if (!isset($media) || !$media->getId()) {
        throw new \Exception('The image is required');
      }

      Categories::create(
        $processed['name'],
        $processed['description'],
        $media->getId()
      );

      $this->redirect('admin/categories');
    } catch (\Exception $e) {
      $this->render('Categories/create', [
        'errors' => [$e->getMessage() . ' ' . $e->getFile()  . ' ' . $e->getLine()],
      ]);
    }
  }

  /**
   * The process edit method, will handle the edition of categories
   *
   * @param array $params The parameters passed to the controller
   */
  public function edit_category(array $params)
  {
    $category_id = FieldChecker::cleanInt($this->requestContext->getOptParam());
    try {
      $processed = $this->process_fields();
      $media = Categories::getCategoryById($category_id)->getImage();

      if (isset($_FILES['image'])) {
        $media = $this->upload_file($_FILES['image']);
      }

      // Field verification
      if (strlen($processed['name']) < 5) {
        throw new \Exception('The name must be at least 5 characters long');
      }

      if (strlen($processed['description']) < 10) {
        throw new \Exception('The description must be at least 10 characters long');
      }

      if (!isset($media) || !$media->getId()) {
        throw new \Exception('The image is required');
      }

      Categories::update(
        $category_id,
        $processed['name'],
        $processed['description'],
        $media->getId()
      );

      $this->redirect('admin/categories');
    } catch (\Exception $e) {
      $this->render('Categories/edit', [
        'article_id' => $category_id,
        'errors' => [$e->getMessage()],
      ]);
    }
  }

  /**
   * The process delete method, will handle the deletion of categories
   *
   * @param array $params The parameters passed to the controller
   */
  public function delete_category(array $params)
  {
    $category_id = FieldChecker::cleanInt($this->requestContext->getOptParam());
    try {
      Categories::delete($category_id);
      $this->redirect('admin/categories');
    } catch (\Exception $e) {
      $this->render('Categories/list', [
        'categories' => Categories::getAllCategories(),
        'errors' => [$e->getMessage()],
      ]);
    }
  }
}

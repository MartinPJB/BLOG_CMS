<?php

namespace Controller\AdminSubControllers;

use Controller\AdminController;
use Core\FieldChecker;
use Model\Medias;
use Model\Categories;
use Model\Users;
use Model\Articles;

/**
 * AdminArticlesController | Manage articles in the admin panel
 */
class AdminArticlesController extends AdminController
{
  /**
   * Validates the fields of an article.
   *
   * @param string $title
   * @param string $description
   * @param Medias $media
   * @param int $categoryId
   *
   * @throws \Exception
   */
  private function validateArticleFields($title, $description, $media, $categoryId)
  {
    if (strlen($title) < 5) {
      throw new \Exception('The title must be at least 5 characters long');
    }

    if (strlen($description) < 10) {
      throw new \Exception('The description must be at least 10 characters long');
    }

    if (!isset($media) || !$media->getId()) {
      throw new \Exception('The image is required');
    }

    if (!isset($categoryId) || !Categories::getCategoryById($categoryId)) {
      throw new \Exception('The category is required');
    }
  }

  /**
   * Gets an article by its ID.
   *
   * @param int $articleId
   * @return Articles
   *
   * @throws \Exception
   */
  private function getArticleById($articleId)
  {
    $this->requiresValidID('articles');
    return Articles::getArticle($articleId);
  }

  /**
   * Handles common actions for articles (edit, delete).
   *
   * @param string $action
   * @param int $articleId
   */
  private function handleArticleAction($action, $articleId)
  {
    $this->requiresValidID('articles');
    $article = $this->getArticleById($articleId);
    $allCategories = Categories::getAllCategories();
    $this->render("Articles/$action", ['article' => $article, 'categories' => $allCategories]);
  }

  /**
   * Handles various actions related to articles (create, edit, delete, list).
   *
   * @param array $params
   */
  public function articles($params)
  {
    $additionalParams = $this->parseOptParam();

    $action = $additionalParams['action'];
    $articleId = $additionalParams['id'];

    switch ($action) {
      case 'create':
        $this->handleCreateAction();
        break;
      case 'edit':
        $this->handleArticleAction('edit', $articleId);
        break;
      case 'delete':
        $this->handleArticleAction('delete', $articleId);
        break;
      default:
        $this->render('Articles/list', ['articles' => Articles::getAllArticles()]);
        break;
    }
  }

  /**
   * Handles the creation of articles.
   */
  private function handleCreateAction()
  {
    $allCategories = Categories::getAllCategories();

    if (count($allCategories) == 0) {
      $this->addMessage('You need to create a category before creating an article');
      $this->redirect('admin/categories');
    }

    $this->render('Articles/create', ['categories' => $allCategories]);
  }

  /**
   * Handles the creation or edition of articles.
   *
   * @param array $params
   * @param string $action
   */
  private function handleCreateOrEdit($params, $action)
  {
    $articleId = FieldChecker::cleanInt($this->requestContext->getOptParam());

    try {
      $processed = $this->process_fields();
      $authorId = Users::getAuthentificatedUser()->getId();
      $newMediaId = $this->upload_file($_FILES['image']);
      $media = Medias::getMediaById($newMediaId);

      $this->validateArticleFields($processed['title'], $processed['description'], $media, $processed['category_id']);

      if ($action === 'create') {
        Articles::create(
          $processed['title'],
          $processed['description'],
          $authorId,
          $media->getId(),
          $processed['category_id'],
          explode(', ', $processed['tags']),
          true,
          false
        );
      } elseif ($action === 'edit') {
        $this->handleEditAction($processed, $authorId, $media, $articleId);
      }

      $this->redirect('admin/articles');
    } catch (\Exception $e) {
      $this->render("Articles/$action", ['categories' => Categories::getAllCategories(), 'errors' => [$e->getMessage()]]);
    }
  }

  /**
   * Handles the edition of articles.
   *
   * @param array $processed
   * @param int $authorId
   * @param Medias $media
   * @param int $articleId
   */
  private function handleEditAction($processed, $authorId, $media, $articleId)
  {
    if (isset($_FILES['image'])) {
      $media = Medias::getMediaById($this->upload_file($_FILES['image']));
    }

    Articles::update(
      $articleId,
      $processed['title'],
      $processed['description'],
      $authorId,
      $media->getId(),
      $processed['category_id'],
      explode(', ', $processed['tags']),
      $processed['status'] == 'draft',
      $processed['status'] == 'published'
    );
  }

  /**
   * Handles the creation of articles.
   *
   * @param array $params
   */
  public function create_article($params)
  {
    $this->handleCreateOrEdit($params, 'create');
  }

  /**
   * Handles the edition of articles.
   *
   * @param array $params
   */
  public function edit_article($params)
  {
    $this->handleCreateOrEdit($params, 'edit');
  }

  /**
   * Handles the deletion of articles.
   *
   * @param array $params
   */
  public function delete_article($params)
  {
    $articleId = FieldChecker::cleanInt($this->requestContext->getOptParam());

    try {
      Articles::delete($articleId);
      $this->redirect('admin/articles');
    } catch (\Exception $e) {
      $this->render('Articles/list', ['articles' => Articles::getAllArticles(), 'errors' => [$e->getMessage()]]);
    }
  }
}

<?php

namespace Controller\AdminSubControllers;

use Controller\AdminController;
use Core\FieldChecker;
use Model\Articles;
use Model\Medias;
use Model\Categories;
use Model\Users;

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
   * Handles various actions related to articles (create, edit, delete, list).
   *
   * @param array $params
   */
  public function articles(array $params)
  {
    $additionalParams = $this->parseOptParam();

    $action = $additionalParams['action'];
    $articleId = $additionalParams['id'];

    $allCategories = Categories::getAllCategories();

    switch ($action) {
      case 'create':
        if (count($allCategories) == 0) {
          $this->addMessage('You need to create a category before creating an article');
          $this->redirect('admin/categories');
        }
        $this->render('Articles/create', ['categories' => $allCategories]);
        break;
      case 'edit':
        $article = $this->getArticleById($articleId);
        $this->render('Articles/edit', ['article' => $article, 'categories' => $allCategories]);
        break;
      case 'delete':
        $article = $this->getArticleById($articleId);
        $this->render('Articles/delete', ['article' => $article]);
        break;
      default:
        $this->render('Articles/list', ['articles' => Articles::getAllArticles()]);
        break;
    }
  }

  /**
   * Handles the creation of articles.
   *
   * @param array $params
   */
  public function create_article(array $params)
  {
    try {
      $processed = $this->process_fields();
      $authorId = Users::getAuthentificatedUser()->getId();
      $newMediaId = $this->upload_file($_FILES['image']);
      $media = Medias::getMediaById($newMediaId);

      $this->validateArticleFields($processed['title'], $processed['description'], $media, $processed['category_id']);

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

      $this->redirect('admin/articles');
    } catch (\Exception $e) {
      $this->render('Articles/create', ['categories' => Categories::getAllCategories(), 'errors' => [$e->getMessage()]]);
    }
  }

  /**
   * Handles the edition of articles.
   *
   * @param array $params
   */
  public function edit_article(array $params)
  {
    $articleId = FieldChecker::cleanInt($this->requestContext->getOptParam());
    try {
      $processed = $this->process_fields();
      $authorId = Users::getAuthentificatedUser()->getId();
      $media = Articles::getArticle($articleId)->getImage();

      if (isset($_FILES['image'])) {
        $media = Medias::getMediaById($this->upload_file($_FILES['image']));
      }

      $this->validateArticleFields($processed['title'], $processed['description'], $media, $processed['category_id']);

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

      $this->redirect('admin/articles');
    } catch (\Exception $e) {
      $this->render('Articles/edit', ['article_id' => $articleId, 'errors' => [$e->getMessage()]]);
    }
  }

  /**
   * Handles the deletion of articles.
   *
   * @param array $params
   */
  public function delete_article(array $params)
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

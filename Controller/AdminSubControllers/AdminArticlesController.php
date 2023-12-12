<?php

namespace Controller\AdminSubControllers;

use \Controller\AdminController;
use \Core\FieldChecker;
use \Model\Articles;
use \Model\Categories;
use \Model\Users;

/**
 * AdminArticlesController | Manage articles in the admin panel
 */
class AdminArticlesController extends AdminController {
  /**
   * The articles method, will handle the creation, edition and deletion of articles
   *
   * @param array $params The parameters passed to the controller
   */
  public function articles(array $params): void
  {
    $additional_params = $this->parseOptParam();

    $action = $additional_params['action'];
    $article_id = $additional_params['id'];

    switch ($action) {
      case 'create':
        $this->render('Articles/create', [
          'categories' => Categories::getAllCategories(),
        ]);
        break;
      case 'edit':
        $article = Articles::getArticle($article_id);
        $this->render('Articles/edit', [
          'article' => $article,
          'categories' => Categories::getAllCategories(),
        ]);
        break;
      case 'delete':
        $article = Articles::getArticle($article_id);
        $this->render('Articles/delete', [
          'article' => $article,
        ]);
        break;
      default:
        $this->render('Articles/list', [
          'articles' => Articles::getAllArticles(),
        ]);
        break;
    }
  }

  /**
   * The process create method, will handle the creation of articles
   *
   * @param array $params The parameters passed to the controller
   */
  public function create_article(array $params): void
  {
    try {
      $processed = $this->process_fields();
      $author_id = Users::getAuthentificatedUser()->getId();
      $media = $this->upload_file($_FILES['image']);

      Articles::create(
        $processed['title'],
        $processed['description'],
        $author_id,
        $media->getId(),
        $processed['category_id'],
        explode(', ', $processed['tags']),
        TRUE,
        FALSE
      );

      $this->redirect('admin/articles');
    } catch (\Exception $e) {
      $this->render('Articles/create', [
        'categories' => Categories::getAllCategories(),
        'errors' => [$e->getMessage()],
      ]);
    }
  }

  /**
   * The process edit method, will handle the edition of articles
   *
   * @param array $params The parameters passed to the controller
   */
  public function edit_article(array $params): void
  {
    $article_id = FieldChecker::cleanInt($this->requestContext->getOptParam());
    try {
      $processed = $this->process_fields();
      $author_id = Users::getAuthentificatedUser()->getId();
      $media = Articles::getArticle($article_id)->getImage();

      if (isset($_FILES['image'])) {
        $media = $this->upload_file($_FILES['image']);
      }

      Articles::update(
        $article_id,
        $processed['title'],
        $processed['description'],
        $author_id,
        $media->getId(),
        $processed['category_id'],
        explode(', ', $processed['tags']),
        $processed['status'] == 'draft',
        $processed['status'] == 'published'
      );

      $this->redirect('admin/articles');
    } catch(\Exception $e) {
      $this->render('Articles/edit', [
        'article_id' => $article_id,
        'errors' => [$e->getMessage()],
      ]);
    }
  }

  /**
   * The process delete method, will handle the deletion of articles
   *
   * @param array $params The parameters passed to the controller
   */
  public function delete_article(array $params): void
  {
    $article_id = FieldChecker::cleanInt($this->requestContext->getOptParam());
    try {
      Articles::delete($article_id);
      $this->redirect('admin/articles');
    } catch (\Exception $e) {
      $this->render('Articles/list', [
        'articles' => Articles::getAllArticles(),
        'errors' => [$e->getMessage()],
      ]);
    }
  }
}
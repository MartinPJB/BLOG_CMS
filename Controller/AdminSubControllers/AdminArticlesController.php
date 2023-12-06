<?php

namespace Controller\AdminSubControllers;

use \Controller\AdminController;
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
        $this->render('Articles/edit', [
          'article_id' => $article_id,
        ]);
        break;
      case 'delete':
        $this->render('Articles/delete', [
          'article_id' => $article_id,
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
  public function create_article(array $params)
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

}
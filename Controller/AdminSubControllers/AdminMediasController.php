<?php

namespace Controller\AdminSubControllers;

use \Controller\AdminController;
use \Core\FieldChecker;
use \Model\Articles;
use \Model\Medias;
use \Model\Users;

/**
 * AdminMediasController | Manage medias in the admin panel
 */
class AdminMediasController extends AdminController {
  /**
   * The medias method, will handle the creation, edition and deletion of medias
   *
   * @param array $params The parameters passed to the controller
   */
  public function medias(array $params): void
  {
    $additional_params = $this->parseOptParam();

    $action = $additional_params['action'];
    $media_id = $additional_params['id'];

    switch ($action) {
      case 'upload':
        $this->render('Medias/upload');
        break;

      case 'edit':
        $media = Medias::getMediaById($media_id);
        $this->render('Medias/edit', [
          'media' => $media,
        ]);
        break;
      case 'delete':
        $this->render('Medias/delete', [
          'media_id' => $media_id,
        ]);
        break;
      default:
        $this->render('Medias/list', [
          'media' => Medias::getAllMedias(),
        ]);
        break;
    }
  }

  /**
   * The process delete method, will handle the creation of medias
   *
   * @param array $params The parameters passed to the controller
   */
  public function delete_media(array $params): void
  {
    $media_id = FieldChecker::cleanInt($this->requestContext->getOptParam());
    try {
      $is_JSON = isset($params['GET']['json']); // We might need to use this method for it to return a JSON response

      // Delete media
      Medias::delete($media_id);

      if (!$is_JSON) {
        $this->redirect('admin/medias');
      } else {
        header('Content-Type: application/json');
        echo json_encode([
          'success' => 'Media deleted successfully',
        ]);
      }
    } catch (\Exception $e) {
      $data = [
        'medias' => Medias::getAllMedias(),
        'errors' => [$e->getMessage()],
      ];

      if (!$is_JSON) {
        $this->render('Medias/list', $data);
      } else {
        header('Content-Type: application/json');
        echo json_encode($data);
      }
    }
  }
}
<?php

namespace Controller\AdminSubControllers;

use Controller\AdminController;
use Core\FieldChecker;
use Model\Articles;
use Model\Medias;
use Model\Users;

/**
 * AdminMediasController | Manage medias in the admin panel
 */
class AdminMediasController extends AdminController
{
  /**
   * Handles various actions related to medias (upload, edit, delete, list).
   *
   * @param array $params
   */
  public function medias($params)
  {
    $additionalParams = $this->parseOptParam();

    $action = $additionalParams['action'];
    $media_id = $additionalParams['id'];

    switch ($action) {
      case 'upload':
        $this->render('Medias/upload');
        break;

      case 'edit':
        $this->requiresValidID('medias');
        $media = $this->getMediaById($media_id);
        $this->render('Medias/edit', ['media' => $media]);
        break;

      case 'delete':
        $this->requiresValidID('medias');
        $this->render('Medias/delete', ['mediaId' => $media_id]);
        break;

      default:
        $this->render('Medias/list', ['medias' => Medias::getAllMedias()]);
        break;
    }
  }

  /**
   * Handles the deletion of medias.
   *
   * @param array $params
   */
  public function delete_media($params)
  {

    $media_id = FieldChecker::cleanInt($this->requestContext->getOptParam());
    try {
      $isJSON = isset($params['GET']['json']); // We might need to use this method for it to return a JSON response

      // Delete media
      Medias::delete($media_id);

      $this->handleDeleteResponse($isJSON, ['success' => 'Media deleted successfully.']);
    } catch (\Exception $e) {
      $data = ['medias' => Medias::getAllMedias(), 'errors' => [$e->getMessage()]];
      $this->handleDeleteResponse($isJSON, $data);
    }
  }

  /**
   * Gets a media by its ID.
   *
   * @param int $media_id
   * @return Medias
   *
   * @throws \Exception
   */
  private function getMediaById($media_id)
  {
    $this->requiresValidID('medias');
    return Medias::getMediaById($media_id);
  }

  /**
   * Handles the response after deleting a media.
   *
   * @param bool $isJSON
   * @param mixed $data
   */
  private function handleDeleteResponse($isJSON, $data)
  {
    if (!$isJSON) {
      $this->redirect('admin/medias');
    } else {
      header('Content-Type: application/json');
      echo json_encode($data);
    }
  }
}

<?php

namespace Controller\AdminSubControllers;

use Controller\AdminController;
use Core\FieldChecker;
use Model\Medias;

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
    $mediaId = $additionalParams['id'];

    switch ($action) {
      case 'upload':
        $this->render('Medias/upload');
        break;

      case 'edit':
        $this->handleMediaAction('edit', $mediaId);
        break;

      case 'delete':
        $this->handleMediaAction('delete', $mediaId);
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
    $mediaId = FieldChecker::cleanInt($this->requestContext->getOptParam());

    try {
      $isJSON = $this->checkAndGetJsonParam($params);

      // Delete media
      $media = $this->getMediaById($mediaId);
      $path = $media->getPath();
      Medias::delete($mediaId);

      // Delete file
      unlink($path);

      $this->handleJSONresponse($isJSON, ['success' => 'Media deleted successfully.']);
    } catch (\Exception $e) {
      $this->handleException($isJSON, $e);
    }
  }

  /**
   * Handles the unassignment of medias from articles or block or anything.
   *
   * @param array $params
   */
  public function unassign_media($params)
  {
    $mediaId = FieldChecker::cleanInt($this->requestContext->getOptParam());

    try {
      $isJSON = $this->checkAndGetJsonParam($params);

      $this->validateUnassignMediaParams($params);

      $table = FieldChecker::cleanString($params['POST']['table']);
      $id = FieldChecker::cleanInt($params['POST']['id']);
      $column = FieldChecker::cleanString($params['POST']['column']);

      $media = Medias::getMediaById($mediaId);
      $media->unassignFrom($table, $column, $id);

      $this->handleJSONresponse($isJSON, ['success' => 'Media unassigned successfully.']);
    } catch (\Exception $e) {
      $this->handleException($isJSON, $e);
    }
  }

  /**
   * Gets a media by its ID.
   *
   * @param int $mediaId
   * @return Medias
   *
   * @throws \Exception
   */
  private function getMediaById($mediaId)
  {
    $this->requiresValidID('medias');
    return Medias::getMediaById($mediaId);
  }

  /**
   * Handles the response after deleting a media.
   *
   * @param bool $isJSON
   * @param mixed $data
   */
  private function handleJSONresponse($isJSON, $data)
  {
    if (!$isJSON) {
      $this->redirect('admin/medias');
    } else {
      header('Content-Type: application/json');
      echo json_encode($data);
    }
  }

  /**
   * Handles common actions for media (edit, delete).
   *
   * @param string $action
   * @param int $mediaId
   */
  private function handleMediaAction($action, $mediaId)
  {
    $this->requiresValidID('medias');
    $media = $this->getMediaById($mediaId);
    $this->render("Medias/$action", ['media' => $media]);
  }

  /**
   * Validates and retrieves the 'json' parameter.
   *
   * @param array $params
   * @return bool
   *
   * @throws \Exception
   */
  private function checkAndGetJsonParam($params)
  {
    $isJSON = isset($params['GET']['json']);
    if (!$isJSON) {
      throw new \Exception('JSON parameter is missing.');
    }

    return $isJSON;
  }

  /**
   * Validates parameters required for unassign_media action.
   *
   * @param array $params
   *
   * @throws \Exception
   */
  private function validateUnassignMediaParams($params)
  {
    if (!isset($params['POST']['table'])) {
      throw new \Exception('Table name is missing.');
    }
    if (!isset($params['POST']['id'])) {
      throw new \Exception('ID is missing.');
    }
    if (!isset($params['POST']['column'])) {
      throw new \Exception('Column name is missing.');
    }
  }

  /**
   * Handles the exception and sends the JSON response.
   *
   * @param bool $isJSON
   * @param \Exception $e
   */
  private function handleException($isJSON, \Exception $e)
  {
    $data = ['medias' => Medias::getAllMedias(), 'errors' => [$e->getMessage()]];
    $this->handleJSONresponse($isJSON, $data);
  }
}

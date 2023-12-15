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
   * Edits a media in the admin panel.
   *
   * @param array $params
   */
  public function edit_media($params)
  {
    $mediaId = FieldChecker::cleanInt($this->requestContext->getOptParam());

    try {
      $this->requiresValidID('medias');

      // Fetch the media by ID
      $media = $this->getMediaById($mediaId);

      // Handle the form submission
      if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $this->handleEditMediaForm($media);
      } else {
        // Render the edit media form
        $this->render('Medias/edit', ['media' => $media]);
      }
    } catch (\Exception $e) {
      $this->render('Medias/list', ['medias' => Medias::getAllMedias(), 'errors' => [$e->getMessage()]]);
    }
  }

  /**
   * Handles the form submission for editing a media.
   *
   * @param Medias $media
   *
   * @throws \Exception
   */
  private function handleEditMediaForm($media)
  {
    try {
      // Process and validate form fields as needed
      $processed = $this->process_fields();

      // Update media with new information
      $media::update(
        $media->getId(),
        $processed['name'],
        $media->getType(),
        $media->getSize(),
        $media->getPath(),
        $processed['alt'],
        $media->getUploadedAt()->format('Y-m-d H:i:s'),
        $media->getHash()
      );

      // Redirect to the medias list page
      $this->redirect('admin/medias');
    } catch (\Exception $e) {
      // Render the edit media form with errors
      $this->render('Medias/edit', ['media' => $media, 'errors' => [$e->getMessage()]]);
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
    $isJSON = $this->checkAndGetJsonParam($params);

    try {
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
    $isJSON = $this->checkAndGetJsonParam($params);

    try {

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
   */
  private function checkAndGetJsonParam($params)
  {
    return isset($params['GET']['json']);
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

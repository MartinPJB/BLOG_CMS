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
  public $name = 'Admin - Medias';
  public $description = 'Handles all requests related to medias in the admin panel.';

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
      case 'edit':
        $this->handleMediaAction('edit', $mediaId);
        break;

      case 'upload':
        $this->render('Medias/upload');
        break;

      default:
        $this->render('Medias/list', ['medias' => Medias::getAllMedias()]);
        break;
    }
  }

  /**
   * Get all medias and returns them as JSON.
   *
   * @param array $params
   */
  public function get_all_medias($params)
  {
    $medias = Medias::getAllMedias();
    $this->handleJSONresponse(true, ['medias' => $medias]);
  }

  /**
   * Uploads a media in the admin panel.
   *
   * @param array $params
   */
  public function upload_media($params)
  {
    try {
      // Handle the form submission
      if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $this->handleUploadMediaForm();
      } else {
        // Render the upload media form
        $this->render('Medias/upload');
      }
    } catch (\Exception $e) {
      $this->render('Medias/list', ['medias' => Medias::getAllMedias(), 'errors' => [$e->getMessage()]]);
    }
  }

  /**
   * Handles the form submission for uploading a media.
   *
   * @throws \Exception
   */
  private function handleUploadMediaForm()
  {
    try {
      // Process and validate form fields as needed
      $processed = $this->process_fields();

      var_dump($processed['name'], $processed['alt']);
      $file_extension = strtolower(pathinfo($_FILES['media_file']['name'], PATHINFO_EXTENSION));

      // Upload file and get its ID
      $uploadedMediaId = $this->upload_file($_FILES['media_file'], $processed['name'] . '.' . $file_extension, $processed['alt']);

      // Perform additional checks on the uploaded media type
      $this->validateMediaType($uploadedMediaId);

      // Redirect to the medias list page
      $this->redirect('admin/medias');
    } catch (\Exception $e) {
      // Render the upload media form with errors
      $this->render('Medias/upload', ['errors' => [$e->getMessage()]]);
    }
  }

  /**
   * Validates the media type based on its ID.
   *
   * @param int $mediaId
   *
   * @throws \Exception
   */
  private function validateMediaType($mediaId)
  {
    // Fetch the media by ID
    $media = $this->getMediaById($mediaId);

    // Allowed media types
    $allowedTypes = ['mp3', 'mp4', 'png', 'jpeg', 'jpg', 'webp', 'gif', 'svg'];

    // Get the file extension
    $fileExt = strtolower(pathinfo($media->getPath(), PATHINFO_EXTENSION));

    // Check if the file type is allowed
    if (!in_array($fileExt, $allowedTypes)) {
      // Delete the media if it's not allowed
      Medias::delete($mediaId);
      throw new \Exception('Invalid media type. Only MP3, MP4, PNG, JPEG, JPG, WebP, GIF, and SVG are allowed.');
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
      echo json_encode($data, JSON_PRETTY_PRINT);
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

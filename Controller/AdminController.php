<?php

namespace Controller;

use \Core\Controller\ControllerInterface;
use \Core\Controller\ControllerBase;
use \Core\RequestContext;
use \Core\FieldChecker;
use \Model\Medias;
use \Core\Config;
use Core\Database\Manager;
use \Model\SiteSettings;

/**
 * Admin controller | Handles all requests related to the admin page
 */
class AdminController extends ControllerBase implements ControllerInterface
{
  public $name = 'Admin';
  public $description = 'Handles all requests related to the admin page.';

  /**
   * {@inheritDoc}
   */
  public function __construct(RequestContext $requestContext)
  {
    parent::__construct($requestContext, 'Back');
  }

  /**
   * {@inheritDoc}
   */
  public function index($params)
  {
    $this->render('Admin/index');
  }

  /**
   * Parse the optional parameter and allows to get the different actions and ID needed
   *
   * @return array The different actions and ID needed
   */
  protected function parseOptParam()
  {
    $opt_param = $this->requestContext->getOptParam();
    $opt_param = explode('/', $opt_param);

    return [
      'action' => !empty($opt_param[0]) ? $opt_param[0] : 'list',
      'id' => isset($opt_param[1]) ? $opt_param[1] : null,
    ];
  }

  /**
   * The process create method, will handle the validation of all the fields in the admin panel (articles, categories, etc.)
   */
  protected function process_fields()
  {
    $POST = $this->requestContext->getParameters()['POST'];

    // Check all the fields to avoid SQL injections and XSS attacks
    $fields = [];
    foreach ($POST as $field => $value) {
      $field = FieldChecker::cleanString($field);

      // Check if the field is an email
      if (FieldChecker::checkEmail($value)) {
        $fields[$field] = FieldChecker::cleanString($value);
        continue;
      }

      // Check if the field is a number
      else if (FieldChecker::checkInt($value)) {
        $fields[$field] = FieldChecker::cleanInt($value);
        continue;
      }

      // Check if the field is a date
      else if (FieldChecker::checkDate($value)) {
        $fields[$field] = FieldChecker::cleanDate($value);
        continue;
      }

      // Check if the field is a URL
      else if (FieldChecker::checkUrl($value)) {
        $fields[$field] = FieldChecker::cleanString($value);
        continue;
      }

      // Check if the field is a boolean
      else if (FieldChecker::checkBool($value)) {
        $fields[$field] = FieldChecker::cleanBool($value);
        continue;
      }

      // Then we just clean the string
      else {
        $fields[$field] = FieldChecker::cleanString($value);
        continue;
      }
    }

    // Call the right method in order to create the thing
    return $fields;
  }

  /**
   * The upload file method, will handle the upload of files in the admin panel (articles, categories, etc.)
   *
   * @param array $file The file to upload
   * @return mixed The uploaded file
   */
  protected function upload_file($file, $name = "", $alt = "")
  {
    try {
      if (!isset($file) || empty($file['type'])) {
        throw new \Exception("Invalid file data.");
      }

      if (empty($name)) {
        $name = uniqid() . '.' . explode('/', $file['type'])[1];
      }

      if (empty($alt)) {
        $alt = $name;
      }

      $file_tmp = $file['tmp_name'];
      $file_size = $file['size'];
      $file_error = $file['error'];

      $file_ext = explode('.', $name);
      $file_ext = strtolower(end($file_ext));

      $file_size_limit = Config::get('site_file_size_limit');

      if ($file_error !== UPLOAD_ERR_OK) {
        throw new \ErrorException("There was an error uploading your file. Error code: {$file_error}");
      }

      if ($file_size > $file_size_limit) {
        throw new \ErrorException("Your file is too big. The maximum size is {$file_size_limit} bytes.");
      }

      $directory = dirname(__DIR__) . '/uploads/' . $file_ext . '/';

      // Create the folder if it doesn't exist
      if (!file_exists($directory)) {
        mkdir($directory, 0777, true);
      }

      $file_destination = $directory . $name;

      $startTime = time();
      $maxWaitTime = 5; // Maximum wait time in seconds
      $loopActive = true;
      while ($loopActive && !file_exists($file_tmp)) {
        // Check if maximum wait time is reached
        if (time() - $startTime > $maxWaitTime) {
          $loopActive = false;
        }
        sleep(1);
      }
      // Get file's hash
      if (!file_exists($file_tmp)) {
        // The tmp exists but sometimes it can return an error for some reason, in that case just refresh the page
        header("Refresh:0");
      }

      $file_hash = md5_file($file_tmp);
      var_dump($file_hash);

      // Check if the file already exists
      $existingFile = Manager::read('medias', [], ['hash' => $file_hash]);

      if (!empty($existingFile)) {
        var_dump("Successfully fetched existing file (no duplication):", $existingFile[0]['id']);
        return $existingFile[0]['id'];
      }

      if (!move_uploaded_file($file_tmp, $file_destination)) {
        throw new \ErrorException("There was an error uploading your file. => move_uploaded_file");
      }

      Medias::create(
        ucfirst(explode('.', $name)[0]),
        mime_content_type($file_destination),
        $file_size,
        "uploads/{$file_ext}/{$name}",
        $alt,
        date('Y-m-d H:i:s'),
        $file_hash
      );

      var_dump("Successfully uploaded file (new file):", Manager::getLastInsertedId());
      return Manager::getLastInsertedId();
    } catch (\Exception $e) {
      // Log or print detailed error information for debugging
      error_log($e->getMessage());
      throw new \ErrorException("There was an error uploading your file. {$e->getMessage()}");
    }
  }



  /**
   * If a subpage requires a valid ID of an element in the database, this method will check if the ID is valid
   *
   * @param string $table The table to check the ID in
   * @return int|null The ID if it is valid, null otherwise
   */
  protected function checkId($table)
  {
    $id = $this->requestContext->getOptParam();
    $id = explode('/', $id);
    $id = FieldChecker::cleanInt(end($id));

    if (empty($id)) return null;

    $result = Manager::read($table, [], ['id' => $id]);

    if (empty($result)) return null;

    return $id;
  }

  /**
   *
   */
  protected function requiresValidID($pageName)
  {
    $id = $this->checkId($pageName);

    if (empty($id) || $id === null) {
      $this->redirect('admin/' . $pageName);
    }

    return $id;
  }
}

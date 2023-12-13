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
  public function index(array $params)
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
  protected function upload_file(array $file, $name = "")
  {
    if (empty($file)) return false;
    if (empty($name)) $name = uniqid() . '.' . explode('/', $file['type'])[1];

    $file_tmp = $file['tmp_name'];
    $file_size = $file['size'];
    $file_error = $file['error'];

    $file_ext = explode('.', $name);
    $file_ext = strtolower(end($file_ext));

    $file_size_limit = Config::get('site_file_size_limit');

    if ($file_error === UPLOAD_ERR_OK) {
      if ($file_size <= $file_size_limit) {
        $directory = dirname(__DIR__) . '/Themes/' . $this->siteSettings->getTheme() . '/Back/public/admin_uploads/';

        // Create the folder if it doesn't exist
        if (!file_exists($directory . $file_ext . '/')) mkdir($directory . $file_ext . '/', 0777, true);

        // $file_destination = __DIR__ . '/../../' . $this->siteSettings->getTheme() . '/Back/public/admin_upload/' . $name;
        $file_destination = dirname(__DIR__) . '/Themes/' . $this->siteSettings->getTheme() . '/Back/public/admin_uploads/' . $file_ext . '/' . $name;
        if (move_uploaded_file($file_tmp, $file_destination)) {
          Medias::create(
            ucfirst(explode('.', $name)[0]),
            mime_content_type($file_destination),
            $file_size,
            "public/back/admin_uploads/{$file_ext}/{$name}",
            $name,
            date('Y-m-d H:i:s')
          );
          return Manager::getLastInsertedId();
        }
      }

      throw new \ErrorException("The file is too big, over {$file_size_limit} mb.");
    }

    throw new \ErrorException("There was an error uploading your file.");
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
    $id = explode('/', $id)[1];
    $id = FieldChecker::cleanInt($id);

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
      $this->redirect('admin/'.$pageName);
    }

    return $id;
  }
}

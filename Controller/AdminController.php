<?php

namespace Controller;

use \Core\Controller\ControllerInterface;
use \Core\Controller\ControllerBase;
use \Core\RequestContext;
use \Core\FieldChecker;
use \Model\Medias;
use \Core\Config;
use \Model\SiteSettings;

/**
 * Admin controller | Handles all requests related to the admin page
 */
class AdminController extends ControllerBase implements ControllerInterface
{
  public string $name = 'Admin';
  public string $description = 'Handles all requests related to the admin page.';

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
  public function index(array $params): void
  {
    $this->render('Admin/index');
  }

  /**
   * Parse the optional parameter and allows to get the different actions and ID needed
   *
   * @return array The different actions and ID needed
   */
  protected function parseOptParam(): array
  {
    $opt_param = $this->requestContext->getOptParam();
    $opt_param = explode('/', $opt_param);

    return [
      'action' => !empty($opt_param[0]) ? $opt_param[0] : 'list',
      'id' => $opt_param[1] ?? null,
    ];
  }

  /**
   * The process create method, will handle the validation of all the fields in the admin panel (articles, categories, etc.)
   */
  protected function process_fields(): array
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
  protected function upload_file(array $file, string $name = ""): mixed
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
        // $file_destination = __DIR__ . '/../../' . $this->siteSettings->getTheme() . '/Back/public/admin_upload/' . $name;
        $file_destination = dirname(__DIR__, 1) . '/Themes/' . $this->siteSettings->getTheme() . '/Back/public/admin_uploads/' . $name;
        if (move_uploaded_file($file_tmp, $file_destination)) {
          $new_media = Medias::create(
            ucfirst(explode('.', $name)[0]),
            mime_content_type($file_destination),
            $file_size,
            "public/back/admin_uploads/{$name}",
            $name,
            date('Y-m-d H:i:s')
          );
          return $new_media;
        }
      }

      throw new \ErrorException("The file is too big, over {$file_size_limit} mb.");
    }

    throw new \ErrorException("There was an error uploading your file.");
  }
}
<?php

namespace Controller\AdminSubControllers;

use Controller\AdminController;
use Core\FieldChecker;
use Model\Blocks;
use Model\Medias;

/**
 * AdminBlocksController | Manage blocks in the admin panel
 */
class AdminBlocksController extends AdminController {
  public $name = 'Admin - Blocks';
  public $description = 'Handles all requests related to blocks in the admin panel.';

  /**
   * Validates the fields of a block.
   *
   * @param array $block_fields: Should contain arrays like : ['name' => $field_name, 'content' => $field_content,'min" => $min_length, 'max" => $max_length]
   * @param string $description
   * @param Medias $media
   *
   * @throws \Exception
   */
  private function validateBlockFields($block_fields = [], $media) {
    foreach ($block_fields as $field) {
      $len = strlen($field['content']);
      if ($len < $field['min']) {
        throw new \Exception('The ' . $field['name'] . ' must be at least ' . $field['min'] . ' characters long');
      }
      if ($len > $field['max']) {
        throw new \Exception('The ' . $field['name'] . ' must be less than ' . $field['max'] . ' characters long');
      }
    }

    if (!isset($media) || !$media->getId()) {
      throw new \Exception('The media is required');
    }
  }

  /**
   * Gets an block by its ID.
   *
   * @param int $blockId
   * @return Blocks
   *
   * @throws \Exception
   */
  private function getBlockById($blockId)
  {
    $this->requiresValidID('blocks');
    return Blocks::getBlock($blockId, true);
  }

  /**
   * Handles common actions for blocks (edit, delete).
   *
   * @param string $action
   * @param int $blocId
   */
  private function handleBlockAction($action, $blockId)
  {
    $this->requiresValidID('blocks');
    $block = $this->getBlockById($blockId);
/*     $this->render("Blocks/$action", ['block' => $block, 'categories' => $allCategories]);
 */  }

  /**
   * Handles various actions related to blocks (create, edit, delete, list).
   *
   * @param array $params
   */
  public function blocks($params) {
    $additionalParams = $this->parseOptParam();

    $action = $additionalParams['action'];
    $blockId = $additionalParams['id'];

    switch ($action) {
      case 'create':
        $this->handleCreateAction();
        break;
      case 'edit':
        $this->handleBlockAction('edit', $blockId);
        break;
      case 'delete':
        $this->handleBlockAction('delete', $blockId);
        break;
      case 'blocks':
        $this->requiresValidID('blocks');
        $blocksBlock = Blocks::getBlocksByBlock($blockId);
        $block = Blocks::getBlock($blockId);
        $availableBlocks = Blocks::getAvailableBlocks();
        $this->render('Blocks/list', ['block' => $block, 'available_blocks' => $availableBlocks]);
        break;
      default:
        $this->render('Blocks/list', ['blocks' => Blocks::getAllBlocks()]);
        break;
    }
  }
  /**
   * Handles the creation of blocks.
   *
   * @param array $params
   */
  public function create_block($params) {
    $this->handleCreateOrEdit($params, 'create');
  }

  /**
   * Handles the edition of blocks.
   *
   * @param array $params
   */
  public function edit_block($params) {
    $this->handleCreateOrEdit($params, 'edit');
  }

  /**
   * Handles the deletion of blocks.
   *
   * @param array $params
   */
  public function delete_block($params) {
    $blockId = FieldChecker::cleanInt($this->requestContext->getOptParam());

    try {
      Blocks::delete($blockId);
      $this->redirect('admin/blocks');
    } catch (\Exception $e) {
      $this->render('Blocks/list', ['blocks' => Blocks::getAllBlocks(), 'errors' => [$e->getMessage()]]);
    }
  }

  /**
   * Handles the creation or edition of blocks.
   *
   * @param array $params
   * @param string $action
   */
  private function handleCreateOrEdit($params, $action) {
    $articleId = FieldChecker::cleanInt($this->requestContext->getOptParam());

    try {
      $processed =  $this->process_fields();
      $jsonDatas = json_encode(array_diff_key($processed, array_flip(['type', 'name'])));
      $newMediaId = NULL;

      if (is_null($newMediaId) && isset($processed['media_id'])) {
        $newMediaId = $processed['media_id'];
      }

      if (is_null($newMediaId) && (isset($_FILES['image']) && !empty($_FILES['image']['tmp_name'])) && !isset($processed['media_id'])) {
        $newMediaId = $this->upload_file($_FILES['image']);
      }
      var_dump($articleId);
      if ($action === 'create') {
        Blocks::create(
          $processed['name'],
          $jsonDatas,
          $articleId,
          $processed['type'],
          1, /* TBM */
          $newMediaId
        );
      } elseif ($action === 'edit') {
        Blocks::update(
          $blockId,
          $processed['name'],
          $jsonDatas,
          $articleId,
          $processed['type'],
          1, /* TBM */
          $newMediaId
        );
      }

      $this->redirect("admin/articles/blocks/$articleId");
    } catch (\Exception $e) {
      $this->render("Blocks/$action", ['errors' => [$e->getMessage()]]);
    }
  }
}

<?php

namespace Controller\AdminSubControllers;

use Controller\AdminController;
use Core\FieldChecker;
use Model\Blocks;
use Model\Medias;

/**
 * AdminBlocksController | Manage blocks in the admin panel
 */
class AdminBlocksController extends AdminController
{
  public $name = 'Admin - Blocks';
  public $description = 'Handles all requests related to blocks in the admin panel.';

  /**
   * Validates the fields of a block.
   *
   * @param array $block_fields Should contain arrays like: ['name' => $field_name, 'content' => $field_content,'min" => $min_length, 'max" => $max_length]
   * @param Medias $media
   *
   * @throws \Exception
   */
  private function validateBlockFields($block_fields = [], $media)
  {
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
   * Gets a block by its ID.
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
   * @param int $blockId
   */
  private function handleBlockAction($action, $blockId)
  {
    $this->requiresValidID('blocks');
    if ($action === 'delete') {
      $this->delete_block([
        'id' => $blockId
      ]);
    }

    // Uncomment this part if rendering with block information is needed.
    /*
        $block = $this->getBlockById($blockId);
        $allCategories = Categories::getAllCategories();
        $this->render("Blocks/$action", ['block' => $block, 'categories' => $allCategories]);
        */
  }

  /**
   * Handles various actions related to blocks (create, edit, delete, list).
   *
   * @param array $params
   */
  public function blocks($params)
  {
    $additionalParams = $this->parseOptParam();

    $action = $additionalParams['action'];
    $blockId = $additionalParams['id'];

    switch ($action) {
      case 'edit':
        $this->requiresValidID('blocks');
        $this->handleBlockAction('edit', $blockId);
        break;
      case 'delete':
        $this->requiresValidID('blocks');
        $this->handleBlockAction('delete', $blockId);
        break;
      case 'blocks':
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
  public function create_block($params)
  {
    $this->handleCreateOrEdit($params, 'create');
  }

  /**
   * Handles the edition of blocks.
   *
   * @param array $params
   */
  public function edit_block($params)
  {
    $this->handleCreateOrEdit($params, 'edit');
  }

  /**
   * Handles the deletion of blocks.
   *
   * @param array $params
   */
  public function delete_block($params)
  {
    $blockId = FieldChecker::cleanInt(explode('/', $this->requestContext->getOptParam())[1]);
    try {
      $idarticle = $this->getBlockById($blockId)->getArticleId();
      Blocks::delete($blockId);
      $this->addMessage('The block has been successfully deleted!');
      $this->redirect("admin/articles/blocks/$idarticle");
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
  private function handleCreateOrEdit($params, $action)
  {
    $blockId = FieldChecker::cleanInt($this->requestContext->getOptParam());
    $articleId = $blockId;

    $block = NULL;
    if ($action === 'edit') {
      $block = $this->getBlockById($blockId);
      $articleId = $block->getArticleId();
    }

    try {
      $processed = $this->process_fields();
      $jsonDatas = json_encode(array_diff_key($processed, array_flip(['type', 'name'])));
      $newMediaId = NULL;

      if (!empty($blockId) && !is_null($block)) {
        $block = Blocks::getBlock($blockId);
        if (!is_null($block) && !is_null($block->getMedia())) {
          $newMediaId = $block->getMedia()->getId();
        }
      }

      if (isset($processed['media_id'])) {
        var_dump($processed['media_id'], "media_id is set du truc");
        $newMediaId = $processed['media_id'];
      }

      if ((isset($_FILES['block_src']) && !empty($_FILES['block_src']['tmp_name'])) && !isset($processed['media_id'])) {
        $newMediaId = $this->upload_file($_FILES['block_src']);
      }

      if ($action === 'create') {
        Blocks::create(
          $processed['name'],
          $jsonDatas,
          $articleId,
          $processed['type'],
          !is_null($block) ? $block->getWeight() : 1,
          $newMediaId
        );
      } elseif ($action === 'edit') {
        Blocks::update(
          $blockId,
          $processed['name'],
          $jsonDatas,
          $articleId,
          $processed['type'],
          !is_null($block) ? $block->getWeight() : 1,
          $newMediaId
        );
      }

      $this->addMessage("The block has been successfully $action-ed!");
      $this->redirect("admin/articles/blocks/{$articleId}");
    } catch (\Exception $e) {
      $this->render("Blocks/$action", ['errors' => [$e->getMessage()]]);
    }
  }
}

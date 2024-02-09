<?php

namespace Drupal\ai_search\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class AiSearchController.
 */
class AiSearchController extends ControllerBase {

  /**
   * Content.
   *
   * @return string
   *   Return Hello string.
   */
  public function content($request, $response) {
    $data = $request->getParsedBody();
    
    return [
      '#type' => 'markup',
      '#markup' => $this->t('Implement method: content')
    ];
  }

  
}

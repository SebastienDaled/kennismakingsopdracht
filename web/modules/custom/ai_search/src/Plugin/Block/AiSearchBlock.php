<?php

namespace Drupal\ai_search\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'AiSearchBlock' block.
 *
 * @Block(
 *  id = "ai_search_block",
 *  admin_label = @Translation("AI Search block"),
 * )
 */

class AiSearchBlock extends BlockBase {

  /**
   * x@xxxxxxxxxxx
   */
  public function build() {
    $form = \Drupal::formBuilder()->getForm('Drupal\ai_search\Form\AiSearchForm');
    return $form;
  }

}
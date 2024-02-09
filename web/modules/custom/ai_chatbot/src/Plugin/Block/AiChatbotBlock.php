<?php

namespace Drupal\ai_chatbot\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'AiChatbotBlock' block.
 *
 * @Block(
 *  id = "ai_chatbot_block",
 *  admin_label = @Translation("AI Chatbot block"),
 * )
 */
class AiChatbotBlock extends BlockBase {

  /**
   * x@xxxxxxxxxxx
   */
  public function build() {
    $form = \Drupal::formBuilder()->getForm('Drupal\ai_chatbot\Form\AiChatbotForm');
    return $form;
  }

}
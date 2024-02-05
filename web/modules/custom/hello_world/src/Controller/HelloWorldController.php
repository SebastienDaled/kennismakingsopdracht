<?php
// modules/custom/hello_world/src/Controller/HelloWorldController.php

namespace Drupal\hello_world\Controller;

use Drupal\Core\Controller\ControllerBase;

class HelloWorldController extends ControllerBase {

  /**
   * {@inheritdoc}
   */
  public function content() {
    $output = [
      '#markup' => $this->t('Hello, World!'),
    ];

    return $output;
  }

}

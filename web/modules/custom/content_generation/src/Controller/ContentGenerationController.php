<?php

namespace Drupal\content_generation\Controller;

use Drupal\content_generation\Form\ContentGenerationForm;
use Drupal\Core\Controller\ControllerBase;

class ContentGenerationController extends ControllerBase {

  /**
   * {@inheritdoc}
   */
  public function content() {
    // Build the form array.
    $form = $this->formBuilder()->getForm(ContentGenerationForm::class);

    // Return the form.
    return $form;
  }

}


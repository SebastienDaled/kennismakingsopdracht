<?php

namespace Drupal\content_generation\Controller;

use Drupal\content_generation\Form\ContentDraftForm;
use Drupal\content_generation\Form\ContentGenerationForm;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Tests\Component\Annotation\Doctrine\Fixtures\Annotation\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;



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

  public function contentDraft(RouteMatchInterface $route_match, Request $request) {
    
    $form = $this->formBuilder()->getForm(ContentDraftForm::class);

    return $form;
    
  }

}


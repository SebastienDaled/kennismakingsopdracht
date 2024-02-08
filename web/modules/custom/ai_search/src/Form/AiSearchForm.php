<?php

namespace Drupal\ai_search\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Site\Settings;
use GuzzleHttp\Client;
use Drupal\media\Entity\Media;
use Drupal\node\Entity\Node;
use Drupal\Core\File\FileSystemInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AiSearchForm extends FormBase {

  /**
   * x@xxxxxxxxxxx
   */
  public function getFormId() {
    return 'ai_search_form';
  }

  /**
   * x@xxxxxxxxxxx
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // a title
    $form['title'] = [
      '#type' => 'item',
      '#title' => $this->t('Search for content with AI'),
      '#attributes' => [
        'class' => ['form-title']
      ]
    ];
    $form['prompt'] = [
      '#type' => 'textarea',
      '#placeholder' => 'Write a prompt for the AI to generate content.',
      '#title' => $this->t('Prompt'),
      '#required' => TRUE,
    ];
    // submit
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Search'),
    ];
    return $form;
  }

  /**
   * x@xxxxxxxxxxx
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $api_key = "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";

    $prompt_user = $form_state->getValue('prompt');

    $prompt_relevant_content = "search for relevant content by the following prompt:" . $prompt_user;
    // get all content from articles, news and offices
    $all_content_query = \Drupal::entityQuery('node')
      ->accessCheck(FALSE)
      ->condition('type', ['articles', 'news', 'offices'], 'IN')
      ->execute();
    $all_content = Node::loadMultiple($all_content_query);



    $redirect = new RedirectResponse('/search-ai');
    $redirect->send();
  }

}

// go throug all content from articles, news and offices by the promt given in the form and return the content that is relevant to that search use AI
// return the content in a list with the title and the content
// function search_content($prompt, $api_key) {
//   $client = new Client();
//   $response = $client->request('GET', 'https://api.openai.com/v1/engines/davinci/search', [
//     'headers' => [
//       'authorization' => 'Bearer ' . $api_key,
//       'Content-Type' => 'application/json',
//     ],
//     'json' => [
//       'documents' => [
//         'title' => 'article',
//         'content' => 'content',
//       ],
//       'query' => $prompt,
//     ],
//   ]);
//   $content = json_decode($response->getBody());
//   return $content;
// }
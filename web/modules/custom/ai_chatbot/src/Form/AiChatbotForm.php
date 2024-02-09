<?php

namespace Drupal\ai_chatbot\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Site\Settings;
use GuzzleHttp\Client;
use Drupal\media\Entity\Media;
use Drupal\node\Entity\Node;
use Drupal\Core\File\FileSystemInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AiChatbotForm extends FormBase {

  /**
   * x@xxxxxxxxxxx
   */
  public function getFormId() {
    return 'ai_chatbot_form';
  }

  /**
   * x@xxxxxxxxxxx
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // a title
    $form['title'] = [
      '#type' => 'item',
      '#title' => $this->t('Chat with AI'),
      '#attributes' => [
        'class' => ['form-title']
      ]
    ];
    $form['prompt'] = [
      '#type' => 'textarea',
      '#placeholder' => 'Write a prompt for the AI to generate content.',
      '#required' => TRUE,
    ];
    // submit
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Chat'),
    ];

    // $form['result'] = [
    //   '#type' => 'item',
    //   '#title' => $this->t('Result'),
    //   '#attributes' => [
    //     'class' => ['form-result']
    //   ]
    // ];
    return $form;
  }

  /**
   * x@xxxxxxxxxxx
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $nodes_offices = \Drupal::entityTypeManager()->getStorage('node')->loadByProperties(['type' => 'offices']);
    $nodes_news = \Drupal::entityTypeManager()->getStorage('node')->loadByProperties(['type' => 'news']);
    $nodes_articles = \Drupal::entityTypeManager()->getStorage('node')->loadByProperties(['type' => 'article']);

    $office_values = [];
    foreach ($nodes_offices as $node) {
      // dd($node->values['body']);
      // $office_values[$node->id()] = $node->getTitle();
    }

    $nodes = array_merge($nodes_offices, $nodes_news, $nodes_articles);
    
    dd($nodes);

    // $api_key = "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";

    // $prompt_user = $form_state->getValue('prompt');

    // // index all content from articles, news and offices
    // $all_content_query = \Drupal::entityQuery('node')
    //   ->accessCheck(FALSE)
    //   ->condition('type', ['article', 'news', 'office'], 'IN')
    //   ->execute();

    // $all_content = Node::loadMultiple($all_content_query);
    // $content_string = "";
    // // get the body of each content into a string
    // foreach ($all_content as $content) {
    //   $body = $content->get('body')->value;
    //   $content_string .= $body;
    // }
    // // dd($content_string);
    
    // $prompt_ai = "this is al the content that needs to be searched: " . $content_string . ". the following prompt asks for more info about something from the content" . $prompt_user;
    // // $prompt_ai = $prompt_user . ": " . $content_string;

    // $response = get_ai_response($prompt_ai, $api_key);
    

    // $form['result'] = [
    //   '#type' => 'item',
    //   '#markup' => $response,
    //   '#attributes' => [
    //     'class' => ['form-result']
    //   ]
    // ];

  }
}

// function get_ai_response($prompt, $api_key) {
//   $url = 'https://api.openai.com/v1/completions';
//   $client = new Client();
//   $model = "gpt-3.5-turbo-instruct";

//   $response = $client->post($url, [
//     'headers' => [
//       'Authorization' => 'Bearer ' . $api_key,
//       'Content-Type' => 'application/json',
//     ],
//     'json' => [
//       'prompt' => $prompt,
//       'temperature' => 0.7,
//       'max_tokens' => 150,
//       'model' => $model,
//     ],
//   ]);
//   $response = json_decode($response->getBody()->getContents())->choices[0]->text;
//   dd($response);
//   return $response;
// }
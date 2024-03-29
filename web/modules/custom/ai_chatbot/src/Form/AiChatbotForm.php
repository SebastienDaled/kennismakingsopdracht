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
use Symfony\Component\Validator\Constraints\All;

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
      '#value' => $this->t('Ask'),
    ];

    // get the query from the url
    $query = \Drupal::request()->query->get('response');
    $question = \Drupal::request()->query->get('question');

    // if there is a query, show it in the form
    if ($query) {
      $form['response'] = [
        '#type' => 'item',
        '#title' => $this->t('Result: '),
        '#markup' => "<p>" . $question . '<p/><p class="ai_result">' . $query . '</p>',
        '#attributes' => [
          'class' => ['form-result']
        ]
      ];
    } else {
      $form['response'] = [
        '#type' => 'item',
        '#title' => $this->t('Result: '),
        '#markup' => '<p class="ai_result">ask a question first.</p>',
        '#attributes' => [
          'class' => ['form-result']
        ]
      ];
    }

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
    $nodes_news = \Drupal::entityTypeManager()->getStorage('node')->loadByProperties(['type' => 'news']);
    $nodes_articles = \Drupal::entityTypeManager()->getStorage('node')->loadByProperties(['type' => 'article']);

    // make an object with all the bodys fromeach content type
    $all_data = [];

    // Counter for creating keys
    $counter = 1;

    foreach($nodes_news as $news) {
        $body_value = $news->body->value;

        $key = 'news' . $counter;

        $all_data[$key] = $body_value;

        $counter++;
    }
    $counter = 1;
    foreach($nodes_articles as $article) {
      $body_value = $article->body->value;

      $key = 'article' . $counter;

      $all_data[$key] = $body_value;

      $counter++;
    }

    $json_all_data = json_encode($all_data);

    $api_key = Settings::get('api_key');

    $prompt_user = $form_state->getValue('prompt');
    
    $prompt_ai = "there is a json object with data, you need to you to answer the question that is being asked. the jsonobject has 'news1', 'news2,... & 'article1', 'article2',... each one has text that contains info about article or news. the question being asked to be anwered with the data in the json format. json data: " . $json_all_data . ". question: " . $prompt_user . ".";
    
    $response = get_ai_response($prompt_ai, $api_key);
    
    // set response in the query in the url bar
    $response = urlencode($response);
    $response = new RedirectResponse('/search?response=' . $response . "&question=". $prompt_user);
    $response->send();
    dd($response);
  }
}

function get_ai_response($prompt, $api_key) {
  $url = 'https://api.openai.com/v1/completions';
  $client = new Client();
  $model = "gpt-3.5-turbo-instruct";

  $response = $client->post($url, [
    'headers' => [
      'Authorization' => 'Bearer ' . $api_key,
      'Content-Type' => 'application/json',
    ],
    'json' => [
      'prompt' => $prompt,
      'temperature' => 0.7,
      'max_tokens' => 150,
      'model' => $model,
    ],
  ]);
  $response = json_decode($response->getBody()->getContents())->choices[0]->text;
  return $response;
}
<?php

namespace Drupal\content_generation\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Site\Settings;
use GuzzleHttp\Client;
use Drupal\media\Entity\Media; 
use Drupal\node\Entity\Node;
use Drupal\Core\File\FileSystemInterface;

class ContentGenerationForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'content_generation_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // a title
    $form['title'] = [
      '#type' => 'item',
      '#title' => $this->t('Generate content with AI'),
      '#attributes' => [
        'class' => ['form-title']
      ]
    ];
    $form['content_type'] = [
      '#type' => 'select',
      '#title' => $this->t('Content type'),
      '#options' => [
        'article' => $this->t('Article'),
        'news' => $this->t('News'),
      ],
      '#required' => TRUE,
    ];
    $form['prompt'] = [
      '#type' => 'textarea',
      '#placeholder' => 'Write a prompt for the AI to generate content.',
      '#title' => $this->t('Prompt'),
      '#required' => TRUE,
    ];
    // offices
    $form['subtitle'] = [
      '#type' => 'item',
      '#title' => $this->t('optional: only for articles'),
      '#attributes' => [
        'class' => ['form-title']
      ]
    ];
    $form['offices'] = [
      '#type' => 'entity_autocomplete',
      '#title' => $this->t('Offices'),
      '#target_type' => 'node',
      '#selection_settings' => [
        'target_bundles' => ['offices'],
      ],
    ];
    $form['taxonomy'] = [
      '#type' => 'entity_autocomplete',
      '#title' => $this->t('Tags'),
      '#target_type' => 'taxonomy_term',
      '#selection_settings' => [
        'target_bundles' => ['tags'],
      ],
      '#tags' => TRUE,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
      '#attributes' => [
        'class' => ['btn-ai']
      ]
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */

  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    // api key
    $api_key = "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";

    $content_type = $form_state->getValue('content_type');
    $prompt = $form_state->getValue('prompt');

    $prompt_title = "make a title for a " . $content_type . " about " . $prompt;
    $prompt_text = "write a text for a " . $content_type . " about " . $prompt;
    $prompt_image = "make an image for a " . $content_type . " about " . $prompt;

    $image_url = get_openai_image($prompt_image, $api_key);
    $title = get_openai_post($prompt_title, 20, $api_key);
    $text = get_openai_post($prompt_text, 150, $api_key);

    $data = file_get_contents($image_url);
    $file = \Drupal::service('file.repository')->writeData($data, 'public://image.png', FileSystemInterface::EXISTS_RENAME);

    $media = Media::create([
      'bundle' => 'image',
      'name' => 'Image for ' . $title,
      'field_media_image' => [
        'target_id' => $file->id(),
        'alt' => 'Image for ' . $title,
      ],
    ]);

    $media->save();


    if ($content_type == 'article') {
      $node = Node::create([
        'type' => $content_type,
        'title' => $title,
        'body' => [
          'value' => $text,
        ],
        'field_tags' => $form_state->getValue('taxonomy'),
        'field_offices' => $form_state->getValue('offices'),
        'field_media_image' => [
          'target_id' => $media->id(),
        ],
      ]);

      $node->save();
    } else {
      $node = Node::create([
        'type' => $content_type,
        'title' => $title,
        'body' => [
          'value' => $text,
        ],
        'field_media_image' => [
          'target_id' => $media->id(),
        ],
      ]);
      
      $node->save();
    }

    

    print $node->toUrl('canonical', ['absolute' => TRUE])->toString() . "\n";


  }
  
}

/**
 * Get OpenAI Recipe
 *
 * @param string $prompt
 * @param integer $max_tokens
 * @param string $api_key
 * @return string
 */
function get_openai_post($prompt, $max_tokens, $api_key) {
  $url = 'https://api.openai.com/v1/completions';
  $temperature = 0.7;
  $model = 'gpt-3.5-turbo-instruct';

  $client = new Client();
  $response = $client->request('POST', $url, [
      'headers' => [
          'Authorization' => 'Bearer ' . $api_key,
          'Content-Type' => 'application/json',
      ],
      'json' => [
          'prompt' => $prompt,
          'temperature' => $temperature,
          'max_tokens' => $max_tokens,
          'model' =>  $model,
      ],
  ]);

  return json_decode($response->getBody()->getContents())->choices[0]->text;
}


/**
 * Get OpenAI Image
 *
 * @param string $prompt
 * @param string $api_key
 * @return string
 */
function get_openai_image($prompt, $api_key) {
  $url = 'https://api.openai.com/v1/images/generations';

  $client = new Client();
  $response = $client->request('POST', $url, [
      'headers' => [
          'Authorization' => 'Bearer ' . $api_key,
          'Content-Type' => 'application/json',
      ],
      'json' => [
          'prompt' => $prompt,
          'n' => 1,
          'size' => '512x512',
      ],
  ]);

  return json_decode($response->getBody()->getContents())->data[0]->url;
}
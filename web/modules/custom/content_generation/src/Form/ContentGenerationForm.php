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
        'offices' => $this->t('Office'),
      ],
      '#required' => TRUE,
    ];
    $form['prompt'] = [
      '#type' => 'textarea',
      '#placeholder' => 'Write a prompt for the AI to generate content.',
      '#title' => $this->t('Prompt'),
    ];
    $form['subtitle'] = [
      '#type' => 'item',
      '#title' => $this->t('Add an Office and a Tag'),
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
    $form['country'] = [
      '#type' => 'entity_autocomplete',
      '#title' => $this->t('Country'),
      '#target_type' => 'taxonomy_term',
      '#selection_settings' => [
        'target_bundles' => ['country'],
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
   * 
   */

  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    // the api key
    $api_key = Settings::get('api_key');
    // dd($api_key);

    // retreiving the values from the form
    $content_type = $form_state->getValue('content_type');
    $prompt = $form_state->getValue('prompt');
    $country = $form_state->getValue('country');

    // making the prompts for the ai
    // --- article, news
    $prompt_title = "make a title for a " . $content_type . " about " . $prompt;
    $prompt_text = "write a text for a " . $content_type . " about " . $prompt;
    $prompt_image = "make an image for a " . $content_type . " about " . $prompt;

    // --- office
    $prompt_all_info = "make a telephone, fax, email, address and contact name for " . $country . "and return it in an object in json format";

    // creating the content
    if ($content_type == 'article') {

      // get the title and text from openai
      $image_url = get_openai_image($prompt_image, $api_key);
      $title = get_openai_post($prompt_title, 20, $api_key);
      $text = get_openai_post($prompt_text, 150, $api_key);

      // create the media entity
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

      // create the node
      $node = Node::create([
        'type' => $content_type,
        'title' => $title,
        'body' => [
          'value' => $text,
          "format" => "restricted_html",
        ],
        'field_tags' => [
          'target_id' => $form_state->getValue('taxonomy')[0]['target_id'],
        ],
        'field_offices' => [
          'target_id' => $form_state->getValue('offices'),
        ],
        'field_media_image' => [
          'target_id' => $media->id(),
        ],
      ]);

      $node->save();

    } else if ($content_type == 'offices') {
      $country_name = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($country[0]['target_id']);
      $country_name = $country_name->name->value;

      // get the telephone, fax, email, address and contact from openai
      $all_info = get_openai_post($prompt_all_info, 70, $api_key);
      $all_info = json_decode($all_info);

      $image_prompt = "make an image for an office in " . $country_name;
      $image_url = get_openai_image($image_prompt, $api_key);

      // create the media entity
      $data = file_get_contents($image_url);
      $file = \Drupal::service('file.repository')->writeData($data, 'public://image.png', FileSystemInterface::EXISTS_RENAME);

      $media = Media::create([
        'bundle' => 'image',
        'name' => 'Image for ' . $country_name,
        'field_media_image' => [
          'target_id' => $file->id(),
          'alt' => 'Image for ' . $country_name,
        ],
      ]);

      $media->save();
      
      // create the node
      $node = Node::create([
        'type' => $content_type,
        'title' => "Office " . $country_name,
        'field_telephone_number' => $all_info->telephone,
        'field_fax' => $all_info->fax,
        'field_email' => $all_info->email,
        'field_adres' => $all_info->address,
        'field_contact_person' => $all_info->contact_name,
        'field_country' => [
          'target_id' => $country[0]['target_id'],
        ],
        'field_media_image' => [
          'target_id' => $media->id(),
        ],
      ]);

      $node->save();

    } else if ($content_type == 'news'){
      // get the title and text from openai
      $image_url = get_openai_image($prompt_image, $api_key);
      $title = get_openai_post($prompt_title, 20, $api_key);
      $text = get_openai_post($prompt_text, 150, $api_key);

      // create the media entity
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

      // create the node
      $node = Node::create([
        'type' => $content_type,
        'title' => $title,
        'body' => [
          'value' => $text,
          "format" => "restricted_html",
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
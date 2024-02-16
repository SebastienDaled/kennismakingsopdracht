<?php

namespace Drupal\content_generation\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Site\Settings;
use GuzzleHttp\Client;
use Drupal\media\Entity\Media; 
use Drupal\node\Entity\Node;
use Drupal\Core\File\FileSystemInterface;
use Exception;

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
    session_start();
    // the api key
    $api_key = Settings::get('api_key');
    // dd($api_key);

    // retreiving the values from the form
    $content_type = $form_state->getValue('content_type');
    $prompt = $form_state->getValue('prompt');
    $country = $form_state->getValue('country');

    // making the prompts for the ai
    // --- article, news
    $prompt_title = $this->t("make a title for a " . $content_type . " about " . $prompt);
    $prompt_text = $this->t("write a text for a " . $content_type . " about " . $prompt);
    $prompt_image = $this->t("make an image for a " . $content_type . " about " . $prompt);

    // --- office
    // $prompt_all_info = "make a telephone, fax, email, address and contact name for " . $country . "and return it in an object in json format";
    // t();
    $prompt_all_info = $this->t("make a telephone, fax, email, address and contact name for @country and return it in an object in json format", ['@country' => $country]);

    // creating the content
    if ($content_type == 'article') {

      // get the title and text from openai
      $image_url = get_openai_image($prompt_image, $api_key);
      $title = get_openai_post($prompt_title, 20, $api_key);
      $text = get_openai_post($prompt_text, 150, $api_key);

      // create the media entity
      $data = file_get_contents($image_url);
      $file = \Drupal::service('file.repository')->writeData($data, 'public://image.png', FileSystemInterface::EXISTS_RENAME);

      // save the image in media
      $media = Media::create([
        'bundle' => 'image',
        'name' => 'Image for ' . $title,
        'field_media_image' => [
          'target_id' => $file->id(),
          'alt' => 'Image for ' . $title,
        ],
      ]);

      $media->save();

      // creating the object to send to /draft
      $json = json_encode([
        'title' => $title,
        'text' => $text,
        'image' => $media->id(),
        'tags' => $form_state->getValue('taxonomy')[0]['target_id'],
        'offices' => $form_state->getValue('offices'),
      ]);

    } else if ($content_type == 'offices') {
      $country_name = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($country[0]['target_id']);
      $country_name = $country_name->name->value;

      // get the telephone, fax, email, address and contact from openai
      $all_info = get_openai_post($prompt_all_info, 70, $api_key);
      $all_info = json_decode($all_info);
      $all_info->country_name = $country_name;
      $all_info->country = $country[0]['target_id'];

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

      $all_info->image = $media->id();


      // make a json object with the information
      $json = json_encode($all_info);

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

      $json = json_encode([
        'title' => $title,
        'text' => $text,
        'image' => $media->id(),
      ]);
    }

    // redirect to the /draft page
    $form_state->setRedirect('draft.content', ['prompt' => $json, 'content_type' => $content_type]);

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
  try {
    $url = Settings::get('openai_model_completion');
    $temperature = Settings::get('openai_temprature');
    $model = Settings::get('openai_model');

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

    // Check if the response was successful (status code 2xx)
    if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
        return json_decode($response->getBody()->getContents())->choices[0]->text;
    } else {
        // Handle non-successful response here
        throw new Exception('OpenAI API request failed with status code: ' . $response->getStatusCode());
    }
  } catch (Exception $e) {
    // Handle exceptions here
    // You can log the error, show a user-friendly message, or take any other appropriate action.
    return 'Error: ' . $e->getMessage();
  }
}



/**
 * Get OpenAI Image
 *
 * @param string $prompt
 * @param string $api_key
 * @return string
 */
function get_openai_image($prompt, $api_key) {
  try {
    $url = Settings::get('openai_image_generation');

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

    // Check if the response was successful (status code 2xx)
    if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
        return json_decode($response->getBody()->getContents())->data[0]->url;
    } else {
        // Handle non-successful response here
        throw new Exception('OpenAI image generation request failed with status code: ' . $response->getStatusCode());
    }
  } catch (Exception $e) {
    // Handle exceptions here
    // You can log the error, show a user-friendly message, or take any other appropriate action.
    return 'Error: ' . $e->getMessage();
  }
}

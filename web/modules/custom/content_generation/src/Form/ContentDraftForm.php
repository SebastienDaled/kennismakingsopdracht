<?php 

namespace Drupal\content_generation\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Site\Settings;
use GuzzleHttp\Client;
use Drupal\media\Entity\Media;
use Drupal\node\Entity\Node;
use Drupal\Core\File\FileSystemInterface;
use Drupal\webform\Plugin\WebformElement\Telephone;

class ContentDraftForm extends FormBase {

  /**
   * x@xxxxxxxxxxx
   */
  public function getFormId() {
    return 'content_draft_form';
  }

  /**
   * x@xxxxxxxxxxx
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // retrieve the prompt and content type from the query string
    $params = \Drupal::request()->query->all();

    // decode the prompt
    $info = json_decode($params['prompt']);
    // retrieve content type
    $content_type = $params['content_type'];
    // retrieve the media
    $media = Media::load($info->image);

    // build the form each depending on the content type
    if ($content_type === "offices") {
      $form['title'] = [
        '#type' => 'item',
        '#title' => $this->t('draft form for ' . $content_type ),
        '#attributes' => [
          'class' => ['form-title']
        ]
      ];
      $form['image'] = [
        '#type' => 'hidden',
        '#value' => $media->id(),
      ];
      $form['telephone'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Telephone'),
        '#required' => TRUE,
        '#value' => $info->telephone,
      ];
      $form['fax'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Fax'),
        '#required' => TRUE,
        '#value' => $info->fax,
      ];
      $form['email'] = [
        '#type' => 'email',
        '#title' => $this->t('Email'),
        '#required' => TRUE,
        '#value' => $info->email,
      ];
      $form['adress'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Adress'),
        '#required' => TRUE,
        '#value' => $info->address,
      ];
      $form['contact_name'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Contact Name'),
        '#required' => TRUE,
        '#value' => $info->contact_name,
      ];
      $form['country'] = [
        '#type' => 'hidden',
        '#value' => $info->country,
      ];
      $form['country_name'] = [
        '#type' => 'hidden',
        '#value' => $info->country_name,
      ];
      $form['content_type'] = [
        '#type' => 'hidden',
        '#value' => $content_type,
      ];
      $form['submit'] = [
        '#type' => 'submit',
        '#value' => $this->t('Submit'),
      ];
    } else if ($content_type === "article") {
      $form['title'] = [
        '#type' => 'item',
        '#title' => $this->t('draft form for ' . $content_type ),
        '#attributes' => [
          'class' => ['form-title']
        ]
      ];
      $form['title'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Title'),
        '#required' => TRUE,
        '#value' => $info->title,
      ];
      $form['text'] = [
        '#type' => 'textarea',
        '#title' => $this->t('Text'),
        '#required' => TRUE,
        '#value' => $info->text,
      ];
      $form['image'] = [
        '#type' => 'hidden',
        '#value' => $media->id(),
      ];
      $form['tags'] = [
        '#type' => 'hidden',
        '#value' => $info->tags,
      ];  
      $form['content_type'] = [
        '#type' => 'hidden',
        '#value' => $content_type,
      ];
      $form['offices'] = [
        '#type' => 'hidden',
        '#value' => $info->offices,
      ];
      $form['submit'] = [
        '#type' => 'submit',
        '#value' => $this->t('Submit'),
      ];
    } else if ($content_type === "news") {
      $form['title'] = [
        '#type' => 'item',
        '#title' => $this->t('draft form for ' . $content_type ),
        '#attributes' => [
          'class' => ['form-title']
        ]
      ];
      $form['title'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Title'),
        '#required' => TRUE,
        '#value' => $info->title,
      ];
      $form['text'] = [
        '#type' => 'textarea',
        '#title' => $this->t('Text'),
        '#required' => TRUE,
        '#value' => $info->text,
      ];
      $form['image'] = [
        '#type' => 'hidden',
        '#value' => $media->id(),
      ];
      $form['content_type'] = [
        '#type' => 'hidden',
        '#value' => $content_type,
      ];
      $form['submit'] = [
        '#type' => 'submit',
        '#value' => $this->t('Submit'),
      ];
    } 

   
    return $form;
  }

  /**
   * x@xxxxxxxxxxx
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // retrieve the content type from the form
    $content_type = $form_state->getValue('content_type');

    // create the node depending on the content type
    if ($content_type === "offices") {
      $telephone = $form_state->getValue('telephone');
      $fax = $form_state->getValue('fax');
      $email = $form_state->getValue('email');
      $adress = $form_state->getValue('adress');
      $contact_name = $form_state->getValue('contact_name');
      $country = $form_state->getValue('country');
      $country_name = $form_state->getValue('country_name');
      $image = $form_state->getValue('image');

      $node = Node::create([
        'type' => $content_type,
        'title' => "Office " . $country_name,
        'field_telephone_number' => $telephone,
        'field_fax' => $fax,
        'field_email' => $email,
        'field_adres' => $adress,
        'field_contact_name' => $contact_name,
        'field_country' => ['target_id' => $country],
        'field_media_image' => ['target_id' => $image],
      ]);
      $node->save();

    } else if ($content_type === "article") {
      $title = $form_state->getValue('title');
      $text = $form_state->getValue('text');
      $image = $form_state->getValue('image');
      $tags = $form_state->getValue('tags');
      $office = $form_state->getValue('offices');

      $node = Node::create([
        'type' => $content_type,
        'title' => $title,
        'body' => [
          'value' => $text,
          "format" => "restricted_html",
        ],
        'field_tags' => [
          'target_id' => $tags,
        ],
        'field_offices' => [
          'target_id' => $office,
        ],
        'field_media_image' => [
          'target_id' => $image,
        ],
      ]);
      $node->save();

    } else if ($content_type === "news") {
      $title = $form_state->getValue('title');
      $text = $form_state->getValue('text');
      $image = $form_state->getValue('image');

      $node = Node::create([
        'type' => $content_type,
        'title' => $title,
        'body' => [
          'value' => $text,
          "format" => "restricted_html",
        ],
        'field_media_image' => [
          'target_id' => $image,
        ],
      ]);

      $node->save();
    }

    // redirect to the content page
    $form_state->setRedirect('content_generation.content');
  }
}
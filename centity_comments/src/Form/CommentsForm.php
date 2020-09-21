<?php

namespace Drupal\centity_comments\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\RedirectCommand;
use Drupal\Core\Ajax\HtmlCommand;

/**
 * Form controller for the centity entity edit forms.
 *
 * @ingroup centity
 */
class CommentsForm extends ContentEntityForm {
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var $entity \Drupal\centity\Entity\Centity */
    $form = parent::buildForm($form, $form_state);

    return $form;
  }

  public function actions(array $form, FormStateInterface $form_state) {
    parent::actions($form, $form_state);

    $actions['submit'] = [
      '#type' => 'submit',
      '#value' => 'Save',
      "#submit" => ['::submitForm', '::save'],
      '#ajax' => [
        'callback' => '::ajaxSubmitCallback',
        'event' => 'click',
      ],
    ];

    return $actions;
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    $this->cleanTagsFromInput($form_state->getValue('name')[0]['value']);

    // Phone number validation. Example: (123) 123-1234.
    $phone = $form_state->getValue('phone')[0]['value'];
    if (isset($phone)) {
      $preg = "/\(\d{3}\) \d{3}-\d{4}/";
      preg_match($preg, $phone, $resultPhone);
      if ($resultPhone == FALSE) {
        $form_state->setErrorByName(
          'phone',
          $this->t(
            "The phone number is not correct.<br>Example phone number: '(000) 000-0000'."
          )
        );
      }
    }
  }

  public function ajaxSubmitCallback(array &$form, FormStateInterface $form_state) {

    $response = new AjaxResponse();

    $addmessage = [
      '#theme' => 'status_messages',
      '#message_list' => drupal_get_messages(),
      '#status_headings' => [
        'status' => t('Status message'),
        'error' => t('Error message'),
        'warning' => t('Warning message'),
      ],
    ];

    // Rendering different types of messages.
    $updateForm = \Drupal::service('renderer')->render($addmessage);

    $isEdit = \Drupal::routeMatch()->getParameter('centity');
    if ($isEdit != NULL) {
      $editID = $isEdit->id();
      $link = "comments/{$editID}/edit";
      $edits = strstr($_SERVER['REQUEST_URI'], $link);
    } else {
      $edits = FALSE;
    }

    if ($form_state->hasAnyErrors()) {
      $response->addCommand(
        new HtmlCommand('#form-system-messages', $updateForm)
      );
    }
    else {
      $response->addCommand(new RedirectCommand("/" . 'comments'));
      if ($edits == FALSE) {
        \Drupal::messenger()->addMessage(
          "Comment '{$form_state->getValue('name')[0]['value']}' has been created!",
          'status'
        );
      }
      else {
        \Drupal::messenger()->addMessage(
          "Comment '{$form_state->getValue('name')[0]['value']}' has been edited!",
          'status'
        );
      }
    }

    return $response;
  }

  public function cleanTagsFromInput($value) {
    $value = trim($value);
    $value = stripslashes($value);
    $value = strip_tags($value);
    $value = htmlspecialchars($value);

    return $value;
  }
}

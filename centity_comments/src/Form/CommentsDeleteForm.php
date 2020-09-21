<?php

namespace Drupal\centity_comments\Form;

use Drupal\Core\Entity\ContentEntityConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Provides a form for deleting a content_entity_example entity.
 *
 * @ingroup centity
 */
class CommentsDeleteForm extends ContentEntityConfirmFormBase {
  public function getQuestion() {
    return $this->t(
      'Are you sure you want to delete %name?',
      ['%name' => $this->entity->label()]
    );
  }

  public function getCancelUrl() {
    return new Url('centity_comments.page');
  }

  public function getConfirmText() {
    return $this->t('Delete');
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $entity = $this->getEntity();
    $entity->delete();

    $this->logger('centity')->notice(
      'deleted %title.',
      ['%title' => $this->entity->label()]
    );

    $form_state->setRedirect('centity_comments.page');
  }

}

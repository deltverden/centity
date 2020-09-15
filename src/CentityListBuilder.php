<?php

namespace Drupal\centity;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Url;

/**
 * Provides a list controller for centity entity.
 *
 * @ingroup centity
 */
class CentityListBuilder extends EntityListBuilder {
  /**
   * {@inheritdoc}
   *
   * We override ::render() so that we can add our own content above the table.
   * parent::render() is where EntityListBuilder creates the table using our
   * buildHeader() and buildRow() implementations.
   */
  public function render() {
    $build['description'] = [
      '#markup' => $this->t('
        Centity implements a Comments model.
        These contacts are fieldable entities.
        You can manage the fields on the
        <a href="@adminlink">Contacts admin page</a>.', [
        '@adminlink' => \Drupal::urlGenerator()
          ->generateFromRoute('centity.centity_settings'),
      ]),
    ];

    $build += parent::render();
    return $build;
  }

  /**
   * {@inheritdoc}
   *
   * Building the header and content lines for the contact list.
   *
   * Calling the parent::buildHeader() adds a column for the possible actions
   * and inserts the 'edit' and 'delete' links as defined for the entity type.
   */
  public function buildHeader() {
    $header['id'] = $this->t('ContactID');
    $header['name'] = $this->t('Name');
    $header['email'] = $this->t('Email');
    $header['phone'] = $this->t('Phone');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\centity\Entity\Centity */
    $row['id'] = $entity->id();
    $row['name'] = $entity->link();
    $row['email'] = $entity->email->value;
    $row['phone'] = $entity->phone->value;
    return $row + parent::buildRow($entity);
  }
}

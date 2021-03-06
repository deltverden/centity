<?php

namespace Drupal\centity_comments\Controller;

use Drupal\centity\Entity\Centity;
use Drupal\Core\Controller\ControllerBase;

class Comments extends ControllerBase {
  public function get() {
    $centity = Centity::create();
    $addcomment = \Drupal::service('entity.form_builder')
      ->getForm($centity, 'add');

    $storage = \Drupal::entityTypeManager()
      ->getStorage('centity');
    $query = $storage->getQuery()
      ->pager(2)
      ->sort('id', 'DESC');
    $result = $query->execute();
    $rows = $storage->loadMultiple($result);

    $comments = [];

    foreach ($rows as $row) {
      $avatar = $row->avatar->entity;
      $image = $row->image->entity;
      if ($avatar !== NULL) {
        $avatar = file_url_transform_relative(file_create_url($avatar->getFileUri()));
      } else {
        $avatar = 'default.png';
      }

      if ($image !== NULL) {
        $image = file_url_transform_relative(file_create_url($image->getFileUri()));
      } else {
        $image = '';
      }

      array_push($comments, [
        'id' => $row->id->value,
        'name' => $row->name->value,
        'email' => $row->email->value,
        'phone' => $row->phone->value,
        'text' => $row->text->value,
        'avatar' => $avatar,
        'image' => $image,
      ]);
    }

    $data = [
      'title' => 'Comments',
      'window' => $comments
    ];

    $display[] = [
      '#theme' => 'comments_theme',
      '#comments' => $data,
      '#addcomment' => $addcomment
    ];

    $display['paginate'] = [
      '#type' => 'pager'
    ];

    return $display;
  }
}

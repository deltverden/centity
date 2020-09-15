<?php

namespace Drupal\centity_comments\Controller;

use Drupal\Core\Controller\ControllerBase;

class Comments extends ControllerBase {
  public function get() {
    $storage = \Drupal::entityTypeManager()
      ->getStorage('centity');
    $query = $storage->getQuery();
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

    return [
      '#theme' => 'comments_theme',
      '#comments' => $data
    ];
  }
}

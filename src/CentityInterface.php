<?php

namespace Drupal\centity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\user\EntityOwnerInterface;
use Drupal\Core\Entity\EntityChangedInterface;

interface CentityInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface {

}

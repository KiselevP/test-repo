<?php

namespace Drupal\mirtek;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface defining a mirtek product group entity type.
 */
interface MirtekProductGroupInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface {

}

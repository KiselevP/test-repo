<?php

namespace Drupal\mirtek;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface defining a mirtek product entity type.
 */
interface MirtekProductInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface {

}

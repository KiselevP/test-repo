<?php

namespace Drupal\mirtek;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Defines the access control handler for the testnew entity type.
 */
class MirtekProductAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {

    switch ($operation) {
      case 'view':
        return AccessResult::allowedIfHasPermission($account, 'view mirtek product');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'admister mirtek');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'admister mirtek');

      default:
        // No opinion.
        return AccessResult::neutral();
    }

  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
      return AccessResult::allowedIfHasPermission($account, 'admister mirtek');
  }

}

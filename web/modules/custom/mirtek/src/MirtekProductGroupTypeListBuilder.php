<?php

namespace Drupal\mirtek;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Url;

/**
 * Defines a class to build a listing of mirtek product group type entities.
 *
 * @see \Drupal\mirtek\Entity\MirtekProductGroupType
 */
class MirtekProductGroupTypeListBuilder extends ConfigEntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['title'] = $this->t('Label');

    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row['title'] = [
      'data' => $entity->label(),
      'class' => ['menu-label'],
    ];

    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function render() {
    $build = parent::render();

    $build['table']['#empty'] = $this->t(
      'No mirtek product group types available. <a href=":link">Add mirtek product group type</a>.',
      [':link' => Url::fromRoute('entity.mirtek_product_group_type.add_form')->toString()]
    );

    return $build;
  }

}

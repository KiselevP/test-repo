<?php

namespace Drupal\mirtek;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Url;

/**
 * Defines a class to build a listing of mirtek product type entities.
 *
 * @see \Drupal\mirtek\Entity\MirtekProductType
 */
class MirtekProductTypeListBuilder extends ConfigEntityListBuilder {

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
      'No mirtek product types available. <a href=":link">Add mirtek product type</a>.',
      [':link' => Url::fromRoute('entity.mirtek_product_type.add_form')->toString()]
    );

    return $build;
  }

}

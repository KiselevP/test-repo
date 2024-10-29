<?php

declare(strict_types=1);

namespace Drupal\swiper_formatter\Plugin\Field\FieldFormatter;

use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\File\FileUrlGeneratorInterface;
use Drupal\Core\Routing\RedirectDestinationInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\image\Plugin\Field\FieldFormatter\ImageFormatter;
use Drupal\swiper_formatter\SwiperFormatterTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'Swiper' formatter.
 *
 * @FieldFormatter(
 *   id = "swiper_formatter_image",
 *   label = @Translation("Swiper images"),
 *   field_types = {
 *     "image"
 *   }
 * )
 * @phpstan-consistent-constructor
 */
class SwiperFormatterImage extends ImageFormatter {

  use SwiperFormatterTrait;

  /**
   * {@inheritdoc}
   */
  public function __construct(
    $plugin_id,
    $plugin_definition,
    FieldDefinitionInterface $field_definition,
    array $settings,
    $label,
    $view_mode,
    array $third_party_settings,
    AccountInterface $current_user,
    EntityStorageInterface $image_style_storage,
    FileUrlGeneratorInterface $file_url_generator = NULL,
    protected EntityTypeManagerInterface $entityTypeManager,
    protected EntityFieldManagerInterface $entityFieldManager,
    protected RedirectDestinationInterface $destination) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings, $current_user, $image_style_storage, $file_url_generator);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): SwiperFormatterImage {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('current_user'),
      $container->get('entity_type.manager')->getStorage('image_style'),
      $container->get('file_url_generator'),
      $container->get('entity_type.manager'),
      $container->get('entity_field.manager'),
      $container->get('redirect.destination')
    );
  }

}

<?php

declare(strict_types=1);

namespace Drupal\swiper_formatter\Plugin\Field\FieldFormatter;

use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Routing\RedirectDestinationInterface;
use Drupal\swiper_formatter\SwiperFormatterTrait;
use Drupal\text\Plugin\Field\FieldFormatter\TextDefaultFormatter;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'swiper_formatter_text' formatter variant.
 *
 * @FieldFormatter(
 *   id = "swiper_formatter_text",
 *   label = @Translation("Swiper markup"),
 *   field_types = {
 *     "text",
 *     "text_long",
 *     "text_with_summary"
 *   },
 *   quickedit = {
 *     "editor" = "form"
 *   }
 * )
 * @phpstan-consistent-constructor
 */
class SwiperFormatterText extends TextDefaultFormatter {

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
    protected EntityTypeManagerInterface $entityTypeManager,
    protected EntityFieldManagerInterface $entityFieldManager,
    protected RedirectDestinationInterface $destination) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): SwiperFormatterText {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('entity_type.manager'),
      $container->get('entity_field.manager'),
      $container->get('redirect.destination')
    );
  }

}

<?php

declare(strict_types=1);

namespace Drupal\swiper_formatter;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface defining a Nk tools swiper entity type.
 */
interface SwiperFormatterInterface extends ConfigEntityInterface {

  /**
   * Default Swiper's modules.
   *
   * @var array
   */
  const SWIPER_MODULES = [
    'navigation',
    'pagination',
    'scrollbar',
    'autoplay',
    'lazy',
  ];

  /**
   * Properties that can be assigned to a breakpoint template.
   *
   * @var array
   */
  const BREAKPOINT_OPTIONS = [
    'slidesPerView',
    'spaceBetween',
    'navigation',
    'pagination',
  ];

  /**
   * Returns an array with all of the properties that are Swiper.js options.
   *
   * @param bool $check_breakpoint
   *   When true check on breakpoint templates.
   *
   * @return array
   *   An array with swiper options, keyed by entity id.
   */
  public static function getSwipers(bool $check_breakpoint = FALSE): array;

  /**
   * Sets all the properties that are Swiper.js options into an array.
   *
   * @param array $swiper_options
   *   Swiper template options to set to entity.
   *
   * @return self
   *   Object instance implementing this interface.
   */
  public function setSwiper(array $swiper_options = []): self;

}

<?php

namespace Drupal\twig_typography_test;

use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Controller routines for Twig typography test routes.
 */
class TwigTypographyTestController {
  use StringTranslationTrait;

  /**
   * Menu callback for testing Twig filters in a Twig template.
   */
  public function testFilterRender() {
    return [
      '#theme' => 'twig_typography_test_filter',
      '#message_first' => 'Test "curly double quotes" and \'curly single quotes\'',
      '#message_second' => 'Test for hanging widow',
      '#message_third' => [
        '#markup' => 'Test a "render array"',
      ],
    ];
  }

}

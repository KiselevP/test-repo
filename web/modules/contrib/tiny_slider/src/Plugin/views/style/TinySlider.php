<?php

namespace Drupal\tiny_slider\Plugin\views\style;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\style\StylePluginBase;

/**
 * Style plugin to render each item into TinySlider.
 *
 * @ingroup views_style_plugins
 *
 * @ViewsStyle(
 *     id = "tiny_slider",
 *     title = @Translation("TinySlider"),
 *     help = @Translation("Displays rows as Tiny Slider."),
 *     theme = "tiny_slider_views",
 *     display_types = {"normal"}
 * )
 */
class TinySlider extends StylePluginBase {

  /**
   * Does the style plugin allows to use style plugins.
   *
   * @var bool
   */
  protected $usesRowPlugin = TRUE;

  /**
   * Does the style plugin support custom css class for the rows.
   *
   * @var bool
   */
  protected $usesRowClass = TRUE;

  /**
   * Set default options.
   */
  protected function defineOptions() {
    $options = parent::defineOptions();

    $settings = _tiny_slider_default_settings();
    foreach ($settings as $k => $v) {
      $options[$k] = ['default' => $v];
    }
    return $options;
  }

  /**
   * Render the given style.
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);
    // Items.
    $form['items'] = [
      '#type' => 'number',
      '#step' => '.1',
      '#title' => $this->t('Items'),
      '#default_value' => $this->options['items'],
      '#description' => $this->t('Maximum amount of items displayed at a time with the widest browser width.'),
    ];
    // Margin.
    $form['margin'] = [
      '#type' => 'number',
      '#title' => $this->t('Margin'),
      '#default_value' => $this->options['margin'],
      '#description' => $this->t('Margin from items.'),
    ];
    // Navigation.
    $form['nav'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Navigation'),
      '#default_value' => $this->options['nav'],
      '#description' => $this->t('Controls the display and functionalities of nav components (dots).'),
    ];
    // navPosition.
     $form['navPosition'] = [
      '#type' => 'select',
      '#title' => $this->t('Navigation position'),
      '#options' => [
        'top' => $this->t('Top'),
        'bottom' => $this->t('Bottom'),
      ],
      '#default_value' => $this->options['navPosition'],
      '#description' => $this->t('Display navigation above/below slides.'),
      '#states' => [
        'visible' => [
          ':input[name="style_options[nav]"]' => ['checked' => TRUE],
        ],
      ],
    ];
    // Autoplay.
    $form['autoplay'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Autoplay'),
      '#default_value' => $this->options['autoplay'],
      '#description' => $this->t('Toggles the automatic change of slides.'),
    ];
    // AutoplayHoverPause.
    $form['autoplayHoverPause'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Pause on hover'),
      '#default_value' => $this->options['autoplayHoverPause'],
      '#description' => $this->t('Pause autoplay on mouse hover.'),
      '#states' => [
        'visible' => [
          ':input[name="style_options[autoplay]"]' => ['checked' => TRUE],
        ],
      ],
    ];
    // AutoplayButtonOutput.
    $form['autoplayButtonOutput'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Autoplay buttons'),
      '#default_value' => $this->options['autoplayButtonOutput'],
      '#description' => $this->t('Turn off/on arrow autoplay buttons.'),
      '#states' => [
        'visible' => [
          ':input[name="style_options[autoplay]"]' => ['checked' => TRUE],
        ],
      ],
    ];
    // autoplayPosition.
     $form['autoplayPosition'] = [
      '#type' => 'select',
      '#title' => $this->t('Autoplay position'),
      '#options' => [
        'top' => $this->t('Top'),
        'bottom' => $this->t('Bottom'),
      ],
      '#default_value' => $this->options['autoplayPosition'],
      '#description' => $this->t('Display autoplay controls above/below slides.'),
      '#states' => [
        'visible' => [
          ':input[name="style_options[autoplay]"]' => ['checked' => TRUE],
          ':input[name="style_options[autoplayButtonOutput]"]' => ['checked' => TRUE],
        ],
      ],
    ];
    // Autoplay text start.
    $form['autoplayTextStart'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Start Text'),
      '#default_value' => $this->options['autoplayTextStart'],
      '#states' => [
        'visible' => [
          ':input[name="style_options[autoplay]"]' => ['checked' => TRUE],
          ':input[name="style_options[autoplayButtonOutput]"]' => ['checked' => TRUE],
        ],
      ],
    ];
    // Autoplay text stop.
    $form['autoplayTextStop'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Stop Text'),
      '#default_value' => $this->options['autoplayTextStop'],
      '#states' => [
        'visible' => [
          ':input[name="style_options[autoplay]"]' => ['checked' => TRUE],
          ':input[name="style_options[autoplayButtonOutput]"]' => ['checked' => TRUE],
        ],
      ],
    ];
    // Controls.
    $form['controls'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Controls'),
      '#default_value' => $this->options['controls'],
      '#description' => $this->t('Controls the display and functionalities of (prev/next buttons).'),
    ];
    // controlsPosition.
     $form['controlsPosition'] = [
      '#type' => 'select',
      '#title' => $this->t('Controls position'),
      '#options' => [
        'top' => $this->t('Top'),
        'bottom' => $this->t('Bottom'),
      ],
      '#default_value' => $this->options['controlsPosition'],
      '#description' => $this->t('Display controls above/below slides.'),
      '#states' => [
        'visible' => [
          ':input[name="style_options[controls]"]' => ['checked' => TRUE],
        ],
      ],
    ];
    // Controls text prev.
    $form['controlsTextPrev'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Prev Text'),
      '#default_value' => $this->options['controlsTextPrev'],
      '#states' => [
        'visible' => [
          ':input[name="style_options[controls]"]' => ['checked' => TRUE],
        ],
      ],
    ];
    // Controls next text.
    $form['controlsTextNext'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Next Text'),
      '#default_value' => $this->options['controlsTextNext'],
      '#states' => [
        'visible' => [
          ':input[name="style_options[controls]"]' => ['checked' => TRUE],
        ],
      ],
    ];
    // arrowKeys.
    $form['arrowKeys'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Arrow Keys'),
      '#default_value' => $this->options['arrowKeys'],
      '#description' => $this->t('Allows using arrow keys to switch slides.'),
    ];
    // mouseDrag.
    $form['mouseDrag'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Mouse Drag'),
      '#default_value' => $this->options['mouseDrag'],
      '#description' => $this->t('Turn off/on mouse drag.'),
    ];
    // Loop.
    $form['loop'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Loop'),
      '#default_value' => $this->options['loop'],
      '#description' => $this->t('Moves throughout all the slides seamlessly.'),
    ];
    // Speed.
    $form['speed'] = [
      '#type' => 'number',
      '#title' => $this->t('Speed'),
      '#default_value' => $this->options['speed'],
      '#description' => $this->t('Pagination speed in milliseconds.'),
    ];
    // DimensionMobile.
    $form['dimensionMobile'] = [
      '#type' => 'number',
      '#title' => $this->t('Mobile dimension'),
      '#default_value' => $this->options['dimensionMobile'],
      '#description' => $this->t('Set the mobile dimensions in px.'),
    ];
    // ItemsMobile.
    $form['itemsMobile'] = [
      '#type' => 'number',
      '#step' => '.1',
      '#title' => $this->t('Mobile items'),
      '#default_value' => $this->options['itemsMobile'],
      '#description' => $this->t('Maximum amount of items displayed at mobile.'),
    ];
    // DimensionDesktop.
    $form['dimensionDesktop'] = [
      '#type' => 'number',
      '#title' => $this->t('Desktop dimension'),
      '#default_value' => $this->options['dimensionDesktop'],
      '#description' => $this->t('Set the desktop dimension in px.'),
    ];
    // itemsDesktop.
    $form['itemsDesktop'] = [
      '#type' => 'number',
      '#step' => '.1',
      '#title' => $this->t('Desktop items'),
      '#default_value' => $this->options['itemsDesktop'],
      '#description' => $this->t('Maximum amount of items displayed at desktop.'),
    ];
  }

}

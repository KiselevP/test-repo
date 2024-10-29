<?php

namespace Drupal\tiny_slider\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceFormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;
use Drupal\tiny_slider\TinySliderGlobal;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'tiny_slider_field_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "tiny_slider_field_formatter",
 *   label = @Translation("Tiny Slider Carousel"),
 *   field_types = {
 *     "image"
 *   }
 * )
 */
class TinySliderFieldFormatter extends EntityReferenceFormatterBase implements ContainerFactoryPluginInterface {

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * The image style storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $imageStyleStorage;

  /**
   * {@inheritdoc}
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, $label, $view_mode, array $third_party_settings, AccountInterface $current_user, EntityStorageInterface $image_style_storage) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);
    $this->currentUser = $current_user;
    $this->imageStyleStorage = $image_style_storage;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('current_user'),
      $container->get('entity_type.manager')->getStorage('image_style')
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    $tiny_slider_default_settings = TinySliderGlobal::defaultSettings();
    return $tiny_slider_default_settings + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $image_styles = image_style_options(FALSE);
    $description_link = Link::fromTextAndUrl(
      $this->t('Configure Image Styles'),
      Url::fromRoute('entity.image_style.collection')
    );
    $element['image_style'] = [
      '#title' => $this->t('Image style'),
      '#type' => 'select',
      '#default_value' => $this->getSetting('image_style'),
      '#empty_option' => $this->t('None (original image)'),
      '#options' => $image_styles,
      '#description' => $description_link->toRenderable() + [
        '#access' => $this->currentUser->hasPermission('administer image styles'),
      ],
    ];

    // Link image to.
    $element['image_link'] = [
      '#title' => $this->t('Link image to'),
      '#type' => 'select',
      '#default_value' => $this->getSetting('image_link'),
      '#empty_option' => $this->t('Nothing'),
      '#options' => [
        'content' => $this->t('Content'),
        'file' => $this->t('File'),
      ],
    ];

    // Items.
    $element['items'] = [
      '#type' => 'number',
      '#step' => '.1',
      '#title' => $this->t('Items'),
      '#default_value' => !empty($this->getSetting('items')) ? $this->getSetting('items') : 3,
      '#description' => $this->t('Maximum amount of items displayed at a time with the widest browser width.'),
    ];

    // Margin.
    $element['margin'] = [
      '#type' => 'number',
      '#title' => $this->t('Margin'),
      '#default_value' => $this->getSetting('margin'),
      '#description' => $this->t('Margin from items.'),
    ];

    // Navigation.
    $element['nav'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Navigation'),
      '#default_value' => $this->getSetting('nav'),
      '#description' => $this->t('Controls the display and functionalities of controls components (prev/next buttons).'),
    ];

    // navPosition.
     $element['navPosition'] = [
      '#type' => 'select',
      '#title' => $this->t('Navigation position'),
      '#options' => [
        'top' => $this->t('Top'),
        'bottom' => $this->t('Bottom'),
      ],
      '#default_value' => $this->getSetting('navPosition'),
      '#description' => $this->t('Display navigation above/below slides.'),
      '#states' => [
        'visible' => [
          ':input[name="fields[field_image][settings_edit_form][settings][nav]"]' => [
            'checked' => TRUE
          ],
        ],
      ],
    ];

    // Autoplay.
    $element['autoplay'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Autoplay'),
      '#default_value' => $this->getSetting('autoplay'),
    ];

    // AutoplayHoverPause.
    $element['autoplayHoverPause'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Pause on hover'),
      '#default_value' => $this->getSetting('autoplayHoverPause'),
      '#description' => $this->t('Pause autoplay on mouse hover.'),
      '#states' => [
        'visible' => [
          ':input[name="fields[field_image][settings_edit_form][settings][autoplay]"]' => [
            'checked' => TRUE
          ],
        ],
      ],
    ];

    // Autoplay Button Output.
    $element['autoplayButtonOutput'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Autoplay buttons'),
      '#default_value' => $this->getSetting('autoplayButtonOutput'),
      '#description' => $this->t('Turn off/on arrow autoplay buttons.'),
      '#states' => [
        'visible' => [
          ':input[name="fields[field_image][settings_edit_form][settings][autoplay]"]' => [
            'checked' => TRUE
          ],
        ],
      ],
    ];

    // Autoplay Position.
    $element['autoplayPosition'] = [
      '#type' => 'select',
      '#title' => $this->t('Autoplay position'),
      '#options' => [
        'top' => $this->t('Top'),
        'bottom' => $this->t('Bottom'),
      ],
      '#default_value' => $this->getSetting('autoplayPosition'),
      '#description' => $this->t('Display autoplay controls above/below slides.'),
      '#states' => [
        'visible' => [
          ':input[name="fields[field_image][settings_edit_form][settings][autoplay]"]' => [
            'checked' => TRUE
          ],
          ':input[name="fields[field_image][settings_edit_form][settings][autoplayButtonOutput]"]' => [
            'checked' => TRUE
          ],
        ],
      ],
    ];

    // Autoplay text start.
    $element['autoplayTextStart'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Start Text'),
      '#default_value' => $this->getSetting('autoplayTextStart'),
      '#states' => [
        'visible' => [
          ':input[name="fields[field_image][settings_edit_form][settings][autoplay]"]' => [
            'checked' => TRUE
          ],
          ':input[name="fields[field_image][settings_edit_form][settings][autoplayButtonOutput]"]' => [
            'checked' => TRUE
          ],
        ],
      ],
    ];

    // Autoplay text stop.
    $element['autoplayTextStop'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Stop Text'),
      '#default_value' => $this->getSetting('autoplayTextStop'),
      '#states' => [
        'visible' => [
          ':input[name="fields[field_image][settings_edit_form][settings][autoplay]"]' => [
            'checked' => TRUE
          ],
          ':input[name="fields[field_image][settings_edit_form][settings][autoplayButtonOutput]"]' => [
            'checked' => TRUE
          ],
        ],
      ],
    ];

    // Controls.
    $element['controls'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Controls'),
      '#default_value' => $this->getSetting('controls'),
      '#description' => $this->t('Controls the display and functionalities of nav components (dots).'),
    ];

    // Controls Position.
    $element['controlsPosition'] = [
      '#type' => 'select',
      '#title' => $this->t('Controls position'),
      '#options' => [
        'top' => $this->t('Top'),
        'bottom' => $this->t('Bottom'),
      ],
      '#default_value' => $this->getSetting('controlsPosition'),
      '#description' => $this->t('Display controls above/below slides.'),
      '#states' => [
        'visible' => [
          ':input[name="fields[field_image][settings_edit_form][settings][controls]"]' => [
            'checked' => TRUE
          ],
        ],
      ],
    ];

    // Controls text prev.
    $element['controlsTextPrev'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Prev Text'),
      '#default_value' => $this->getSetting('controlsTextPrev'),
      '#states' => [
        'visible' => [
          ':input[name="fields[field_image][settings_edit_form][settings][controls]"]' => [
            'checked' => TRUE
          ],
        ],
      ],
    ];

    // Controls text next.
    $element['controlsTextNext'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Next Text'),
      '#default_value' => $this->getSetting('controlsTextNext'),
      '#states' => [
        'visible' => [
          ':input[name="fields[field_image][settings_edit_form][settings][controls]"]' => [
            'checked' => TRUE
          ],
        ],
      ],
    ];

    // arrowKeys.
    $element['arrowKeys'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Arrow Keys'),
      '#default_value' => $this->getSetting('arrowKeys'),
      '#description' => $this->t('Allows using arrow keys to switch slides.'),
    ];

    // mouseDrag.
    $element['mouseDrag'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Mouse Drag'),
      '#default_value' => $this->getSetting('mouseDrag'),
      '#description' => $this->t('Turn off/on mouse drag.'),
    ];

    // Loop.
    $element['loop'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Loop'),
      '#default_value' => $this->getSetting('loop'),
      '#description' => $this->t('Moves throughout all the slides seamlessly.'),
    ];

    // Speed.
    $element['speed'] = [
      '#type' => 'number',
      '#title' => $this->t('Speed'),
      '#default_value' => $this->getSetting('speed'),
      '#description' => $this->t('Pagination speed in milliseconds.'),
    ];

    // DimensionMobile.
    $element['dimensionMobile'] = [
      '#type' => 'number',
      '#title' => $this->t('Mobile dimension'),
      '#default_value' => $this->getSetting('dimensionMobile'),
      '#description' => $this->t('Set the mobile dimensions in px.'),
    ];

    // ItemsMobile.
    $element['itemsMobile'] = [
      '#type' => 'number',
      '#step' => '.1',
      '#title' => $this->t('Mobile items'),
      '#default_value' => $this->getSetting('itemsMobile'),
      '#description' => $this->t('Maximum amount of items displayed at mobile.'),
    ];

    // DimensionDesktop.
    $element['dimensionDesktop'] = [
      '#type' => 'number',
      '#title' => $this->t('Desktop dimension'),
      '#default_value' => $this->getSetting('dimensionDesktop'),
      '#description' => $this->t('Set the desktop dimensions in px.'),
    ];

    // itemsDesktop.
    $element['itemsDesktop'] = [
      '#type' => 'number',
      '#step' => '.1',
      '#title' => $this->t('Desktop items'),
      '#default_value' => $this->getSetting('itemsDesktop'),
      '#description' => $this->t('Maximum amount of items displayed at desktop.'),
    ];

    return $element + parent::settingsForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];

    $itemsdisplay = $this->getSetting('items') ? $this->getSetting('items') : 3;
    $nav = $this->getSetting('nav') ? 'TRUE' : 'FALSE';
    $navposition = $this->getSetting('navPosition') ? $this->getSetting('navPosition') : 'top';
    $autoplay = $this->getSetting('autoplay') ? 'TRUE' : 'FALSE';
    $autoplaypause = $this->getSetting('autoplayHoverPause') ? 'TRUE' : 'FALSE';
    $autoplaybuttonoutput = $this->getSetting('autoplayButtonOutput') ? 'FALSE' : 'FALSE';
    $autoplayposition = $this->getSetting('autoplayPosition') ? $this->getSetting('autoPlayPosition') : 'top';
    $autoplaytextstart = $this->getSetting('autoplayTextStart') ? $this->getSetting('autoplayTextStart') : 'start';
    $autoplaytextstop = $this->getSetting('autoplayTextStop') ? $this->getSetting('autoplayTextStop') : 'stop';
    $controls = $this->getSetting('controls') ? 'TRUE' : 'FALSE';
    $controlsplayposition = $this->getSetting('controlsPosition') ? $this->getSetting('controlsPosition') : 'top';
    $controlstextprev = $this->getSetting('controlsTextPrev') ? $this->getSetting('controlsTextPrev') : 'prev';
    $controlstextnext = $this->getSetting('controlsTextNext') ? $this->getSetting('controlsTextNext') : 'next';
    $arrowkeys = $this->getSetting('arrowKeys') ? 'TRUE' : 'FALSE';
    $mousedrag = $this->getSetting('mouseDrag') ? 'TRUE' : 'FALSE';
    $loop = $this->getSetting('loop') ? 'TRUE' : 'FALSE';
    $speed = $this->getSetting('speed') ? $this->getSetting('speed') : '300';

    $summary[] = $this->t('TinySlider settings summary.');
    $summary[] = $this->t('Image style: ') . $this->getSetting('image_style');
    $summary[] = $this->t('Link image to: ') . $this->getSetting('image_link') ?? $this->t('Nothing');
    $summary[] = $this->t('Amount of items displayed: ') . $itemsdisplay;
    $summary[] = $this->t('Margin from items: ') . $this->getSetting('margin') . 'px';
    $summary[] = $this->t('Display nav: ') . $nav;
    $summary[] = $this->t('Nav position: ') . $navposition;
    $summary[] = $this->t('Autoplay: ') . $autoplay;
    $summary[] = $this->t('Autoplay pause on mouse hover: ') . $autoplaypause;
    $summary[] = $this->t('Show autoplay buttons: ') . $autoplaybuttonoutput;
    $summary[] = $this->t('Start text: ') . $autoplaytextstart;
    $summary[] = $this->t('Stop text: ') . $autoplaytextstop;
    $summary[] = $this->t('Show controls: ') . $controls;
    $summary[] = $this->t('Prev text: ') . $controlstextprev;
    $summary[] = $this->t('Next text: ') . $controlstextnext;
    $summary[] = $this->t('Arrow keys: ') . $arrowkeys;
    $summary[] = $this->t('Mouse drag: ') . $mousedrag;
    $summary[] = $this->t('Loop: ') . $loop;
    $summary[] = $this->t('Speed: ') . $speed . 'ms';

    if ($this->getSetting('dimensionMobile')) {
      $summary[] = $this->t('Mobile dimensions: ') . $this->getSetting('dimensionMobile') . 'px';
    }

    if ($this->getSetting('itemsMobile')) {
      $summary[] = $this->t('Mobile items to show: ') . $this->getSetting('itemsMobile');
    }

    if ($this->getSetting('dimensionDesktop')) {
      $summary[] = $this->t('Desktop dimensions: ') . $this->getSetting('dimensionDesktop') . 'px';
    }

    if ($this->getSetting('itemsDesktop')) {
      $summary[] = $this->t('Desktop items to show: ') . $this->getSetting('itemsDesktop');
    }

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {

    $elements = [];

    $files = $this->getEntitiesToView($items, $langcode);

    // Early opt-out if the field is empty.
    if (empty($files)) {
      return $elements;
    }

    $url = NULL;
    $image_link_setting = $this->getSetting('image_link');
    // Check if the formatter involves a link.
    if ($image_link_setting == 'content') {
      $entity = $items->getEntity();
      if (!$entity->isNew()) {
        $url = $entity->toUrl()->toString();
      }
    }
    elseif ($image_link_setting == 'file') {
      $link_file = TRUE;
    }

    $image_style_setting = $this->getSetting('image_style');

    // Collect cache tags to be added for each item in the field.
    $cache_tags = [];
    if (!empty($image_style_setting)) {
      $image_style = $this->imageStyleStorage->load($image_style_setting);
      $cache_tags = $image_style->getCacheTags();
    }

    foreach ($files as $delta => $file) {
      if (isset($link_file)) {
        $image_uri = $file->getFileUri();
        $url = \Drupal::service('file_url_generator')->generate($image_uri);
      }
      $cache_tags = Cache::mergeTags($cache_tags, $file->getCacheTags());

      // Extract field item attributes for the theme function, and unset them
      // from the $item so that the field template does not re-render them.
      $item = $file->_referringItem;
      $item_attributes = $item->_attributes;
      unset($item->_attributes);

      $elements[$delta] = [
        '#theme' => 'image_formatter',
        '#item' => $item,
        '#item_attributes' => $item_attributes,
        '#image_style' => $image_style_setting,
        '#url' => $url,
        '#cache' => [
          'tags' => $cache_tags,
        ],
      ];
    }
    $tiny_slider_default_settings = TinySliderGlobal::defaultSettings();
    $settings = $tiny_slider_default_settings;
    foreach ($settings as $k => $v) {
      $s = $this->getSetting($k);
      $settings[$k] = isset($s) ? $s : $settings[$k];
    }
    return [
      '#theme' => 'tiny_slider',
      '#items' => $elements,
      '#settings' => $settings,
      '#attached' => ['library' => ['tiny_slider/tiny_slider']],
    ];

  }

  /**
   * Generate the output appropriate for one field item.
   *
   * @param \Drupal\Core\Field\FieldItemInterface $item
   *   One field item.
   *
   * @return string
   *   The textual output generated.
   */
  protected function viewValue(FieldItemInterface $item) {
    // The text value has no text format assigned to it, so the user input
    // should equal the output, including newlines.
    return nl2br(Html::escape($item->value));
  }

}

<?php

namespace Drupal\phone_number\Tests\Functional;

use Drupal\Tests\BrowserTestBase;
use Drupal\Tests\phone_number\Traits\PhoneNumberCreationTrait;

/**
 * Tests the local formatting of phone number fields.
 *
 * @group phone_number
 */
class PhoneNumberFieldLocalFormatterTest extends BrowserTestBase {

  use PhoneNumberCreationTrait;

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = [
    'field',
    'node',
    'phone_number',
  ];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * A user with permission to create articles.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $webUser;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->createPhoneNumberField();
  }

  /**
   * Tests the phone number local formatter.
   *
   * @covers \Drupal\phone_number\Plugin\Field\FieldFormatter\PhoneNumberLocalFormatter::viewElements
   *
   * @dataProvider providerPhoneNumbersLocal
   */
  public function testPhoneNumberLocalFormatter($input, $expected, $country) {
    // Set the view display with settings.
    $this->container->get('entity_display.repository')
      ->getViewDisplay('node', 'article', 'default')
      ->setComponent('field_phone_number', [
        'type' => 'phone_number_local',
        'settings' => [
          'as_link' => FALSE,
        ],
        'weight' => 1,
      ])
      ->save();

    // Data input.
    $edit = [
      'title[0][value]' => $this->randomMachineName(),
      'field_phone_number[0][country-code]' => $country,
      'field_phone_number[0][phone]' => $input,
    ];

    // Assertions.
    $this->drupalGet('node/add/article');
    $this->submitForm($edit, 'Save');
    $this->assertSession()->responseContains($expected);
  }

  /**
   * Provides the phone numbers to check and expected results.
   */
  public function providerPhoneNumbersLocal() {
    return [
      'standard phone number from US' => [
        '(650) 253-0000', '(650) 253-0000', 'US',
      ],
      'standard phone number from CH' => [
        '44 668 18 00', '044 668 18 00', 'CH',
      ],
      'standard phone number from GB' => [
        '0161 496 0000', '0161 496 0000', 'GB',
      ],
      'standard phone number from US with country code' => [
        '+1 650 253 0000', '(650) 253-0000', 'US',
      ],
    ];
  }

  /**
   * Tests the phone number local as link formatter.
   *
   * @covers \Drupal\phone_number\Plugin\Field\FieldFormatter\PhoneNumberLocalFormatter::viewElements
   *
   * @dataProvider providerPhoneNumbersLocalLink
   */
  public function testPhoneNumberLocalLinkFormatter($input, $expected, $expected_link, $country) {
    // Set the view display with settings.
    $this->container->get('entity_display.repository')
      ->getViewDisplay('node', 'article', 'default')
      ->setComponent('field_phone_number', [
        'type' => 'phone_number_local',
        'settings' => [
          'as_link' => TRUE,
        ],
        'weight' => 1,
      ])
      ->save();

    // Data input.
    $edit = [
      'title[0][value]' => $this->randomMachineName(),
      'field_phone_number[0][country-code]' => $country,
      'field_phone_number[0][phone]' => $input,
    ];

    // Assertions.
    $this->drupalGet('node/add/article');
    $this->submitForm($edit, 'Save');
    $this->assertSession()->responseContains('<a href="tel:' . $expected_link . '">' . $expected . '</a>');
  }

  /**
   * Provides the phone numbers to check and expected results.
   */
  public function providerPhoneNumbersLocalLink() {
    return [
      'standard phone number from US' => [
        '(650) 253-0000', '(650) 253-0000', '+1-650-253-0000', 'US',
      ],
      'standard phone number from CH' => [
        '44 668 18 00', '044 668 18 00', '+41-44-668-18-00', 'CH',
      ],
      'standard phone number from GB' => [
        '0161 496 0000', '0161 496 0000', '+44-161-496-0000', 'GB',
      ],
      'standard phone number from US with country code' => [
        '+1 650 253 0000', '(650) 253-0000', '+1-650-253-0000', 'US',
      ],
    ];
  }

}

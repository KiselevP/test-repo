<?php

namespace Drupal\Tests\video_embed_rutube\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\video_embed_rutube\Plugin\video_embed_field\Provider\Rutube;

/**
 * Test that URL parsing for rutube provider is functioning.
 *
 * @group video_embed_rutube
 */
class ProviderUrlParseTest extends KernelTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = ['video_embed_field', 'video_embed_rutube'];

  /**
   * Test URL parsing works as expected.
   *
   * @dataProvider urlsWithExpectedIds
   */
  public function testUrlParsing($url, $expected) {
    $this->assertEquals($expected, Rutube::getIdFromInput($url));
  }

  /**
   * A data provider for URL parsing test cases.
   *
   * @return array
   *   An array of test cases.
   */
  public function urlsWithExpectedIds() {
    return [
      [
        'http://rutube.ru/video/3fc1e8701383ed7367e735d1f74c919b',
        '3fc1e8701383ed7367e735d1f74c919b',
      ],
      [
        'http://rutube.ru/video/3fc1e8701383ed7367e735d1f74c919b/',
        '3fc1e8701383ed7367e735d1f74c919b',
      ],
      [
        'https://rutube.ru/video/3fc1e8701383ed7367e735d1f74c919b/?r=wd',
        '3fc1e8701383ed7367e735d1f74c919b',
      ],
      [
        'https://www.rutube.ru/video/3fc1e8701383ed7367e735d1f74c919b/?r=wd',
        '3fc1e8701383ed7367e735d1f74c919b',
      ],
      [
        'https://rutube.ru/video/3fc1e8701383ed7367e735d1f74c919b/?r=wd&t=825',
        '3fc1e8701383ed7367e735d1f74c919b',
      ],
      [
        'https://rutube.ru/video/3fc1e8701383ed7367e735d1f74c919b/?r=wd&t=825',
        '3fc1e8701383ed7367e735d1f74c919b',
      ],
      [
        'https://rutube.ru/video/?r=wd&t=825',
        FALSE,
      ],
      [
        'https://rutube.ru/video/-/?r=wd&t=825',
        FALSE,
      ],
    ];
  }

  /**
   * Test time index parsing works as expected.
   *
   * @dataProvider timeIndexesWithExpectedValues
   */
  public function testTimeIndexParsing($url, $expected) {
    $this->assertEquals($expected, Rutube::getTimeIndex($url));
  }

  /**
   * A data provider for time index parsing test cases.
   *
   * @return array
   *   An array of test cases.
   */
  public function timeIndexesWithExpectedValues() {
    return [
      [
        'https://rutube.ru/video/3fc1e8701383ed7367e735d1f74c919b/?r=wd&t=825',
        '825',
      ],
      [
        'https://rutube.ru/video/3fc1e8701383ed7367e735d1f74c919b/?r=wd&param=var&t=825&stop=200',
        '825',
      ],
      [
        'https://rutube.ru/video/3fc1e8701383ed7367e735d1f74c919b/?r=wd&t=',
        FALSE,
      ],
      [
        'https://rutube.ru/video/3fc1e8701383ed7367e735d1f74c919b/?r=wd&t=test',
        FALSE,
      ],
    ];
  }

}

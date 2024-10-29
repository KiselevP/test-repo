<?php

namespace Drupal\Tests\twig_typography\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Tests the twig typography extension.
 *
 * @group twig_typography
 * @group Template
 */
class TwigTypographyBrowserTest extends BrowserTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = ['twig_typography', 'twig_typography_test'];

  /**
   * Theme to enable.
   *
   * @var string
   */
  protected $defaultTheme = 'stark';

  /**
   * Tests that a render array gets rendered to a string and used.
   */
  public function testTypographyRenderArray() {
    $this->drupalGet('/twig-typography-test');
    $assert = $this->assertSession();
    $assert->responseContains('Test <span class="push-double"></span>​<span class="pull-double">“</span>curly dou­ble quotes” and <span class="push-single"></span>​<span class="pull-single">‘</span>curly sin­gle quotes’');
    $assert->responseContains('Test for hang­ing&nbsp;widow');
    $assert->responseContains('Test a <span class="push-double"></span>​<span class="pull-double">“</span>ren­der&nbsp;array”');
  }

}

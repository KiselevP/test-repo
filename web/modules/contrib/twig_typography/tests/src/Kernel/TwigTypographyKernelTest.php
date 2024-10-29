<?php

namespace Drupal\Tests\twig_typography\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\twig_typography\TwigExtension\TestTypography;

/**
 * Tests the twig typography extension.
 *
 * @group twig_typography
 * @group Template
 */
class TwigTypographyKernelTest extends KernelTestBase {

  /**
   * The system under test.
   *
   * @var \Drupal\Core\Template\TwigExtension
   */
  protected $systemUnderTest;

  /**
   * {@inheritdoc}
   */
  public function setUp(): void {
    parent::setUp();

    $this->systemUnderTest = new TestTypography();
  }

  /**
   * Tests that a render array gets rendered to a string using renderPlain().
   *
   * In a try/catch statement the page level render() function is tried first
   * and then renderPlain() used as a fallback.
   */
  public function testTypographyRenderArray() {
    $render_array = [
      '#markup' => '<h1 class="page-title">Test "the" page \'title\'</h1>',
    ];
    $this->assertSame('<h1 class="page-title">Test <span class="push-double"></span>​<span class="pull-double">“</span>the” page <span class="push-single"></span>​<span class="pull-single">‘</span>title’</h1>', $this->systemUnderTest->applyTypography($render_array, []));
  }

}

<?php

namespace Drupal\Tests\wine\Functional;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Tests\BrowserTestBase;
use Drupal\dcg_test\TestTrait;

/**
 * Test configuration entity.
 *
 * @group DCG
 */
class ConfigurationEntityTest extends BrowserTestBase {

  use TestTrait;

  /**
   * {@inheritdoc}
   */
  public static $modules = ['wine'];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
    $user = $this->drupalCreateUser(['administer example']);
    $this->drupalLogin($user);
  }

  /**
   * Test callback.
   */
  public function testConfigurationEntity() {

    $this->drupalGet('admin/structure/example');
    $this->assertPageTitle('Example configuration');
    $this->assertXpath('//td[@colspan = "4" and text() = "There are no examples yet."]');

    $this->drupalGet('admin/structure/example/add');
    $this->assertPageTitle('Add an example');

    $edit = [
      'label' => 'Test',
      'name' => 'test',
      'status' => TRUE,
      'description' => 'The entity description.',
    ];
    $this->drupalPostForm(NULL, $edit, 'Save');
    $this->assertStatusMessage(new FormattableMarkup('Created new example %label.', ['%label' => 'Test']));

    $this->assertXpath('//tbody//td[text() = "Test"]/following::td[text() = "test"]/following::td[text() = "Enabled"]/following::td//ul[@class = "dropbutton"]');

    $this->getSession()->getDriver()->click('//ul[@class = "dropbutton"]//a[text() = "Edit"]');

    $this->assertPageTitle('Edit an example');
    $this->assertXpath('//input[@name = "label" and @value = "Test"]');
    $this->assertXpath('//input[@name = "status" and @checked = "checked"]');
    $this->assertXpath('//textarea[@name = "description" and text() = "The entity description."]');

    $edit = [
      'label' => 'Updated test',
    ];
    $this->drupalPostForm(NULL, $edit, 'Save');
    $this->assertStatusMessage(new FormattableMarkup('Updated example %label.', ['%label' => 'Updated test']));

    $this->getSession()->getDriver()->click('//ul[@class = "dropbutton"]//a[text() = "Delete"]');
    $this->assertPageTitle(new FormattableMarkup('Are you sure you want to delete the example %label?', ['%label' => 'Updated test']));

    $this->drupalPostForm(NULL, [], 'Delete');
    $this->assertStatusMessage(new FormattableMarkup('The example %label has been deleted.', ['%label' => 'Updated test']));
    $this->assertXpath('//td[@colspan = "4" and text() = "There are no examples yet."]');
  }

}

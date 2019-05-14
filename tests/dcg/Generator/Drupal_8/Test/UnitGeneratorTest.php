<?php

namespace DrupalCodeGenerator\Tests\Generator\Drupal_8\Test;

use DrupalCodeGenerator\Tests\Generator\BaseGeneratorTest;

/**
 * Test for d8:test:unit command.
 */
class UnitGeneratorTest extends BaseGeneratorTest {

  protected $class = 'Drupal_8\Test\Unit';

  protected $interaction = [
    'Module name [%default_name%]:' => 'Foo',
    'Module machine name [foo]:' => 'foo',
    'Class [ExampleTest]:' => 'ExampleTest',
  ];

  protected $fixtures = [
    'tests/src/Unit/ExampleTest.php' => __DIR__ . '/_unit.php',
  ];

}
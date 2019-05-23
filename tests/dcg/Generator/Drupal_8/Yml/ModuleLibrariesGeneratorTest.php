<?php

namespace DrupalCodeGenerator\Tests\Generator\Drupal_8\Yml;

use DrupalCodeGenerator\Tests\Generator\BaseGeneratorTest;

/**
 * Test for yml:module-libraries command.
 */
class ModuleLibrariesGeneratorTest extends BaseGeneratorTest {

  protected $class = 'Yml\ModuleLibraries';

  protected $interaction = [
    'Module machine name [%default_machine_name%]:' => 'example',
  ];

  protected $fixtures = [
    'example.libraries.yml' => __DIR__ . '/_module_libraries.yml',
  ];

}
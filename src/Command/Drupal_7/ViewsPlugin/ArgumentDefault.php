<?php

namespace DrupalCodeGenerator\Command\Drupal_7\ViewsPlugin;

use DrupalCodeGenerator\Command\ModuleGenerator;

/**
 * Implements d7:views-plugin:argument-default command.
 */
class ArgumentDefault extends ModuleGenerator {

  protected $name = 'd7:views-plugin:argument-default';
  protected $description = 'Generates Drupal 7 argument default views plugin';

  /**
   * {@inheritdoc}
   */
  protected function generate() :void {
    $vars = &$this->collectDefault();
    $vars['plugin_name'] = $this->ask('Plugin name', 'Example');
    $vars['plugin_machine_name'] = $this->ask('Plugin machine name', '{plugin_name|h2m}');

    $this->addFile('{machine_name}.module')
      ->template('d7/views-plugin/argument-default.module')
      ->action('append')
      ->headerSize(7);

    $this->addFile('views/{machine_name}.views.inc')
      ->template('d7/views-plugin/argument-default-views.inc')
      ->action('append')
      ->headerSize(7);

    $this->addFile('views/views_plugin_argument_{plugin_machine_name}.inc')
      ->template('d7/views-plugin/argument-default');
  }

}

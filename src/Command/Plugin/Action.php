<?php

namespace DrupalCodeGenerator\Command\Plugin;

/**
 * Implements plugin:action command.
 */
final class Action extends PluginGenerator {

  protected $name = 'plugin:action';
  protected $description = 'Generates action plugin';
  protected $alias = 'action';
  protected $pluginLabelQuestion = 'Action label';
  protected $pluginLabelDefault = 'Update node title';

  /**
   * {@inheritdoc}
   */
  protected function generate(): void {
    $vars = &$this->collectDefault();

    $vars['category'] = $this->ask('Action category', 'Custom');
    $vars['configurable'] = $this->confirm('Make the action configurable?', FALSE);

    $this->addFile('src/Plugin/Action/{class}.php', 'action');

    if ($vars['configurable']) {
      $this->addSchemaFile()->template('schema');
    }
  }

}

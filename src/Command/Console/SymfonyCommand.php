<?php

namespace DrupalCodeGenerator\Command\Console;

use DrupalCodeGenerator\Command\ModuleGenerator;
use DrupalCodeGenerator\Utils;

/**
 * Implements console:symfony-command command.
 */
final class SymfonyCommand extends ModuleGenerator {

  protected $name = 'console:symfony-command';
  protected $description = 'Generates Symfony console command';
  protected $alias = 'symfony-command';

  /**
   * {@inheritdoc}
   */
  protected function generate(): void {
    $vars = &$this->collectDefault();
    $command_name_validator = function (?string $value): ?string {
      return static::validate($value, '^[a-z][a-z0-9-_:]*[a-z0-9]$', 'The value is not correct command name.');
    };
    $vars['command']['name'] = $this->ask('Command name', '{machine_name}:example', $command_name_validator);

    $vars['command']['description'] = $this->ask('Command description');

    $sub_names = explode(':', $vars['command']['name']);
    $short_name = array_pop($sub_names);

    $alias_validator = function (?string $value): ?string {
      return static::validate($value, '^[a-z0-9][a-z0-9_]+$', 'The value is not correct alias name.');
    };
    $vars['command']['alias'] = $this->ask('Command alias', $short_name, $alias_validator);

    $vars['class'] = $this->ask('Class', Utils::camelize($short_name) . 'Command');

    if ($this->confirm('Would you like to run the command with Drush')) {
      $this->addServicesFile('drush.services.yml')->template('services');
    }
    $this->addFile('src/Command/{class}.php', 'command');
  }

}

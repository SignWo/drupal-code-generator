<?php

namespace DrupalCodeGenerator\Style;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\StyleInterface as SymfonyStyleInterface;

/**
 * Output style helpers.
 */
interface GeneratorStyleInterface extends SymfonyStyleInterface, OutputInterface {

  /**
   * Asks a question to the user.
   */
  public function askQuestion(Question $question);

  /**
   * Builds console table.
   */
  public function buildTable(array $headers, array $rows): Table;

}

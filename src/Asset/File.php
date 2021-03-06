<?php

namespace DrupalCodeGenerator\Asset;

use DrupalCodeGenerator\Utils;

/**
 * Simple data structure to represent a file being generated.
 */
final class File extends Asset {

  public const ACTION_REPLACE = 0x01;
  public const ACTION_PREPEND = 0x02;
  public const ACTION_APPEND = 0x03;
  public const ACTION_SKIP = 0x04;

  /**
   * Asset content.
   *
   * @var string|null
   */
  private $content;

  /**
   * Twig template to render header.
   *
   * @var string|null
   */
  private $headerTemplate;

  /**
   * Twig template to render main content.
   *
   * @var string|null
   */
  private $template;

  /**
   * The template string to render.
   *
   * @var string|null
   */
  private $inlineTemplate;

  /**
   * Template variables.
   *
   * @var array
   */
  private $vars = [];

  /**
   * Action.
   *
   * An action to take if specified file already exists.
   *
   * @var string
   */
  private $action = self::ACTION_REPLACE;

  /**
   * Header size.
   *
   * @var int
   */
  private $headerSize = 0;

  /**
   * Content resolver.
   *
   * @var callable|null
   */
  private $resolver;

  /**
   * {@inheritdoc}
   */
  public function __construct(string $path) {
    parent::__construct($path);
    $this->mode(0644);
  }

  /**
   * Getter for asset content.
   *
   * @return string|null
   *   Asset content.
   */
  public function getContent(): ?string {
    return $this->content;
  }

  /**
   * Getter for header template.
   *
   * @return string|null
   *   Asset header template.
   */
  public function getHeaderTemplate(): ?string {
    return $this->headerTemplate;
  }

  /**
   * Getter for template.
   *
   * @return string|null
   *   Asset template.
   */
  public function getTemplate(): ?string {
    return $this->template;
  }

  /**
   * Getter for inline template.
   *
   * @return string|null
   *   Asset template.
   */
  public function getInlineTemplate(): ?string {
    return $this->inlineTemplate;
  }

  /**
   * Getter for asset vars.
   *
   * @return array
   *   Asset template variables.
   */
  public function getVars(): array {
    return $this->vars;
  }

  /**
   * Getter for asset action.
   *
   * @return string|callable
   *   Asset action.
   */
  public function getAction() {
    return $this->action;
  }

  /**
   * Getter for asset header size.
   *
   * @return int
   *   Asset header size.
   */
  public function getHeaderSize(): int {
    return $this->headerSize;
  }

  /**
   * Getter asset resolver.
   *
   * @return callable|null
   *   Asset resolver.
   */
  public function getResolver(): ?callable {
    return $this->resolver;
  }

  /**
   * Setter for asset content.
   *
   * @param string|null $content
   *   Asset content.
   *
   * @return self
   *   The asset.
   */
  public function content(?string $content): self {
    $this->content = $content;
    return $this;
  }

  /**
   * Setter for asset header template.
   *
   * @param string|null $header_template
   *   Asset template.
   *
   * @return self
   *   The asset.
   */
  public function headerTemplate(?string $header_template): self {
    $this->headerTemplate = self::addTwigFileExtension($header_template);
    return $this;
  }

  /**
   * Setter for asset template.
   *
   * @param string|null $template
   *   Asset template.
   *
   * @return self
   *   The asset.
   */
  public function template(?string $template): self {
    if ($template !== NULL) {
      $this->template = self::addTwigFileExtension($template);
    }
    return $this;
  }

  /**
   * Setter for asset template.
   *
   * @param string|null $inline_template
   *   The template string to render.
   *
   * @return self
   *   The asset.
   */
  public function inlineTemplate(?string $inline_template): self {
    $this->inlineTemplate = $inline_template;
    return $this;
  }

  /**
   * Setter for asset vars.
   *
   * @param array $vars
   *   Asset template variables.
   *
   * @return self
   *   The asset.
   */
  public function vars(array $vars): self {
    $this->vars = $vars;
    return $this;
  }

  /**
   * Sets "replace" action.
   *
   * @return self
   *   The asset.
   */
  public function replaceIfExists(): self {
    $this->action = self::ACTION_REPLACE;
    return $this;
  }

  /**
   * Sets "prepend" action.
   *
   * @return self
   *   The asset.
   */
  public function prependIfExists(): self {
    $this->action = self::ACTION_PREPEND;
    return $this;
  }

  /**
   * Sets "append" action.
   *
   * @return self
   *   The asset.
   */
  public function appendIfExists(): self {
    $this->action = self::ACTION_APPEND;
    return $this;
  }

  /**
   * Sets "skip" action.
   *
   * @return self
   *   The asset.
   */
  public function skipIfExists(): self {
    $this->action = self::ACTION_SKIP;
    return $this;
  }

  /**
   * Setter for asset header size.
   *
   * @param int $header_size
   *   Asset header size.
   *
   * @return self
   *   The asset.
   */
  public function headerSize(int $header_size): self {
    if ($header_size <= 0) {
      throw new \InvalidArgumentException('Header size must be greater than or equal to 0.');
    }
    $this->headerSize = $header_size;
    return $this;
  }

  /**
   * Setter for asset resolver.
   *
   * @param callable|null $resolver
   *   A callable responsible for resolving content.
   *   @code
   *     $resolver = function (?string $existing_content, ?string $generated_content) ?string {
   *       if ($existing_content !== NULL) {
   *         return $generated_content . "\n" . $existing_content;
   *       }
   *       return $generated_content;
   *     }
   *   @endcode
   *
   * @return \DrupalCodeGenerator\Asset\Asset
   *   The asset.
   */
  public function resolver(?callable $resolver): self {
    $this->resolver = $resolver;
    return $this;
  }

  /**
   * Adds Twig extension if needed.
   */
  private static function addTwigFileExtension(string $template): string {
    if ($template && pathinfo($template, PATHINFO_EXTENSION) != 'twig') {
      $template .= '.twig';
    }
    return $template;
  }

  /**
   * {@inheritdoc}
   */
  public function replaceTokens(array $vars): void {
    parent::replaceTokens($vars);
    if ($this->template) {
      $this->template = Utils::replaceTokens($this->template, $vars);
    }
    if ($this->headerTemplate) {
      $this->headerTemplate = Utils::replaceTokens($this->headerTemplate, $vars);
    }
  }

}

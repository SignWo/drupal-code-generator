<?php

namespace DrupalCodeGenerator;

/**
 * Helper methods for code generators.
 */
class Utils {

  /**
   * Transforms a machine name to human name.
   */
  public static function machine2human(string $machine_name) :string {
    return ucfirst(trim(str_replace('_', ' ', $machine_name)));
  }

  /**
   * Transforms a human name to machine name.
   */
  public static function human2machine(string $human_name) :string {
    return trim(preg_replace(
      ['/^[0-9]+/', '/[^a-z0-9_]+/'],
      '_',
      strtolower($human_name)
    ), '_');
  }

  /**
   * Transforms a camelized sting to machine name.
   */
  public static function camel2machine($input) :string {
    return self::human2machine(preg_replace('/[A-Z]/', ' \0', $input));
  }

  /**
   * Camelize a string.
   */
  public static function camelize(string $input, $upper_camel = TRUE) :string {
    $output = preg_replace('/([^A-Z])([A-Z])/', '$1 $2', $input);
    $output = strtolower($output);
    $output = preg_replace('/[^a-z0-9]/', ' ', $output);
    $output = trim($output);
    $output = ucwords($output);
    $output = str_replace(' ', '', $output);
    return $upper_camel ? $output : lcfirst($output);
  }

  /**
   * Machine name validator.
   */
  public static function validateMachineName(string $value) :string {
    if (!preg_match('/^[a-z][a-z0-9_]*[a-z0-9]$/', $value)) {
      throw new \UnexpectedValueException('The value is not correct machine name.');
    }
    return $value;
  }

  /**
   * Class name validator.
   *
   * @see http://php.net/manual/en/language.oop5.basic.php
   */
  public static function validateClassName(string $value) :string {
    if (!preg_match('/^[A-Z][a-zA-Z0-9]+$/', $value)) {
      throw new \UnexpectedValueException('The value is not correct class name.');
    }
    return $value;
  }

  /**
   * Service name validator.
   */
  public static function validateServiceName($value) :?string {
    if ($value !== '' && $value !== NULL && !preg_match('/^[a-z][a-z0-9_\.]*[a-z0-9]$/', $value)) {
      throw new \UnexpectedValueException('The value is not correct service name.');
    }
    return $value;
  }

  /**
   * Required value validator.
   */
  public static function validateRequired(?string $value) :?string {
    // FALSE is not considered as empty value because question helper use
    // it as negative answer on confirmation questions.
    if ($value === NULL || $value === '') {
      throw new \UnexpectedValueException('The value is required.');
    }
    return $value;
  }

  /**
   * Returns a validator for allowed options.
   *
   * @param array $options
   *   Allowed values.
   *
   * @return callable
   *   Question validator.
   */
  public static function getOptionsValidator(array $options) :callable {
    return function ($value) use ($options) {
      if (!in_array($value, $options)) {
        $options_formatted = implode(', ', $options);
        $error_message = sprintf('The value should be one of the following: %s.', $options_formatted);
        throw new \UnexpectedValueException($error_message);
      }
      return $value;
    };
  }

  /**
   * Returns extension root.
   *
   * @return string|bool
   *   Extension root directory or false if it was not found.
   */
  public static function getExtensionRoot(string $directory) :string {
    $extension_root = FALSE;
    for ($i = 1; $i <= 5; $i++) {
      $info_file = $directory . '/' . basename($directory) . '.info';
      if ((file_exists($info_file) && basename($directory) !== 'drush') || file_exists($info_file . '.yml')) {
        $extension_root = $directory;
        break;
      }
      $directory = dirname($directory);
    }
    return $extension_root;
  }

  /**
   * Removes a given number of lines from the beginning of the string.
   */
  public static function removeHeader(string $content, int $header_size) :string {
    return implode("\n", array_slice(explode("\n", $content), $header_size));
  }

  /**
   * Return the user's home directory.
   *
   * @return string|bool
   *   User's home directory or FALSE.
   */
  public static function getHomeDirectory() {
    return isset($_SERVER['HOME']) ? $_SERVER['HOME'] : getenv('HOME');
  }

  /**
   * Replaces all tokens in a given string with appropriate values.
   *
   * @param string|null $text
   *   A string potentially containing replaceable tokens.
   * @param array $data
   *   An array where keys are token names and values are replacements.
   *
   * @return string|null
   *   Text with tokens replaced.
   */
  public static function replaceTokens(?string $text, array $data) :?string {

    if (!$text || !$data) {
      return $text;
    }

    $process_token = function (array $matches) use ($data) :string {
      list($name, $filter) = array_pad(explode('|', $matches[1], 2), 2, NULL);

      if (!array_key_exists($name, $data)) {
        throw new \UnexpectedValueException(sprintf('Variable "%s" is not defined', $name));
      }
      $result = (string) $data[$name];

      if ($filter) {
        switch ($filter) {
          case 'u2h';
            $result = str_replace('_', '-', $result);
            break;

          case 'h2u';
            $result = str_replace('-', '_', $result);
            break;

          case 'h2m';
            $result = self::human2machine($result);
            break;

          case 'm2h';
            $result = self::machine2human($result);
            break;

          case 'camelize':
            $result = self::camelize($result);
            break;

          case 'c2m':
            $result = self::camel2machine($result);
            break;

          default;
            throw new \UnexpectedValueException(sprintf('Filter "%s" is not defined', $filter));
        }
      }
      return $result;
    };

    return preg_replace_callback('/{(.+?)\}/', $process_token, $text);
  }

  /**
   * Pluralizes a noun.
   *
   * @param string $string
   *   A noun to pluralize.
   *
   * @return string
   *   The pluralized noun.
   */
  public static function pluralize(string $string) :string {
    switch (substr($string, -1)) {
      case 'y':
        return substr($string, 0, -1) . 'ies';

      case 's':
        return $string . 'es';

      default:
        return $string . 's';
    }
  }

}

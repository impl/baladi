<?php
/*
 * This file is part of Baladi, an API documentation generator.
 * Copyright (c) 2009 Bitextender GmbH. All rights reserved.
 *
 * For more information on licensing conditions, see the LICENSE file that
 * is distributed with this source code.
 */
namespace baladi\log;

require_once('severity.php');

/**
 * Represents a logging mechanism.
 *
 * All logs are capable of handling error message expressions. They accept a
 * {@link sprintf}-style string with additional parameters.
 */
interface ILog {

  /**
   * Reports a notice-level log message.
   *
   * @param      string The message to record.
   * @param...   mixed Additional parameters for the message.
   */
  public function note($message);

  /**
   * Reports a information-level log message.
   *
   * @param      string The message to record.
   * @param...   mixed Additional parameters for the message.
   */
  public function inform($message);

  /**
   * Reports a warning-level log message.
   *
   * @param      string The message to record.
   * @param...   mixed Additional parameters for the message.
   */
  public function warn($message);

  /**
   * Reports an error-level log message.
   *
   * @param      string The message to record.
   * @param...   mixed Additional parameters for the message.
   */
  public function err($message);

}
?>
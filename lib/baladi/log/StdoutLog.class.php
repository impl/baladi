<?php
/*
 * This file is part of Baladi, an API documentation generator.
 * Copyright (c) 2009 Bitextender GmbH. All rights reserved.
 *
 * For information on licensing conditions, see the LICENSE file that is
 * distributed with this source code.
 */
namespace baladi\log;

/**
 * Records log messages to the standard output and error pipes associated with
 * the currently-running program.
 */
class StdoutLog implements ILog {

  /**
   * The severity mask to report.
   *
   * @var        int
   */
  protected $severity = severity\ALL;

  /**
   * The standard output pipe.
   *
   * @var        resource
   */
  protected $stdout;

  /**
   * The standard error pipe.
   *
   * @var        resource
   */
  protected $stderr;

  /**
   * Creates a new standard output/error logging mechanism.
   */
  public function __construct() {
    $this->stdout = fopen('php://stdout', 'wb');
    $this->stderr = fopen('php://stderr', 'wb');
  }

  /**
   * Sets the severity mask for this logging mechanism.
   *
   * @param      int The severity mask.
   */
  public function setSeverity($severity) {
    $this->severity = $severity;
  }

  /**
   * A set that maps severity levels to appropriate string representations.
   *
   * @var        string{int}
   */
  protected static $map = array(severity\NOTICE => 'NOTICE',
                                severity\INFORMATION => 'INFO',
                                severity\WARNING => 'WARNING',
                                severity\ERROR => 'ERROR');

  /**
   * Writes a message to the log.
   *
   * @param      resource The resource to which the log message is to be sent.
   * @param      int The message severity.
   * @param      string The message.
   * @param      mixed[] Additional parameters to the message.
   */
  protected function write($resource, $severity, $message, $arguments) {
    $log = sprintf('[%s] [%-8s] %s', date('Y-m-d H:i:s'), self::$map[$severity], vsprintf($message, $arguments));
    fwrite($resource, $log . PHP_EOL);
  }

  /**
   * @see        ILog#note()
   */
  public function note($message) {
    if(severity\includes($this->severity, severity\NOTICE)) {
      $arguments = func_get_args();
      $arguments = array_slice($arguments, 1);
      $this->write($this->stdout, severity\NOTICE, $message, $arguments);
    }
  }

  /**
   * @see        ILog#inform()
   */
  public function inform($message) {
    if(severity\includes($this->severity, severity\INFORMATION)) {
      $arguments = func_get_args();
      $arguments = array_slice($arguments, 1);
      $this->write($this->stdout, severity\INFORMATION, $message, $arguments);
    }
  }

  /**
   * @see        ILog#warn()
   */
  public function warn($message) {
    if(severity\includes($this->severity, severity\WARNING)) {
      $arguments = func_get_args();
      $arguments = array_slice($arguments, 1);
      $this->write($this->stderr, severity\WARNING, $message, $arguments);
    }
  }

  /**
   * @see        ILog#err()
   */
  public function err($message) {
    if(severity\includes($this->severity, severity\ERROR)) {
      $arguments = func_get_args();
      $arguments = array_slice($arguments, 1);
      $this->write($this->stderr, severity\ERROR, $message, $arguments);
    }
  }
  
}
?>
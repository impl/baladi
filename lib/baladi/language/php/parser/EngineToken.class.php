<?php
/*
 * This file is part of Baladi, an API documentation generator.
 * Copyright (c) 2009 Bitextender GmbH. All rights reserved.
 *
 * For information on licensing conditions, see the LICENSE file that is
 * distributed with this source code.
 */
namespace baladi\language\php\parser;

/**
 * Represents a named token, like the namespace or function keywords.
 */
class EngineToken extends Token {

  /**
   * The token type.
   *
   * @var        int
   */
  protected $type;

  /**
   * The line number on which this token occurs.
   *
   * @var        int
   */
  protected $line;

  /**
   * Creates a new named token.
   *
   * @param      int The token's type.
   * @param      string The token's contents.
   * @param      int The token's line number.
   */
  public function __construct($type, $contents, $line) {
    $this->contents = $contents;
    $this->type = $type;
    $this->line = $line;
  }

  /**
   * Retrieves this token's type.
   *
   * @return     int The token's type.
   */
  public function getType() {
    return $this->type;
  }

  /**
   * Retrieves the line number on which this token occurs.
   *
   * @return     int The token's line number.
   */
  public function getLine() {
    return $this->line;
  }

  /**
   * @see        Token#isToken()
   */
  public function isToken($token) {
    return $this->type === $token;
  }

}
?>
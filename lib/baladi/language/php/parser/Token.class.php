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
 * Represents a single PHP token.
 */
abstract class Token {

  /**
   * The contents of this token.
   *
   * @var        string
   */
  protected $contents;

  /**
   * Retrieves the contents of this token.
   *
   * @return     string The token contents.
   */
  public function getContents() {
    return $this->contents;
  }

  /**
   * Determines whether this token is the same as another token.
   *
   * @return     bool True if the tokens are the same; false otherwise.
   */
  abstract public function isToken($token);

}
?>
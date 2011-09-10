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
 * Represents a token that is a single character, like a bracket, comma, or
 * brace.
 */
class CharacterToken extends Token {

  /**
   * Creates a new character token.
   *
   * @param      string The character.
   */
  public function __construct($character) {
    $this->contents = $character;
  }

  /**
   * @see        Token#isToken()
   */
  public function isToken($token) {
    return $this->getContents() === $token;
  }

}
?>
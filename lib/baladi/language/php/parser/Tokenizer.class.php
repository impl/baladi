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
 * Represents a segment of PHP source code as a list of tokens.
 */
class Tokenizer implements \IteratorAggregate {

  /**
   * The iterator used by this tokenizer.
   *
   * @var        TokenIterator
   */
  protected $iterator = null;

  /**
   * Creates a new tokenizer.
   *
   * @param      mixed[] The input tokens. These are in the format specified by
   *                     {@link token_get_all()}.
   */
  public function __construct(array $tokens) {
    $list = array();

    foreach($tokens as $token) {
      if(is_array($token))
        $list[] = new EngineToken($token[0], $token[1], $token[2]);
      else
        $list[] = new CharacterToken($token);
    }

    $this->iterator = new TokenIterator($list);
  }

  /**
   * @see        IteratorAggregate#getIterator()
   */
  public function getIterator() {
    return $this->iterator;
  }

  /**
   * Retrieves the list of tokens being handled by this tokenizer.
   *
   * @return     Token[] A list of tokens.
   */
  public function getTokens() {
    return $this->getIterator()->toArray();
  }

}
?>
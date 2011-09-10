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
 * Iterates over tokens.
 *
 * Note that unlike the standard PHP array iterator, this iterator can go
 * both forwards and backwards. Why this wasn't implemented in PHP itself I'll
 * never know, as the standard functions can do it quite well.
 *
 * Somewhere I suspect that the blind copying of Java resulted in a
 * misunderstanding of software development ideologies in general. Not that PHP
 * itself is much of a testament to software quality....
 */
class TokenIterator implements \Iterator, \SeekableIterator, \Countable {

  /**
   * The tokens that are being iterated over.
   *
   * @var        Token[]
   */
  protected $tokens = array();

  /**
   * The number of tokens being iterated over.
   *
   * @var        int
   */
  protected $count;

  /**
   * The current position in the array.
   *
   * @var        int
   */
  protected $position = 0;

  /**
   * Creates a new token iterator.
   *
   * @param      Token[] The tokens to iterate over.
   */
  public function __construct(array $tokens) {
    $this->tokens = $tokens;
    $this->count = count($tokens);
  }

  /**
   * @see        Countable#count()
   */
  public function count() {
    return $this->count();
  }

  /**
   * @see        Iterator#rewind()
   */
  public function rewind() {
    $this->position = 0;
  }

  /**
   * @see        Iterator#current()
   */
  public function current() {
    if($this->position >= $this->count)
      throw new \OutOfBoundsException('Invalid token offset');

    return $this->tokens[$this->position];
  }

  /**
   * @see        Iterator#key()
   */
  public function key() {
    return $this->position;
  }

  /**
   * @see        Iterator#next()
   */
  public function next() {
    $this->position++;
  }

  /**
   * Moves the position in the token array backward by one.
   *
   * For some reason, this isn't implemented in ArrayIterator. I hate
   * incompetency.
   */
  public function prev() {
    $this->position--;
  }

  /**
   * @see        SeekableIterator#seek()
   */
  public function seek($position) {
    $this->position = $position;
  }

  /**
   * @see        Iterator#valid()
   */
  public function valid() {
    return isset($this->tokens[$this->position]);
  }

  /**
   * Retrieves the array of tokens used by this iterator.
   *
   * @return     Token[] The tokens that are being iterated over.
   */
  public function toArray() {
    return $this->tokens;
  }

  /**
   * Gets the nearest line to this one. This moves backwards through tokens
   * until one is an instance of an {@link EngineToken}.
   *
   * The iterator itself is not modified.
   *
   * @return     int The nearest line.
   */
  public function getNearestLine() {
    for($i = $this->position; $i >= 0; $i++) {
      if($this->tokens[$i] instanceof EngineToken)
        return $this->tokens[$i]->getLine();
    }

    /* We got all the way back to the start, so we must be at the first line.
     */
    return 1;
  }

}
?>
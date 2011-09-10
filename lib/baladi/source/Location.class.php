<?php
/*
 * This file is part of Baladi, an API documentation generator.
 * Copyright (c) 2009 Bitextender GmbH. All rights reserved.
 *
 * For information on licensing conditions, see the LICENSE file that is
 * distributed with this source code.
 */
namespace baladi\source;

/**
 * Represents the location of a particular element of source code.
 *
 * Location representations take the form of a tuple, containing an
 * {@link ISource} and a line number.
 */
class Location {

  /**
   * The source segment.
   *
   * @var        ISource
   */
  protected $source;

  /**
   * The line number.
   *
   * @var        int
   */
  protected $line;

  /**
   * Creates a new location.
   *
   * @param      ISource The source segment.
   * @param      int The line number.
   */
  public function __construct(ISource $source, $line) {
    $this->source = $source;
    $this->line = $line;
  }

  /**
   * Retrieves the source segment for this location.
   *
   * @return     ISource The source segment.
   */
  public function getSource() {
    return $this->source;
  }

  /**
   * Retrieves the line number for this location.
   *
   * @return     int The line number.
   */
  public function getLine() {
    return $this->line;
  }

}
?>
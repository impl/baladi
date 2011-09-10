<?php
/*
 * This file is part of Baladi, an API documentation generator.
 * Copyright (c) 2009 Bitextender GmbH. All rights reserved.
 *
 * For information on licensing conditions, see the LICENSE file that is
 * distributed with this source code.
 */
namespace baladi\scanner;

/**
 * Filters a scanner so that source code found in Mercurial-owned directories
 * is never matched.
 */
class MercurialFilterScanner extends \FilterIterator implements IScanner {

  /**
   * Creates a new Mercurial filter scanner.
   *
   * @param      IScanner The inner scanner.
   */
  public function __construct(IScanner $scanner) {
    parent::__construct($scanner);
  }

  /**
   * @see        FilterIterator#accept()
   */
  public function accept() {
    $source = $this->current();
    return !($source instanceof \baladi\source\IFileSource
             && ($source->match('#(^|/)\.hg(/|$)#') || $source->match('#\.hgignore$#')));
  }

  /**
   * @see        IScanner#scan()
   */
  public function scan() {
    $results = array();
    foreach($this as $result)
      $results[] = $result;

    return $results;
  }

}
?>
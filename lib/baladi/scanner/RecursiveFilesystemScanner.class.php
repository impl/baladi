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
 * Recursively scans a directory for files.
 */
class RecursiveFilesystemScanner extends FilesystemScanner {

  /**
   * The internal iterator used by this scanner.
   *
   * @var        RecursiveIteratorIterator
   */
  protected $iterator;

  /**
   * Creates a new recursive filesystem scanner.
   *
   * @param      string The directory to scan.
   */
  public function __construct($directory) {
    parent::__construct($directory);

    $this->iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->getDirectory()));
  }

  /**
   * @see        Iterator#rewind()
   */
  public function rewind() {
    $this->iterator->rewind();
  }

  /**
   * @see        Iterator#current()
   */
  public function current() {
    return new \baladi\source\FilesystemSource($this->iterator->current());
  }

  /**
   * @see        Iterator#key()
   */
  public function key() {
    return $this->iterator->key();
  }

  /**
   * @see        Iterator#next()
   */
  public function next() {
    return $this->iterator->next();
  }

  /**
   * @see        Iterator#valid()
   */
  public function valid() {
    return $this->iterator->valid();
  }

}
?>
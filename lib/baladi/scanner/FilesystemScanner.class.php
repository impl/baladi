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
 * Scans a filesystem and returns a subset of files from a particular
 * directory.
 */
abstract class FilesystemScanner implements IScanner {

  /**
   * The directory to scan.
   *
   * @var        string
   */
  protected $directory;

  /**
   * Creates a new filesystem scanner.
   *
   * @param      string The directory to scan for source code.
   */
  public function __construct($directory) {
    $this->directory = $directory;
  }

  /**
   * Retrieves the directory to scan.
   *
   * @return     string The directory to scan.
   */
  public function getDirectory() {
    return $this->directory;
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
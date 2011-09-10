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
 * Contains an actual file.
 *
 * The file may be stored on some other form of permanent storage than a
 * filesystem, but must be a file.
 */
interface IFileSource extends ISource, \baladi\scanner\IMatchable {

  /**
   * Retrieves the base name (filename without preceding directories) for this
   * source segment.
   *
   * @return     string The file's base name.
   */
  public function getBaseName();

  /**
   * Retrieves the time the file was last modified as a UNIX timestamp.
   *
   * @return     int The time the file was last modified.
   */
  public function getModifiedTime();

  /**
   * Retrieves a URL representation of this source segment.
   *
   * @return     string A URL representation of this source segment.
   */
  public function getUri();

}
?>
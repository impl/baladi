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
 * Contains a segment of source code.
 *
 * Segments of source code must be able to be expressed as a file.
 */
interface ISource {

  /**
   * Retrieves the content of the source.
   *
   * @return     string The source content.
   */
  public function getContents();

  /**
   * Retrieves a path that can be used to read the source code as a file.
   *
   * This is necessary for some parsers which use filesystem-based wrappers to
   * reflect the contents of a file.
   *
   * @return     string A filesystem path to the source code.
   */
  public function getPath();

  /**
   * Retrieves a generic representation of the source code segment.
   *
   * This will often be in the form of a URI.
   *
   * @return     string A generic representation of the source segment.
   */
  public function asString();
}
?>
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
 * A representation of a file source that exists on a locally-accessible
 * filesystem.
 */
class FilesystemSource implements IFileSource {

  /**
   * Creates a new filesystem source.
   *
   * @param      SplFileInfo The file.
   */
  public function __construct(\SplFileInfo $file) {
    $this->file = $file;
  }

  /**
   * @see        ISource#getContents()
   */
  public function getContents() {
    return file_get_contents($this->file->getRealPath());
  }

  /**
   * @see        ISource#getPath()
   */
  public function getPath() {
    return $this->file->getRealPath();
  }

  /**
   * @see        ISource#asString()
   */
  public function asString() {
    return $this->getUri();
  }

  /**
   * @see        IFileSource#getBaseName()
   */
  public function getBaseName() {
    return $this->file->getFilename();
  }

  /**
   * @see        IFileSource#getModifiedTime()
   */
  public function getModifiedTime() {
    return $this->file->getMTime();
  }

  /**
   * @see        IFileSource#getUri()
   */
  public function getUri() {
    return sprintf('file://%s', $this->file->getRealPath());
  }

  /**
   * @see        IMatchable#match()
   */
  public function match($expression) {
    if(($result = @preg_match($expression, $this->file->getRealPath())) === false)
      throw new \baladi\scanner\InvalidMatchExpressionException(sprintf('The expression "%s" does not compile', $expression));

    return $result > 0;
  }

  /**
   * Represents this source code segment as a string.
   *
   * Equivalent to calling {@link #asString()}.
   *
   * @return     string A string representation of the source segment.
   */
  public function __toString() {
    return $this->asString();
  }

}
?>
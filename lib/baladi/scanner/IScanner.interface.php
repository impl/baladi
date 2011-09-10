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
 * Represents a scanner that can locate {@link baladi\source\ISource} segments.
 */
interface IScanner extends \Iterator {

  /**
   * Retrieves the {@link baladi\source\ISource} segments associated with this
   * scanner.
   *
   * @return     baladi\source\ISource[] An array of source code segments.
   */
  public function scan();

}
?>
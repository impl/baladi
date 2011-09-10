<?php
/*
 * This file is part of Baladi, an API documentation generator.
 * Copyright (c) 2009 Bitextender GmbH. All rights reserved.
 *
 * For more information on licensing conditions, see the LICENSE file that
 * is distributed with this source code.
 */
namespace baladi\log;

/**
 * A logging mechansim that does nothing at all.
 */
class NullLog implements ILog {

  /**
   * @see        ILog#note()
   */
  public function note($message) { }

  /**
   * @see        ILog#inform()
   */
  public function inform($message) { }

  /**
   * @see        ILog#warn()
   */
  public function warn($message) { }

  /**
   * @see        ILog#err()
   */
  public function err($message) { }

}
?>
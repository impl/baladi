<?php
/*
 * This file is part of Baladi, an API documentation generator.
 * Copyright (c) 2009 Bitextender GmbH. All rights reserved.
 *
 * For information on licensing conditions, see the LICENSE file that is
 * distributed with this source code.
 */
namespace baladi\log;

/**
 * Indicates that a given object accepts a log object.
 */
interface ILogAcceptor {

  /**
   * Sets the log mechanism for this object.
   *
   * @param      ILog The log for this object.
   */
  public function setLog(ILog $log);

}

?>
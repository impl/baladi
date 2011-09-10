<?php
/*
 * This file is part of Baladi, an API documentation generator.
 * Copyright (c) 2009 Bitextender GmbH. All rights reserved.
 *
 * For information on licensing conditions, see the LICENSE file that is
 * distributed with this source code.
 */
namespace baladi\language;

/**
 * Thrown by a matcher.
 *
 * Matcher exceptions are assumed to be recoverable exceptions, so processing
 * will continue after they are thrown.
 */
class MatcherException extends \Exception {

  protected $location = null;

  public function setLocation(\baladi\source\Location $location) {
    $this->location = $location;
  }

  public function hasLocation() {
    return $this->location !== null;
  }

  public function getLocation() {
    return $this->location;
  }

  public function __toString() {
    return sprintf('%s: %s', __CLASS__, $this->getMessage()) .
      ($this->hasLocation() ? sprintf(' at %s:%s', $this->location->getSource()->asString(), $this->location->getLine()) : '');
  }

}
?>
<?php
/*
 * This file is part of Baladi, an API documentation generator.
 * Copyright (c) 2009 Bitextender GmbH. All rights reserved.
 *
 * For information on licensing conditions, see the LICENSE file that is
 * distributed with this source code.
 */
namespace baladi\language\php\parser;

use baladi\language\php\model;

class ParserContextStack {

  protected $environment;
  protected $stack = array();

  public function __construct(\baladi\language\php\Environment $environment) {
    $this->environment = $environment;
  }

  public function getEnvironment() {
    return $this->environment;
  }

  public function push(model\_Namespace $namespace) {
    array_unshift($this->stack, new ParserContext($this, $namespace));
  }

  public function pop() {
    array_shift($this->stack);
  }

  public function current() {
    return isset($this->stack[0]) ? $this->stack[0] : null;
  }

  public function clear() {
    $this->stack = array();
  }

}
?>
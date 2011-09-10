<?php
/*
 * This file is part of Baladi, an API documentation generator.
 * Copyright (c) 2009 Bitextender GmbH. All rights reserved.
 *
 * For information on licensing conditions, see the LICENSE file that is
 * distributed with this source code.
 */
namespace baladi\language\php;

class Environment {

  protected $namespaces = array();
  protected $globalNamespace;

  protected $builtinTypes = array();

  public function __construct() {
    $this->globalNamespace = new model\_Namespace();
    
    $this->builtinTypes[model\type\IS_ARRAY] = new model\BuiltinType();
  }

  public function getGlobalNamespace() {
    return $this->globalNamespace;
  }

  public function getBuiltinType($type) {
    return $this->builtinTypes[$type];
  }

}
?>
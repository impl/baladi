<?php
/*
 * This file is part of Baladi, an API documentation generator.
 * Copyright (c) 2009 Bitextender GmbH. All rights reserved.
 *
 * For information on licensing conditions, see the LICENSE file that is
 * distributed with this source code.
 */
namespace baladi\language\php\model;

class UnresolvedClassifier implements IClassifier {

  protected $constants = array();

  public function getLocation() {
    return null;
  }

  public function hasLocation() {
    return false;
  }

  public function setName($name) {
    $this->name = $name;
  }

  public function getName() {
    return $this->name;
  }

  public function addConstant(_Constant $constant) {
    $this->constants[] = $constant;
  }

  public function getConstants() {
    return $this->constants;
  }

}
?>
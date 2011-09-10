<?php
/*
 * This file is part of Baladi, an API documentation generator.
 * Copyright (c) 2009 Bitextender GmbH. All rights reserved.
 *
 * For information on licensing conditions, see the LICENSE file that is
 * distributed with this source code.
 */
namespace baladi\language\php\model;

class Parameter implements INamed, IAssignable {

  protected $name;
  protected $type;
  protected $directions = direction\IN;
  protected $value = null;

  public function setName($name) {
    $this->name = $name;
  }

  public function getName() {
    return $this->name;
  }

  public function setType(IType $type) {
    $this->type = $type;
  }

  public function getType() {
    return $this->type;
  }

  public function addDirection($direction) {
    $this->directions |= $direction;
  }

  public function hasDirection($direction) {
    return $this->directions & $direction;
  }

  public function getDirections() {
    return $this->directions;
  }

  public function setValue($value) {
    $this->value = $value;
  }

  public function hasValue() {
    return $this->value !== null;
  }

  public function getValue() {
    return $this->value;
  }
  
}
?>
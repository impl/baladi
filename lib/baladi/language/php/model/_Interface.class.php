<?php
/*
 * This file is part of Baladi, an API documentation generator.
 * Copyright (c) 2009 Bitextender GmbH. All rights reserved.
 *
 * For information on licensing conditions, see the LICENSE file that is
 * distributed with this source code.
 */
namespace baladi\language\php\model;

class _Interface implements IClassifier {

  protected $name;
  protected $location = null;
  protected $subinterfaces = array();
  protected $implementers = array();
  protected $methods = array();
  protected $constants = array();
  protected $comments = array();

  public function setLocation(\baladi\source\Location $location) {
    $this->location = $location;
  }

  public function hasLocation() {
    return $this->location !== null;
  }

  public function getLocation() {
    return $this->location;
  }

  public function setName($name) {
    $this->name = $name;
  }

  public function getName() {
    return $this->name;
  }

  public function addComment($comment) {
    $this->comments[] = $comment;
  }

  public function getComments() {
    return $this->comments;
  }

  public function addMethod(_Method $method) {
    $this->methods[] = $method;
  }

  public function getMethods() {
    return $this->methods;
  }

  public function addConstant(_Constant $constant) {
    $this->constants[] = $constant;
  }

  public function getConstants() {
    return $this->constants;
  }

  public function addSubinterface(_Interface $interface) {
    $this->subinterfaces[] = $interface;
  }

  public function addImplementer(_Class $class) {
    $this->implementers[] = $class;
  }

}
?>
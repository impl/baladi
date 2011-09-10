<?php
/*
 * This file is part of Baladi, an API documentation generator.
 * Copyright (c) 2009 Bitextender GmbH. All rights reserved.
 *
 * For information on licensing conditions, see the LICENSE file that is
 * distributed with this source code.
 */
namespace baladi\language\php\model;

class _Class implements IClassifier {

  protected $location = null;
  protected $name;
  protected $comments = array();
  protected $methods = array();
  protected $constants = array();
  protected $properties = array();
  protected $subclasses = array();
  protected $features = feature\NONE;

  public static function fromUnresolvedClassifier(UnresolvedClassifier $classifier) {
    $instance = new self();
    $instance->setName($classifier->getName());

    foreach($classifier->getConstants() as $constant)
      $instance->addConstant($constant);

    return $instance;
  }

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

  public function addProperty(_Property $property) {
    $this->properties[] = $property;
  }

  public function getProperties() {
    return $this->properties;
  }

  public function addFeature($feature) {
    $this->features |= $feature;
  }

  public function hasFeature($feature) {
    return $this->features & $feature;
  }

  public function addSubclass(_Class $class) {
    $this->subclasses[] = $class;
  }

}
?>
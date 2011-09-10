<?php
/*
 * This file is part of Baladi, an API documentation generator.
 * Copyright (c) 2009 Bitextender GmbH. All rights reserved.
 *
 * For information on licensing conditions, see the LICENSE file that is
 * distributed with this source code.
 */
namespace baladi\language\php\model;

class _Method implements IElement, INamed, IOperation {

  protected $name;
  protected $visibility = visibility\IS_DEFAULT;
  protected $features = feature\NONE;
  protected $comments = array();
  protected $parameters = array();

  public function setName($name) {
    $this->name = $name;
  }

  public function getName() {
    return $this->name;
  }

  public function setVisibility($visibility) {
    $this->visibility = $visibility;
  }

  public function getVisibility() {
    return $this->visibility;
  }

  public function addComment($comment) {
    $this->comments[] = $comment;
  }

  public function getComments() {
    return $this->comments;
  }

  public function addFeature($feature) {
    $this->features |= $feature;
  }

  public function hasFeature($feature) {
    return $this->features & $feature;
  }

  public function addParameter(Parameter $parameter) {
    $this->parameters[] = $parameter;
  }

  public function getParameters() {
    return $this->parameters;
  }

}
?>
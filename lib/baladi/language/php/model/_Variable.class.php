<?php
/*
 * This file is part of Baladi, an API documentation generator.
 * Copyright (c) 2009 Bitextender GmbH. All rights reserved.
 *
 * For information on licensing conditions, see the LICENSE file that is
 * distributed with this source code.
 */
namespace baladi\language\php\model;

class _Variable implements IElement, INamed, IAssignable {

  protected $name;
  protected $comments = array();
  protected $value = null;

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
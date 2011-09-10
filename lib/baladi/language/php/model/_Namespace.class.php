<?php
/*
 * This file is part of Baladi, an API documentation generator.
 * Copyright (c) 2009 Bitextender GmbH. All rights reserved.
 *
 * For information on licensing conditions, see the LICENSE file that is
 * distributed with this source code.
 */
namespace baladi\language\php\model;

class _Namespace implements IDeclarative {

  protected $name;
  protected $namespaces = array();
  protected $functions = array();
  protected $constants = array();
  protected $variables = array();
  protected $classifiers = array();

  public function setName($name) {
    $this->name = $name;
  }

  public function getName() {
    return $this->name;
  }

  public function addFunction(_Function $function) {
    $this->functions[] = $function;
  }

  public function getFunctions() {
    return $this->functions;
  }

  public function addConstant(_Constant $constant) {
    $this->constants[] = $constant;
  }

  public function getConstants() {
    return $this->constants;
  }

  public function addVariable(_Variable $variable) {
    $this->variables[] = $variable;
  }

  public function getVariables() {
    return $this->variables;
  }

  public function addClassifier(IClassifier $classifier) {
    $this->classifiers[$classifier->getName()] = $classifier;
  }

  public function getClassifier($name) {
    if(!$this->hasClassifier($name))
      throw new \baladi\language\php\EnvironmentException(sprintf('No class "%s" exists in namespace', $name));

    return $this->classifiers[$name];
  }

  public function getClassifiers() {
    return array_values($this->classifiers);
  }

  public function hasClassifier($name) {
    return isset($this->classifiers[$name]);
  }

  public function addNamespace(_Namespace $namespace) {
    $this->namespaces[$namespace->getName()] = $namespace;
  }

  public function getNamespace($name) {
    if(!$this->hasNamespace($name))
      throw new \baladi\language\php\EnvironmentException(sprintf('No namespace "%s" exists in namespace', $name));

    return $this->namespaces[$name];
  }

  public function getNamespaces() {
    return array_values($this->namespaces);
  }

  public function hasNamespace($name) {
    return isset($this->namespaces[$name]);
  }

}
?>
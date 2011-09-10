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

class ParserContext {

  protected $aliases = array();
  protected $stack;
  protected $namespace;

  public function __construct(ParserContextStack $stack, model\_Namespace $namespace) {
    $this->stack = $stack;
    $this->namespace = $namespace;
  }

  public function setNamespace(model\_Namespace $namespace) {
    $this->namespace = $namespace;
  }

  public function getNamespace() {
    return $this->namespace;
  }

  /**
   * Aliases an array of components to a string.
   *
   * @param      string[] The components of the original element.
   * @param      string The new name.
   */
  public function addAlias(array $from, $to) {
    $this->aliases[$to] = $from;
  }

  /**
   * Resolves a potentially aliased class, interface, or namespace name to a
   * fully-qualified name.
   *
   * @param      string[] The partially resolved name.
   *
   * @return     string[] The fully resolved name if the name is aliased;
   *                      otherwise null;
   */
  protected function resolveAlias(array $components) {
    if(!isset($components[0]) || !isset($this->aliases[$components[0]]))
      return null;

    $name = array_shift($components);
    return array_merge($this->aliases[$name], $components);
  }

  public function getClassifierByNamespace(model\_Namespace $namespace, $name) {
    try {
      $classifier = $namespace->getClassifier($name);
    }
    catch(\baladi\language\php\EnvironmentException $ee) {
      $classifier = new model\UnresolvedClassifier();
      $classifier->setName($name);

      $namespace->addClassifier($classifier);
    }

    return $classifier;
  }

  public function getClassifierByLocallyQualifiedName(array $components) {
    $name = array_pop($components);
    return $this->getClassifierByNamespace($this->getNamespaceByLocallyQualifiedName($components), $name);
  }

  public function getClassifierByPartiallyQualifiedName(array $components) {
    if(($alias = $this->resolveAlias($components)) !== null)
      return $this->getClassifierByQualifiedName($alias);
    else {
      $name = array_pop($components);
      return $this->getClassifierByNamespace($this->getNamespaceByLocallyQualifiedName($components), $name);
    }
  }

  public function getClassifierByQualifiedName(array $components) {
    $name = array_pop($components);
    return $this->getClassifierByNamespace($this->getNamespaceByQualifiedName($components), $name);
  }

  public function getClassByNamespace(model\_Namespace $namespace, $name) {
    try {
      $class = $namespace->getClassifier($name);

      if($class instanceof model\UnresolvedClassifier)
        throw new \baladi\language\php\EnvironmentException();
    }
    catch(\baladi\language\php\EnvironmentException $ee) {
      $class = new model\_Class();
      $class->setName($name);

      $namespace->addClassifier($class);
    }

    if(!$class instanceof model\_Class)
      throw new ParserException(sprintf('Unexpected classifier type "%s" for "%s" (expected class)', get_class($class), $class->getName()));

    return $class;
  }

  public function getClassByLocallyQualifiedName(array $components) {
    $name = array_pop($components);
    return $this->getClassByNamespace($this->getNamespaceByLocallyQualifiedName($components), $name);
  }

  public function getClassByPartiallyQualifiedName(array $components) {
    if(($alias = $this->resolveAlias($components)) !== null)
      return $this->getClassByQualifiedName($alias);
    else {
      $name = array_pop($components);
      return $this->getClassByNamespace($this->getNamespaceByLocallyQualifiedName($components), $name);
    }
  }

  public function getClassByQualifiedName(array $components) {
    $name = array_pop($components);
    return $this->getClassByNamespace($this->getNamespaceByQualifiedName($components), $name);
  }

  public function getInterfaceByNamespace(model\_Namespace $namespace, $name) {
    try {
      $interface = $namespace->getClassifier($name);

      if($interface instanceof model\UnresolvedClassifier)
        throw new \baladi\language\php\EnvironmentException();
    }
    catch(\baladi\language\php\EnvironmentException $ee) {
      $interface = new model\_Interface();
      $interface->setName($name);

      $namespace->addClassifier($interface);
    }

    if(!$interface instanceof model\_Interface)
      throw new ParserException(sprintf('Unexpected classifier type "%s" for "%s" (expected interface)', get_class($interface), $interface->getName()));

    return $interface;
  }

  public function getInterfaceByLocallyQualifiedName(array $components) {
    $name = array_pop($components);
    return $this->getInterfaceByNamespace($this->getNamespaceByLocallyQualifiedName($components), $name);
  }

  public function getInterfaceByPartiallyQualifiedName(array $components) {
    if(($alias = $this->resolveAlias($components)) !== null)
      return $this->getInterfaceByQualifiedName($alias);
    else {
      $name = array_pop($components);
      return $this->getInterfaceByNamespace($this->getNamespaceByLocallyQualifiedName($components), $name);
    }
  }

  public function getInterfaceByQualifiedName(array $components) {
    $name = array_pop($components);
    return $this->getInterfaceByNamespace($this->getNamespaceByQualifiedName($components), $name);
  }

  public function getNamespaceHavingParent(model\_Namespace $parent, array $components) {
    $count = count($components);
    for($i = 0; $i < $count; $i++) {
      try {
        $child = $parent->getNamespace($components[$i]);
      }
      catch(\baladi\language\php\EnvironmentException $ee) {
        $child = new model\_Namespace();
        $child->setName($components[$i]);

        $parent->addNamespace($child);
      }

      /* And the new parent is the child. Funny, huh? */
      $parent = $child;
    }

    /* Now $parent is the top of the stack. */
    return $parent;
  }

  public function getNamespaceByLocallyQualifiedName(array $components) {
    /* We start at the current namespace and go down. */
    return $this->getNamespaceHavingParent($this->namespace, $components);
  }

  public function getNamespaceByPartiallyQualifiedName(array $components) {
    if(($alias = $this->resolveAlias($components)) !== null)
      return $this->getNamespaceByQualifiedName($alias);
    else
      return $this->getNamespaceByLocallyQualifiedName($components);
  }

  public function getNamespaceByQualifiedName(array $components) {
    return $this->getNamespaceHavingParent($this->stack->getEnvironment()->getGlobalNamespace(), $components);
  }

}
?>
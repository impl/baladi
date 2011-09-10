<?php
/*
 * This file is part of Baladi, an API documentation generator.
 * Copyright (c) 2009 Bitextender GmbH. All rights reserved.
 *
 * For information on licensing conditions, see the LICENSE file that is
 * distributed with this source code.
 */
namespace baladi\language\php;

class LanguageApplicator extends Applicator {

  /**
   * @see        IApplicator#apply()
   */
  public function apply(\baladi\source\ISource $source) {
    $parser = new parser\Parser($this->getEnvironment(), $source);
    $parser->parse();
  }

  public function printComment($comment) {
    printf("%s\n%s\n%s\n", str_repeat('-', 80), $comment, str_repeat('-', 80));
  }

  public static $visibilityMap = array(model\visibility\IS_PUBLIC => 'public',
                                       model\visibility\IS_PROTECTED => 'protected',
                                       model\visibility\IS_PRIVATE => 'private');

  public static $directionMap = array(model\direction\IN => '',
                                      model\direction\OUT => '&',
                                      model\direction\BOTH => '&');

  public function printFunction(model\_Function $function) {
    printf("%s~ <<TYPE>> %s", do_indent(),
           $function->getName());
    $params = array();
    foreach($function->getParameters() as $parameter) {
      $param = '';
      $t = $parameter->getType();
      if($t instanceof model\IClassifier)
        $param .= sprintf($t->getName());
      elseif($t === $this->getEnvironment()->getBuiltinType(model\type\IS_ARRAY))
        $param .= sprintf('<<ARRAY>>');
      else
        $param .= sprintf('<<TYPE>>');

      $param .= sprintf(' %s$%s', self::$directionMap[$parameter->getDirections()], $parameter->getName());
      if($parameter->hasValue())
        $param .= sprintf(' = %s', $parameter->getValue());

      $params[] = $param;
    }
    printf("(%s):\n", implode(', ', $params));
    add_indent();
    printf("%s- Comments:\n", do_indent());
    foreach($function->getComments() as $comment) {
      $this->printComment($comment);
    }
    sub_indent();
  }

  public function printMethod(model\_Method $method) {
    printf("%s~ %s %s%s%s<<TYPE>> %s", do_indent(),
           self::$visibilityMap[$method->getVisibility()],
           $method->hasFeature(model\feature\IS_STATIC) ? 'static ' : '',
           $method->hasFeature(model\feature\IS_ABSTRACT) ? 'abstract ' : '',
           $method->hasFeature(model\feature\IS_FINAL) ? 'final ' : '',
           $method->getName());
    $params = array();
    foreach($method->getParameters() as $parameter) {
      $param = '';
      $t = $parameter->getType();
      if($t instanceof model\IClassifier)
        $param .= sprintf($t->getName());
      elseif($t === $this->getEnvironment()->getBuiltinType(model\type\IS_ARRAY))
        $param .= sprintf('<<ARRAY>>');
      else
        $param .= sprintf('<<TYPE>>');

      $param .= sprintf(' %s$%s', self::$directionMap[$parameter->getDirections()], $parameter->getName());
      if($parameter->hasValue())
        $param .= sprintf(' = %s', $parameter->getValue());

      $params[] = $param;
    }
    printf("(%s):\n", implode(', ', $params));
    add_indent();
    printf("%s- Comments:\n", do_indent());
    foreach($method->getComments() as $comment) {
      $this->printComment($comment);
    }
    sub_indent();
  }

  public function printProperty(model\_Property $property) {
    printf("%s. %s %s<<TYPE>> $%s%s:\n", do_indent(),
           self::$visibilityMap[$property->getVisibility()],
           $property->hasFeature(model\feature\IS_STATIC) ? 'static ' : '',
           $property->getName(),
           $property->hasValue() ? sprintf(' = %s', $property->getValue()) : '');
    add_indent();
    printf("%s- Comments:\n", do_indent());
    foreach($property->getComments() as $comment) {
      $this->printComment($comment);
    }
    sub_indent();
  }

  public function printConstant(model\_Constant $constant) {
    printf("%sX const <<TYPE>> %s = %s:\n", do_indent(),
           $constant->getName(),
           $constant->getValue());
    add_indent();
    printf("%s- Comments:\n", do_indent());
    foreach($constant->getComments() as $comment) {
      $this->printComment($comment);
    }
    sub_indent();
  }

  public function printClassifierCrap(model\IClassifier $classifier) {
    printf("%s- Comments:\n", do_indent());
    foreach($classifier->getComments() as $comment) {
      $this->printComment($comment);
    }
    printf("%s- Location: %s\n", do_indent(),
           $classifier->hasLocation()
             ? sprintf('%s:%s', $classifier->getLocation()->getSource()->asString(), $classifier->getLocation()->getLine())
             : '<<UNKNOWN>>');
    printf("%s- Methods:\n", do_indent());
    add_indent();
    $methods = $classifier->getMethods();
    foreach($methods as &$method) {
      $this->printMethod($method);
    }
    sub_indent();
    printf("%s- Constants:\n", do_indent());
    add_indent();
    $consts = $classifier->getConstants();
    foreach($consts as &$constant) {
      $this->printConstant($constant);
    }
    sub_indent();
  }

  public function printClass(model\_Class $class) {
    printf("%sT class %s:\n", do_indent(), $class->getName());
    add_indent();
    $this->printClassifierCrap($class);
    printf("%s- Properties:\n", do_indent());
    add_indent();
    $props = $class->getProperties();
    foreach($props as &$property) {
      $this->printProperty($property);
    }
    sub_indent();
    sub_indent();
  }

  public function printInterface(model\_Interface $interface) {
    printf("%sT interface %s:\n", do_indent(), $interface->getName());
    add_indent();
    $this->printClassifierCrap($interface);
    sub_indent();
  }

  public function printClassifier(model\IClassifier $classifier) {
    if($classifier instanceof model\_Class)
      $this->printClass($classifier);
    elseif($classifier instanceof model\_Interface)
      $this->printInterface($classifier);
    else
      printf("%sT <<UNKNOWN/UNRESOLVED>> %s:\n", do_indent(), $classifier->getName());
  }

  public function printNamespace(model\_Namespace $namespace) {
    printf("%sT namespace %s:\n", do_indent(), $namespace->getName());
    add_indent();
    printf("%s- Functions:\n", do_indent());
    add_indent();
    $funx = $namespace->getFunctions();
    foreach($funx as &$function) {
      $this->printFunction($function);
    }
    sub_indent();
    printf("%s- Constants:\n", do_indent());
    add_indent();
    $consts = $namespace->getConstants();
    foreach($consts as &$constant) {
      $this->printConstant($constant);
    }
    sub_indent();
    printf("%s- Classifiers:\n", do_indent());
    add_indent();
    $clx = $namespace->getClassifiers();
    foreach($clx as &$classifier)
      $this->printClassifier($classifier);
    sub_indent();
    printf("%s- Namespaces:\n", do_indent());
    add_indent();
    $nms = $namespace->getNamespaces();
    foreach($nms as &$namespace)
      $this->printNamespace($namespace);
    sub_indent();
    sub_indent();
  }

  public function reduce() {
    /*    foreach($this->getEnvironment()->getNamespaces() as $namespace) {
      $this->printNamespace($namespace);
    }
    */
    //    $gbl = $this->getEnvironment()->getGlobalNamespace();
    //    $gbl->setName('<<GLOBAL>>');
    //$this->printNamespace($gbl);
  }

}
?>
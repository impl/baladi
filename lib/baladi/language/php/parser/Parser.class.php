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

class Parser {

  protected $environment;

  /**
   * The context stack.
   *
   * Okay, so what, I used a stack? I heard <em>your mom</em> uses registers.
   * Yeah, that's right. Take that.
   *
   * No, but seriously, I'm not sure this can ever get deeper than, uh, one,
   * but it's handy none the less.
   *
   * @var        ParserContextStack
   */
  protected $contexts;

  protected $source;

  protected $tokenizer;

  public function __construct(\baladi\language\php\Environment $environment, \baladi\source\ISource $source) {
    $this->environment = $environment;
    $this->source = $source;

    $this->contexts = new ParserContextStack($environment);

    $this->tokenizer = new Tokenizer(token_get_all($source->getContents()));
  }

  /**
   * Handler for the initial state.
   *
   * This method calls {@link #parseScript} when it encounters an opening PHP
   * tag.
   */
  protected function parseInitial() {
    $i = $this->tokenizer->getIterator();

    while($i->valid()) {
      if($i->current()->isToken(T_OPEN_TAG))
        $this->parseScriptBlock();

      $i->next();
    }
  }

  /**
   * Handler for a top-level segment of PHP code.
   *
   * Looks for type and variable declarations as well as the top-level
   * declarations for namespaces and namespace aliasing.
   */
  protected function parseScriptBlock() {
    $this->contexts->push($this->environment->getGlobalNamespace());

    $i = $this->tokenizer->getIterator();

    while($i->valid()) {
      $token = $i->current();
      if($token->isToken(T_NAMESPACE))
        $this->parseNamespace();
      elseif($token->isToken(T_USE))
        $this->parseUse();
      elseif($token->isToken(T_CLASS))
        $this->parseClass();
      elseif($token->isToken(T_INTERFACE))
        $this->parseInterface();
      elseif($token->isToken(T_FUNCTION))
        $this->parseFunction();
      elseif($token->isToken(T_VARIABLE))
        $this->parseVariable();
      elseif($token->isToken(T_CONST))
        $this->parseConstant($this->contexts->current()->getNamespace());
      elseif($token->isToken(T_CLOSE_TAG)) {
        break;
      }

      $i->next();
    }

    $this->contexts->pop();
  }

  protected function parseBlock() {
    $i = $this->tokenizer->getIterator();
    $depth = 0;

    /* Inside non-namespace- or script-level blocks, we don't really care about
     * anything other than class, interface, and function definitions, because
     * those still end up in the global scope.
     *
     * I guess we could check for T_GLOBAL and see if there are any comments on
     * those variables, but in this day and age that seems unnecessary. */
    while(true) {
      $token = $i->current();
      if($token->isToken(T_FUNCTION))
        $this->parseFunction();
      elseif($token->isToken(T_CLASS))
        $this->parseClass();
      elseif($token->isToken(T_INTERFACE))
        $this->parseInterface();
      elseif($token->isToken('{'))
        $depth++;
      elseif($token->isToken('}')) {
        $depth--;
        if($depth === 0)
          break;
      }

      $i->next();
    }
  }

  protected function parseNamespace() {
    $i = $this->tokenizer->getIterator();

    /* The position token, a T_NAMESPACE. */
    $p = $i->current();

    $i->next(); $this->consume();
    try {
      $namespace = $this->contexts->current()->getNamespaceByQualifiedName($this->getDeclarationQualifiedName());
    }
    catch(ParserException $pe) {
      /* Ugh, okay, so here's the deal. PHP has multiple contexts in which the
       * namespace keyword is valid. You can either declare a namespace like
       * <code>namespace My\Name\Space</code>, or you can use the namespace
       * keyword to explicitly access an element from the current namespace
       * like <code>namespace\function\call();</code>.
       *
       * This language is worse than anything I have ever seen. Jesus fucking
       * Christ, who thought this is a good idea?!
       *
       * Actually, on second glance, there's /three/ unique ways you can use
       * the namespace operator. The third one is like this:
       *
       * <code>
       *   namespace {
       *     // Things in the global namespace!
       *   }
       * </code>
       *
       * Sigh.
       */
      if($i->current()->isToken('{'))
        $this->parseNamespaceBlock($this->environment->getGlobalNamespace());

      return;
    }

    /* Mmmm... */
    $i->next(); $this->consume();

    /* Now we see whether we have an opening bracket (in which case we have a
     * new block) or a semicolon (in which case we don't). It's like C++ and
     * Java combined, and you get the best of neither world!
     */
    if($i->current()->isToken('{')) {
      $this->parseNamespaceBlock($namespace);
    }
    elseif($i->current()->isToken(';'))
      $this->contexts->current()->setNamespace($namespace);
    else
      throw new ParserException(sprintf('Invalid token "%s" (expecting ";" or "{") after namespace declaration', $i->current()->getContents()));
  }

  protected function parseNamespaceBlock(model\_Namespace $namespace) {
    $this->contexts->push($namespace);

    $i = $this->tokenizer->getIterator();
    $depth = 0;

    while(true) {
      $token = $i->current();
      if($token->isToken(T_USE))
        $this->parseUse();
      elseif($token->isToken(T_FUNCTION))
        $this->parseFunction();
      elseif($token->isToken(T_VARIABLE))
        $this->parseVariable();
      elseif($token->isToken(T_CLASS))
        $this->parseClass();
      elseif($token->isToken(T_INTERFACE))
        $this->parseInterface();
      elseif($token->isToken(T_CONST))
        $this->parseConstant($namespace);
      elseif($token->isToken('{'))
        $depth++;
      elseif($token->isToken('}')) {
        $depth--;
        if($depth === 0)
          break;
      }

      $i->next();
    }

    $this->contexts->pop();
  }

  protected function parseUse() {
    /* I want to make this clear: PHP, I hate you.
     *
     * % php -r '
     * quote > namespace Foo {
     * quote >   class Bar {
     * quote >     function __construct() { echo "lol"; }
     * quote >   }
     * quote > }
     * quote > namespace Foo\Bar {
     * quote >   function lol() { echo "dongs"; }
     * quote > }
     * quote > namespace {
     * quote >   use Foo\Bar as Baz;
     * quote >   new Baz();
     * quote >   Baz\lol();
     * quote > }'
     * loldongs
     *
     * Go ahead, try it.
     */
    $i = $this->tokenizer->getIterator();

    $i->next(); $this->consume();

    while(true) {
      $name = $this->getDeclarationQualifiedName();

      $i->next(); $this->consume();
      if($i->current()->isToken(T_AS)) {
        $i->next(); $this->consume();
        $this->contexts->current()->addAlias($name, $this->getUnqualifiedName());
        $i->next(); $this->consume();
      }
      else
        $this->contexts->current()->addAlias($name, end($name));

      if(!$i->current()->isToken(','))
        break;

      $i->next(); $this->consume();
    }

    if(!$i->current()->isToken(';'))
      throw new ParserException(sprintf('Invalid token "%s" (expecting ";")', $i->current()->getContents()));
  }

  protected function parseFunction() {
    $i = $this->tokenizer->getIterator();

    $function = new model\_Function();

    /* Save the current position. */
    $position = $i->key();

    /* Go back one to get off the T_FUNCTION. */
    $i->prev();

    /* And see if there's a documentation comment. */
    try {
      $function->addComment($this->getDocumentationComment());
    }
    catch(ParserException $pe) { }

    /* Okay, back to where we started. */
    $i->seek($position);

    /* Mmm... */
    $i->next(); $this->consume();
    $function->setName($this->getString());
    $i->next(); $this->consume();

    /* Now read the parameter list. */
    $this->parseParameters($function);

    /* Chomp chomp chomp. */
    $i->next(); $this->consume();

    if(!$i->current()->isToken('{'))
      throw new ParserException(sprintf('Invalid token "%s" (expecting "{")', $i->current()->getContents()));

    $this->parseBlock();

    $this->contexts->current()->getNamespace()->addFunction($function);
  }

  protected function parseClass() {
    $i = $this->tokenizer->getIterator();

    /* We should have a T_STRING here -- the class name. */
    $i->next(); $this->consume();
    $class = $this->contexts->current()->getClassByLocallyQualifiedName(array($this->getUnqualifiedName()));

    /* Integrity check. */
    if($class->hasLocation())
      throw new ParserException(sprintf('Class "%s" already defined at %s:%s; cannot redefine', $class->getName(),
                                        $class->getLocation()->getSource()->asString(), $class->getLocation()->getLine()));

    /* Great. Either we just created the class or we've got a reference to it;
     * however, we know for a fact that it's defined in this file.
     *
     * Woe be us when PHP supports partial classes (!). */
    $class->setLocation(new \baladi\source\Location($this->source, $i->getNearestLine()));

    /* Save the current position. */
    $position = $i->key();

    /* Scrolling, scrolling, scrolling... */
    $i->prev(); $this->unconsume();

    /* Read backwards and look for class features. */
    while(true) {
      $i->prev();

      $token = $i->current();
      if($token->isToken(T_ABSTRACT))
        $class->addFeature(model\feature\IS_ABSTRACT);
      elseif($token->isToken(T_FINAL))
        $class->addFeature(model\feature\IS_FINAL);
      elseif(!$token->isToken(T_WHITESPACE))
        break;
    }

    /* Keep going back and see if we have a documentation comment. */
    try {
      $class->addComment($this->getDocumentationComment());
    }
    catch(ParserException $pe) { }

    /* Now reset to where we were and read forward. */
    $i->seek($position);
    $i->next(); $this->consume();

    /* Okay, now get superclass and interface information. */
    if($i->current()->isToken(T_EXTENDS)) {
      $i->next(); $this->consume();

      $superclass = $i->current()->isToken(T_NS_SEPARATOR)
        ? $this->contexts->current()->getClassByQualifiedName($this->getQualifiedName())
        : $this->contexts->current()->getClassByPartiallyQualifiedName($this->getPartiallyQualifiedName());
      $superclass->addSubclass($class);

      $i->next(); $this->consume();
    }

    if($i->current()->isToken(T_IMPLEMENTS)) {
      do {
        $i->next(); $this->consume();

        $interface = $i->current()->isToken(T_NS_SEPARATOR)
          ? $this->contexts->current()->getInterfaceByQualifiedName($this->getQualifiedName())
          : $this->contexts->current()->getInterfaceByPartiallyQualifiedName($this->getPartiallyQualifiedName());
        $interface->addImplementer($class);

        $i->next(); $this->consume();
      } while($i->current()->isToken(','));
    }

    if(!$i->current()->isToken('{'))
      throw new ParserException(sprintf('Invalid class declaration (expected "{", found "%s")', $i->current()->getContents()));

    /* Parse the class body. */
    while(true) {
      $token = $i->current();
      if($token->isToken(T_FUNCTION))
        $this->parseMethod($class);
      elseif($token->isToken(T_CONST))
        $this->parseConstant($class);
      elseif($token->isToken(T_VARIABLE))
        $this->parseProperty($class);
      elseif($token->isToken('}'))
        break;

      $i->next();
    }
  }

  protected function parseInterface() {
    $i = $this->tokenizer->getIterator();

    /* We should have a T_STRING here -- the interface name. */
    $i->next(); $this->consume();
    $interface = $this->contexts->current()->getInterfaceByLocallyQualifiedName(array($this->getUnqualifiedName()));

    /* Integrity check. */
    if($interface->hasLocation())
      throw new ParserException(sprintf('Interface "%s" already defined at %s:%s; cannot redefine', $class->getName(),
                                        $interface->getLocation()->getSource()->asString(), $interface->getLocation()->getLine()));

    /* And partial interfaces, too. */
    $interface->setLocation(new \baladi\source\Location($this->source, $i->getNearestLine()));

    /* Save the current position. */
    $position = $i->key();

    /* Scrolling, scrolling, scrolling... */
    $i->prev(); $this->unconsume();

    /* Now we're back at T_INTERFACE. Om nom once more to get into OH MY GOD
     * FREE WILLY! */
    $i->prev();

    /* Interfaces don't have any interesting features, so keep going back and
     * see if we have a documentation comment. */
    try {
      $interface->addComment($this->getDocumentationComment());
    }
    catch(ParserException $pe) { }

    /* Now reset to where we were and read forward. */
    $i->seek($position);
    $i->next(); $this->consume();

    /* Let's see if we extend any other interfaces. Interfaces support multiple
     * inheritance. */
    if($i->current()->isToken(T_EXTENDS)) {
      do {
        $i->next(); $this->consume();

        $superinterface = $i->current()->isToken(T_NS_SEPARATOR)
          ? $this->contexts->current()->getInterfaceByQualifiedName($this->getQualifiedName())
          : $this->contexts->current()->getInterfaceByPartiallyQualifiedName($this->getPartiallyQualifiedName());
        $superinterface->addSubinterface($interface);

        $i->next(); $this->consume();
      } while($i->current()->isToken(','));
    }

    if(!$i->current()->isToken('{'))
      throw new ParserException(sprintf('Invalid interface declaration (expected "{", found "%s")', $i->current()->getContents()));

    /* Parse the interface body. */
    while(true) {
      $token = $i->current();
      if($token->isToken(T_FUNCTION))
        $this->parseMethod($interface, $defaultFeatures = model\feature\IS_ABSTRACT);
      elseif($token->isToken(T_CONST))
        $this->parseConstant($interface);
      elseif($token->isToken('}'))
        break;

      $i->next();
    }
  }

  protected function parseMethod(model\IClassifier $classifier, $defaultFeatures = model\feature\NONE) {
    $i = $this->tokenizer->getIterator();

    $method = new model\_Method();
    $method->addFeature($defaultFeatures);

    /* Save the current position. */
    $position = $i->key();

    /* Read backwards and look for features. */
    while(true) {
      $i->prev();

      $token = $i->current();
      if($token->isToken(T_ABSTRACT))
        $method->addFeature(model\feature\IS_ABSTRACT);
      elseif($token->isToken(T_FINAL))
        $method->addFeature(model\feature\IS_FINAL);
      elseif($token->isToken(T_STATIC))
        $method->addFeature(model\feature\IS_STATIC);
      elseif($token->isToken(T_PUBLIC))
        $method->setVisibility(model\visibility\IS_PUBLIC);
      elseif($token->isToken(T_PROTECTED))
        $method->setVisibility(model\visibility\IS_PROTECTED);
      elseif($token->isToken(T_PRIVATE))
        $method->setVisibility(model\visibility\IS_PRIVATE);
      elseif(!$token->isToken(T_WHITESPACE))
        break;
    }

    /* And see if there's a documentation comment. */
    try {
      $method->addComment($this->getDocumentationComment());
    }
    catch(ParserException $pe) { }

    /* Okay, back to where we started. */
    $i->seek($position);

    /* Mmm... */
    $i->next(); $this->consume();
    $method->setName($this->getString());
    $i->next(); $this->consume();

    /* Now read the parameter list. */
    $this->parseParameters($method);

    /* Chomp chomp chomp. */
    $i->next(); $this->consume();

    if($method->hasFeature(model\feature\IS_ABSTRACT)) {
      if(!$i->current()->isToken(';'))
        throw new ParserException(sprintf('Invalid token "%s" (expecting ";")', $i->current()->getContents()));
    }
    else {
      if(!$i->current()->isToken('{'))
        throw new ParserException(sprintf('Invalid token "%s" (expecting "{")', $i->current()->getContents()));

      $this->parseBlock();
    }

    $classifier->addMethod($method);
  }

  protected function parseParameters(model\IOperation $operation) {
    $i = $this->tokenizer->getIterator();

    if(!$i->current()->isToken('('))
      throw new ParserException(sprintf('Invalid token "%s" (expecting "(")', $i->current()->getContents()));

    $i->next(); $this->consume();

    /* Do we have any parameters? */
    if(!$i->current()->isToken(')')) {
      /* Yep! */
      while(true) {
        $parameter = new model\Parameter();

        /* See if the parameter is typed somehow. */
        if($i->current()->isToken(T_STRING)) {
          $type = $this->contexts->current()->getClassifierByPartiallyQualifiedName($this->getPartiallyQualifiedName());
          $parameter->setType($type);
          $i->next(); $this->consume();
        }
        elseif($i->current()->isToken(T_NS_SEPARATOR)) {
          $type = $this->contexts->current()->getClassifierByQualifiedName($this->getQualifiedName());
          $parameter->setType($type);
          $i->next(); $this->consume();
        }
        elseif($i->current()->isToken(T_ARRAY)) {
          $parameter->setType($this->environment->getBuiltinType(model\type\IS_ARRAY));
          $i->next(); $this->consume();
        }

        /* If it's passed by reference, add an output direction to the parameter. */
        if($i->current()->isToken('&')) {
          $parameter->addDirection(model\direction\out);
          $i->next(); $this->consume();
        }

        if(!$i->current()->isToken(T_VARIABLE))
          throw new ParserException(sprintf('Invalid token "%s" (expecting a variable)', $i->current()->getContents()));

        /* Set the parameter name (trimming off the "$" first). */
        $parameter->setName(substr($i->current()->getContents(), 1));
        $operation->addParameter($parameter);

        $i->next(); $this->consume();

        /* Now maybe we have a default assignment? */
        if($i->current()->isToken('=')) {
          $this->parseParameterAssignment($parameter, $stop = ',');
          $i->next(); $this->consume();
        }

        /* Do we have another parameter? If not, go ahead and break from the
         * loop. */
        if(!$i->current()->isToken(','))
          break;

        $i->next(); $this->consume();
      }

      if(!$i->current()->isToken(')'))
        throw new ParserException(sprintf('Invalid token "%s" (expecting ")")', $i->current()->getContents()));
    }
  }

  protected function parseParameterAssignment(model\Parameter $parameter) {
    $i = $this->tokenizer->getIterator();

    /* We're at a "=". Skip it and consume whitespace until we get to something
     * interesting. */
    $i->next(); $this->consume();

    $value = '';
    $depth = 0;
    while(true) {
      $token = $i->current();
      if($depth === 0 && ($token->isToken(')') || $token->isToken(',')))
        break;
      elseif($token->isToken('('))
        $depth++;
      elseif($token->isToken(')'))
        $depth--;

      $value .= $token->getContents();

      $i->next();
    }

    $parameter->setValue(rtrim($value));

    /* Scroll back so we're at the end of the parameter itself. */
    $i->prev(); $this->unconsume();
  }

  protected function parseConstant(model\IDeclarative $declarative) {
    $i = $this->tokenizer->getIterator();

    $constant = new model\_Constant();

    /* Nothing can precede a T_CONST, so see if we have a documentation comment. */
    $position = $i->key();

    try {
      $constant->addComment($this->getDocumentationComment());
    }
    catch(ParserException $pe) { }

    /* Now reset to where we were and read forward. */
    $i->seek($position);

    /* Set the name. */
    $i->next(); $this->consume();
    $constant->setName($this->getString());
    $i->next(); $this->consume();

    /* Now we make sure we have an '='. */
    if(!$i->current()->isToken('='))
      throw new ParserException(sprintf('Invalid token "%s" (expecting "=")', $i->current()->getContents()));

    /* And read the value. */
    $this->parseAssignment($constant);

    if(!$i->current()->isToken(';'))
      throw new ParserException(sprintf('Invalid token "%s" (expecting ";")', $i->current()->getContents()));

    $declarative->addConstant($constant);
  }

  protected function parseProperty(model\_Class $class) {
    $i = $this->tokenizer->getIterator();

    $property = new model\_Property();

    /* Save the current position. */
    $position = $i->key();

    /* Read backwards and look for features. */
    while(true) {
      $i->prev();

      $token = $i->current();
      if($token->isToken(T_STATIC))
        $property->addFeature(model\feature\IS_STATIC);
      elseif($token->isToken(T_PUBLIC))
        $property->setVisibility(model\visibility\IS_PUBLIC);
      elseif($token->isToken(T_PROTECTED))
        $property->setVisibility(model\visibility\IS_PROTECTED);
      elseif($token->isToken(T_PRIVATE))
        $property->setVisibility(model\visibility\IS_PRIVATE);
      elseif(!$token->isToken(T_WHITESPACE))
        break;
    }

    /* And see if there's a documentation comment. */
    try {
      $property->addComment($this->getDocumentationComment());
    }
    catch(ParserException $pe) { }

    /* Okay, back to where we started. */
    $i->seek($position);

    /* Get the name. */
    $property->setName(substr($i->current()->getContents(), 1));
    $i->next(); $this->consume();

    /* Now see if we have an '='. */
    if($i->current()->isToken('=')) {
      /* And read the value. */
      $this->parseAssignment($property);
    }

    if(!$i->current()->isToken(';'))
      throw new ParserException(sprintf('Invalid token "%s" (expecting ";")', $i->current()->getContents()));

    $class->addProperty($property);
  }

  protected function parseVariable() {
    $i = $this->tokenizer->getIterator();

    /* Save the current position. We're at a T_VARIABLE. */
    $position = $i->key();

    /* Scroll backwards through any whitespace. */
    $i->prev(); $this->unconsume();

    /* If the variable isn't explicitly preceded by a documentation comment, we
     * don't want to worry about it. Note that this really only handles
     * variables that are used at the namespace level, because
     * {@link #parseBlock()} doesn't handle T_VARIABLE. */
    $comment = '';
    try {
      $comment = $this->getDocumentationComment();
    }
    catch(ParserException $pe) {
      /* Yeah, we've got nothing. Probably some bootstrapping code or other
       * stuff people aren't going to want documented. */
      return;
    }

    /* Okay, create a new variable and save the comment. */
    $variable = new model\_Variable();
    $variable->addComment($comment);

    /* Right, back to where we were. */
    $i->seek($position);

    /* Get the name. */
    $variable->setName(substr($i->current()->getContents(), 1));
    $i->next(); $this->consume();

    /* Now see if we have an '='. We might not if the user wants to declare a
     * variable purely to document it. */
    if($i->current()->isToken('=')) {
      /* And read the value. */
      $this->parseAssignment($variable);
    }

    if(!$i->current()->isToken(';'))
      throw new ParserException(sprintf('Invalid token "%s" (expecting ";")', $i->current()->getContents()));

    $this->contexts->current()->getNamespace()->addVariable($variable);
  }

  protected function parseAssignment(model\IAssignable $assignable) {
    $i = $this->tokenizer->getIterator();

    /* We're at a "=". Skip it and consume whitespace until we get to something
     * interesting. */
    $i->next(); $this->consume();

    $value = '';
    $depth = 0;
    while(true) {
      $token = $i->current();
      if($depth === 0 && $token->isToken(';'))
        break;
      elseif($token->isToken('('))
        $depth++;
      elseif($token->isToken(')'))
        $depth--;

      $value .= $token->getContents();

      $i->next();
    }

    $assignable->setValue(rtrim($value));
  }

  protected function consume() {
    $i = $this->tokenizer->getIterator();

    /* Om nom nom nom. */
    while($i->current()->isToken(T_WHITESPACE))
      $i->next();
  }

  protected function unconsume() {
    $i = $this->tokenizer->getIterator();

    /* Mon mon mon mo. */
    while($i->current()->isToken(T_WHITESPACE))
      $i->prev();
  }

  protected function getDocumentationComment() {
    $i = $this->tokenizer->getIterator();

    while(true) {
      $token = $i->current();
      if($token->isToken(T_DOC_COMMENT))
        /* We trim off the beginning and ending of the comment to make things easier to parse later on. */
        return trim(substr($token->getContents(), 3, -2));
      elseif(!$token->isToken(T_WHITESPACE))
        throw new ParserException('No documentation comment found before current position');

      $i->prev();
    }
  }

  protected function getString() {
    $string = $this->tokenizer->getIterator()->current();
    if(!$string->isToken(T_STRING))
      throw new ParserException(sprintf('Invalid string "%s" (expecting string literal)', $string->getContents()));

    return $string->getContents();
  }

  protected function getUnqualifiedName() {
    return $this->getString();
  }

  protected function getQualifiedName() {
    $i = $this->tokenizer->getIterator();

    if(!$i->current()->isToken(T_NS_SEPARATOR))
      throw new ParserException(sprintf('Invalid qualified name initial "%s" (expecting namespace separator)', $i->current()->getContents()));

    $name = array();
    while(true) {
      $i->next();

      $token = $i->current();
      if($token->isToken(T_STRING))
        $name[] = $token->getContents();
      elseif(!$token->isToken(T_NS_SEPARATOR) && !$token->isToken(T_WHITESPACE))
        break;
    }

    /* Un-eat any whitespace we may have encountered. */
    $i->prev(); $this->unconsume();

    return $name;
  }

  protected function getDeclarationQualifiedName() {
    $i = $this->tokenizer->getIterator();

    if(!$i->current()->isToken(T_STRING))
      throw new ParserException(sprintf('Invalid name initial "%s" (expecting string literal)', $i->current()->getContents()));

    $name = array();
    while(true) {
      $token = $i->current();
      if($token->isToken(T_STRING))
        $name[] = $token->getContents();
      elseif(!$token->isToken(T_NS_SEPARATOR) && !$token->isToken(T_WHITESPACE))
        break;

      $i->next();
    }

    /* Un-eat any whitespace we may have encountered. */
    $i->prev(); $this->unconsume();

    return $name;
  }

  protected function getPartiallyQualifiedName() {
    $i = $this->tokenizer->getIterator();

    $name = array();
    if($i->current()->isToken(T_NAMESPACE)) {
      /* We store the token into the array so the context knows that we're
       * dealing with local resolution only. */
      $name[0] = $i->current();

      /* Move past it. */
      $i->next(); $i->consume();
      if(!$i->current()->isToken(T_NS_SEPARATOR))
        throw new ParserException('Invalid namespace-local declaration (expecting a namespace separator)');
      $i->next(); $i->consume();
    }

    return array_merge($name, $this->getDeclarationQualifiedName());
  }

  public function parse() {
    /* Move back to the beginning of the tokens for good measure. */
    $this->tokenizer->getIterator()->rewind();

    /* Clear our stack for good measure. */
    $this->contexts->clear();

    /* And parse it. */
    try {
      try {
        $this->parseInitial();
      }
      catch(\OutOfBoundsException $oobe) {
        throw new ParserException('Unexpected end of source segment');
      }
    }
    catch(ParserException $pe) {
      $pe->setLocation(new \baladi\source\Location($this->source, $this->tokenizer->getIterator()->getNearestLine()));
      throw $pe;
    }
  }

}
?>
<?php
/*
 * This file is part of Baladi, an API documentation generator.
 * Copyright (c) 2009 Bitextender GmbH. All rights reserved.
 *
 * For information on licensing conditions, see the LICENSE file that is
 * distributed with this source code.
 */
namespace baladi\language\php\documenter;

use baladi\language\php\model;

/**
 * Documents source code using a JavaDoc-style syntax.
 *
 * Documentation is handled in a format similar to JavaDoc. For more
 * information on how JavaDoc is represented, see Sun's guide at
 * http://java.sun.com/j2se/javadoc/writingdoccomments/.
 *
 * Documenting a syntactical element usually consists of a short, one-phrase
 * description (called the <name>simple description</name>), an optional longer
 * description (called the <name>extended description</name>), and a series of
 * commands that denote the element and are not present in the language syntax.
 *
 * Commands take the form:
 * <block>
 *   <code>@name[optional param1, optional param2...] param1 param2...</code>
 * </block>
 *
 * Some commands can also be used in-line in simple and extended descriptions.
 * These take the form:
 * <block>
 *   <code>!{@name[optional param1, optional param2...] param1
 *     param2...}</code>
 * </block>
 *
 * Unlike JavaDoc, we use an XML-based language for representing typographical
 * and lexical features like emphasis. The following tags are supported:
 *
 * <description-list>
 *   <item label="emph">Emphasized content.</item>
 *   <item lable="name">Content that represents a named identifier.</item>
 *   <item label="code">Source code of some nature. This is used for inline
 *     markup; to represent large amounts of code, use the
 *     <code>@example</code> command or the &lt;block&gt; element.</item>
 *   <item label="link">Links to some given URI. The URI is specified in the
 *     mandatory <code>uri</code> attribute.</item>
 *   <item label="heading">Denotes a heading. For simplicity reasons, 
 *   <item label="para">An explicit paragraph. Paragraphs are explicitly
 *     created whenever two successive newlines are encountered; however it may
 *     be convenient to denote them in some cases.</item>
 *   <item label="block">An inset block of text. This is useful for quotes or
 *     expressing a line or two of code.</code>
 *   <item label="itemized-list">An unordered list. Contains
 *     <code>&lt;item&gt;</code> tags.</item>
 *   <item label="enumerated-list">An ordered list. Contains
 *     <code>&lt;item&gt;</code> tags.</item>
 *   <item label="description-list">A description list. Contains
 *     <code>&lt;item&gt;</code> tags which support a <code>label</code>
 *     attribute to represent the name of the item being described.</item>
 * </description-list>
 *
 * In some cases, certain strings of text may be implicitly interpreted. To
 * prevent text from being implicitly interpreted, precede it by an exclamation
 * point (!!). (To write an exclamation point, first prevent it from being
 * interpreted: !!!!). For example, one might want to use a period as part of a
 * name or title in a simple description. Normally, periods conclude a simple
 * description. This behavior can be changed, however:
 * <block>
 *   Hashes a string according to Dr!!. D!!. J!!. Bernstein's CubeHash
 *   algorithm.
 * </block>
 *
 * The following parts of documentation must be able to be parsed as well-
 * formed XML nodes: simple descriptions, extended descriptions, and
 * descriptive parameters to commands.
 */
class Documenter {

  /* NB: There seems to be a bug in PHP somewhere (or I'm an idiot) that makes
   * iterating over an array break sometimes if it isn't done by-reference. */

  protected $environment;

  public function __construct(\baladi\language\php\Environment $environment) {
    $this->environment = $environment;
  }

  protected function strip($comment) {
    /* What the fuck is wrong with regular expressions? */
    return implode("\n", preg_replace('#\s*(?:\*[ \t\v]*)?(.*)\s*$#', '$1', preg_split('#\r\n|\r|\n#', $comment)));
  }

  protected function documentInitial() {
    $this->documentNamespace($this->environment->getGlobalNamespace());
  }

  protected function documentNamespace(model\_Namespace $namespace) {
    /* Recursively document namespaces. */
    $namespaces = $namespace->getNamespaces();
    foreach($namespaces as &$namespace) {
      $this->documentNamespace($namespace);
    }

    /* Namespaces can contain functions, classes, interfaces, variables, and
     * constants. */
    $functions = $namespace->getFunctions();
    foreach($functions as &$function) {
      $this->documentFunction($function);
    }

    $classifiers = $namespace->getClassifiers();
    foreach($classifiers as &$classifier) {
      $this->documentClassifier($classifier);
    }

    $variables = $namespace->getVariables();
    foreach($variables as &$variable) {
      $this->documentVariable($variable);
    }

    $constants = $namespace->getConstants();
    foreach($constants as &$constant) {
      $this->documentConstant($constant);
    }
  }

  protected function documentFunction(model\_Function $function) {
    /*$reader = new Reader($this->strip($function->getComment()));
      $model = $reader->read();*/
  }

  protected function documentClassifier(model\_Classifier $classifier) {
    /* We can document any classifier according to the documentation comment,
     * but we only support particular classifiers for component documentation
     * because they vary so wildly. */

    /* !!! NB: Here's what we're going to do. This is actually really ugly (I
     * hate using instanceof). So we're going to use references to point to
     * IClassifiers, whether or not they're resolved or not, and we only
     * *explicitly* create a class or interface in a namespace when we
     * encounter the declaration. This means we'll be able to do
     * $namespace->getClasses() and $namespace->getInterfaces(), but we'll be
     * be able to reference classes/interfaces without always knowing what they
     * are. We can also figure out which classifiers don't belong in the
     * current project (SPL, etc.) because references to them won't resolve.
     * This is cleaner than our current method, which uses an
     * `UnresolvedClassifier' to represent things we haven't figured out yet.
     *
     * At this point the biggest problem is going to be ensuring that we don't
     * have any naming conflicts -- we can't have a class and an interface with
     * the same name. The question is whether we realistically need to care
     * about this at the documentation level.
     *
     * (The problem is that we need to ensure uniqueness at the naming level,
     * but we want to optimize the script to return the list of classes and the
     * list of interfaces immediately.) BTW, this comment probably belongs in
     * `Parser', but you're editing this file heavily so I know you'll see it
     * again. :) */
  }

  protected function documentVariable(model\_Variable $variable) {
  }

  protected function documentConstant(model\_Constant $constant) {
  }

  public function document() {
    $this->documentInitial();
  }

}

/**
 * This documents the function below.
 *
 * @param null One two three.
 *
 * @author Noah Fontes <nfontes@cynigram.com>
 */
function f () { }
?>
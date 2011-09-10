<?php
/*
 * This file is part of Baladi, an API documentation generator.
 * Copyright (c) 2009 Bitextender GmbH. All rights reserved.
 *
 * For information on licensing conditions, see the LICENSE file that is
 * distributed with this source code.
 */
namespace baladi\language\php;

/**
 * Determines whether a given {@link baladi\source\ISource} is a suitable
 * representation of PHP source code and runs the necessary steps to create a
 * set of generators.
 */
class Matcher implements \baladi\language\IMatcher {

  /**
   * The PHP environment for this package.
   *
   * @var        Environment
   */
  protected $environment;

  /**
   * A list of applicators to run (in order, FIFO) to each segment of PHP
   * source code being parsed.
   *
   * @var        IApplicator[]
   */
  protected $applicators = array();

  /**
   * Creates a new matcher.
   *
   * The state of this package for the PHP matcher is maintained by an
   * {@link Environment} object.
   */
  public function __construct() {
    $this->environment = new Environment();
  }

  /**
   * Adds a new applicator to the list of applicators.
   *
   * @var        IApplicator The new applicator.
   */
  public function addApplicator(IApplicator $applicator) {
    $applicator->setEnvironment($this->environment);

    $this->applicators[] = $applicator;
  }

  /**
   * @see        baladi\language\IMatcher#match()
   */
  public function match(\baladi\source\ISource $source) {
    /* First, if we have a file, see if we can just make an assumption based on
     * the extension. Otherwise, we'll have to see what's up with the source.
     */
    if($source instanceof \baladi\source\IFileSource &&
       $source->match('#\.(php3?|phtml)$#i'))
      return true;

    /**
     * Okay, this is a bit rudimentary -- but it'll work for now.
     */
    return preg_match('#<\?(php|=|(?!xml|stylesheet))#i', $source->getContents()) > 0;
  }

  /**
   * @see        baladi\language\IMatcher#apply()
   */
  public function apply(\baladi\source\ISource $source) {
    foreach($this->applicators as $applicator) {
      $applicator->apply($source);
    }
  }

  /**
   * @see        baladi\language\IMatcher#reduce()
   */
  public function reduce() {
    foreach($this->applicators as $applicator) {
      $applicator->reduce();
    }

    return null;
  }

  /**
   * @see        baladi\language\IMatcher#asString()
   */
  public function asString() {
    return 'PHP';
  }

}
?>
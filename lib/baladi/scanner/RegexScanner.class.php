<?php
/*
 * This file is part of Baladi, an API documentation generator.
 * Copyright (c) 2009 Bitextender GmbH. All rights reserved.
 *
 * For information on licensing conditions, see the LICENSE file that is
 * distributed with this source code.
 */
namespace baladi\scanner;

/**
 * Matches source segments against inclusion and exclusion patterns.
 *
 * The expressions for matching are PHP-syntax PCRE patterns.
 *
 * @see        IMatchable
 */
class RegexScanner extends \FilterIterator implements IScanner {

  /**
   * The list of expressions that will result in the scan including the source
   * segment if matched.
   *
   * @var        string[]
   */
  protected $inclusions = array();

  /**
   * The list of expressions that will result in the scan excluding the source
   * segment if matched.
   *
   * @var        string[]
   */
  protected $exclusions = array();

  /**
   * The default result for source segments if they are not matched by any
   * expression.
   *
   * @var        bool
   */
  protected $default = false;

  /**
   * Creates a new regex scanner.
   *
   * @param      IScanner The inner scanner.
   */
  public function __construct(IScanner $scanner) {
    parent::__construct($scanner);
  }

  /**
   * Adds an expression to the list of inclusion expressions.
   *
   * @param      string The pattern to include.
   */
  public function addInclusion($expression) {
    $this->inclusions[] = $expression;
  }

  /**
   * Adds an expression to the list of exclusion expressions.
   *
   * @param      string The pattern to exclude.
   */
  public function addExclusion($expression) {
    $this->exclusions[] = $expression;
  }

  /**
   * Sets the default result for source segments if they are not matched by any
   * inclusion or exclusion expression.
   *
   * @param      bool True to include source segments by default; false to
   *                  exclude them.
   */
  public function setDefault($default) {
    $this->default = (bool)$default;
  }

  /**
   * @see        FilterIterator#accept()
   */
  public function accept() {
    $input = $this->current();
    if(!$input instanceof IMatchable)
      return $this->default;

    foreach($this->exclusions as $exclusion) {
      if($input->match($exclusion))
        return false;
    }

    foreach($this->inclusions as $inclusion) {
      if($input->match($inclusion))
        return true;
    }

    return $this->default;
  }

  /**
   * @see        IScanner#scan()
   */
  public function scan() {
    $results = array();
    foreach($this as $result)
      $results[] = $result;

    return $results;
  }

}
?>
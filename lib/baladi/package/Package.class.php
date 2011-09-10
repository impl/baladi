<?php
/*
 * This file is part of Baladi, an API documentation generator.
 * Copyright (c) 2009 Bitextender GmbH. All rights reserved.
 *
 * For information on licensing conditions, see the LICENSE file that is
 * distributed with this source code.
 */
namespace baladi\package;

/**
 * Represents a complete package of source segments which are to be parsed
 * according to a set of matching rules.
 */
class Package {

  protected $log = null;
  protected $scanners = array();
  protected $matchers = array();

  public function __construct() {
    $this->log = new \baladi\log\NullLog();
  }

  public function setLog(\baladi\log\ILog $log) {
    $this->log = $log;
  }

  public function getLog() {
    return $this->log;
  }

  public function addScanner(\baladi\scanner\IScanner $scanner) {
    $this->scanners[] = $scanner;
  }

  public function addMatcher(\baladi\language\IMatcher $matcher) {
    $this->matchers[] = $matcher;
  }

  protected function createAggregateParseResult() {
    return new \stdClass();
  }

  public function create() {
    $sources = array();

    $this->log->inform('Initializing new package');

    /* Scan the sources. */
    foreach($this->scanners as $scanner) {
      foreach($scanner as $source) {
        $this->log->note('Adding %s to package', $source->asString());
        $sources[] = $source;
      }
    }

    $this->log->inform('Found sources');

    /* Match the scanned sources. */
    foreach($this->matchers as $matcher) {
      $this->log->note('Applying matcher %s', $matcher->asString());
      foreach($sources as $source) {
        if($matcher->match($source)) {
          $this->log->note('  to %s', $source->asString());
          try {
            $matcher->apply($source);
          }
          catch(\baladi\language\MatcherException $me) {
            $this->log->err('Could not apply matcher to %s: %s%s',
                            $source->asString(),
                            $me->getMessage(), $me->hasLocation() ? sprintf(' at line %s', $me->getLocation()->getLine()) : '');
          }
        }
      }
    }

    $this->log->inform('Applied all matchers');

    /* Reduce the generators. */
    $generators = array();
    foreach($this->matchers as $matcher)
      $generators[] = $matcher->reduce();

    $this->log->inform('Created generators for package');

    return $generators;
  }

}

?>
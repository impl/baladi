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
 * Implements environment-handling methods of the {@link IApplicator}
 * interface and installs default (no-operation) handlers for the
 * {@link IApplicator#apply()} and {@link IApplicator#reduce()} methods.
 *
 * Generally, other applicators should extend this class.
 */
abstract class Applicator implements IApplicator {

  /**
   * The current environment.
   *
   * @var        Environment
   */
  protected $environment;

  /**
   * @see        IApplicator#setEnvironment()
   */
  public function setEnvironment(Environment $environment) {
    $this->environment = $environment;
  }

  /**
   * @see        IApplicator#getEnvironment()
   */
  public function getEnvironment() {
    return $this->environment;
  }

  /**
   * @see        IApplicator#apply()
   */
  public function apply(\baladi\source\ISource $source) { }

  /**
   * @see        IApplicator#reduce()
   */
  public function reduce() { }

}
?>
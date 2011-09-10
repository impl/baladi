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
 * Represents an object capable of interfacing with a PHP source code segment
 * to manipulate the PHP environment.
 */
interface IApplicator {

  /**
   * Sets the environment to be used for future applications.
   *
   * @param      Environment The new environment.
   */
  public function setEnvironment(Environment $environment);

  /**
   * Retrieves the environment currently being used.
   *
   * @return     Environment The current environment.
   */
  public function getEnvironment();

  /**
   * Applies this object to a given source code segment.
   *
   * @param      \baladi\source\ISource The source code segment.
   */
  public function apply(\baladi\source\ISource $source);

  /**
   * Performs any operations necessary to alter the application state after all
   * files have been evaluated.
   */
  public function reduce();
  
}
?>
<?php
/*
 * This file is part of Baladi, an API documentation generator.
 * Copyright (c) 2009 Bitextender GmbH. All rights reserved.
 *
 * For information on licensing conditions, see the LICENSE file that is
 * distributed with this source code.
 */
namespace baladi\language;

/**
 * Matches and evaluates given {@link baladi\source\ISource}s to construct a
 * set of useful output generators.
 *
 * Matchers are assumed to keep a referential internal state as they parse
 * files. When all segments of source code have been evaluated, matchers must
 * be capable of producing a set of generators to output useful information
 * about the code.
 */
interface IMatcher {

  /**
   * Matches a source code segment against a set of criteria to determine
   * whether it is a suitable representation of a given language.
   *
   * @param      baladi\source\ISource The source segment to check.
   *
   * @return     bool True if the source seems to represent the given language;
   *                  false otherwise.
   */
  public function match(\baladi\source\ISource $source);

  /**
   * Applies the given source code segment to the matcher's requirements.
   *
   * There are no specific criteria for how this method should behave; however,
   * the matcher must be capable of producing some form of output generators
   * after {@link reduce()} is called.
   *
   * @param      baladi\source\ISource The source segment to evaluate.
   */
  public function apply(\baladi\source\ISource $source);

  /**
   * Reduces the current state of the matcher to a set of generators.
   * Generators are capable of writing output in some form.
   *
   * @see        baladi\generator\IGenerator
   *
   * @return     baladi\generator\IGenerator[] A list of output generators.
   */
  public function reduce();

  /**
   * Provides a simple string representation of this matcher.
   *
   * @return     string A string representation of this matcher.
   */
  public function asString();

}
?>
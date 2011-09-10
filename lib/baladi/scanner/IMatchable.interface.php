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
 * Represents any object that can be matched against a Perl-compatible Regular
 * Expression (PCRE).
 */
interface IMatchable {

  /**
   * Matches the object against a given expression.
   *
   * @param      string The expression to match in PHP's PCRE format.
   */
  public function match($expression);

}
?>
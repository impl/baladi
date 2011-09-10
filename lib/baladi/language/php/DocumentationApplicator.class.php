<?php
/*
 * This file is part of Baladi, an API documentation generator.
 * Copyright (c) 2009 Bitextender GmbH. All rights reserved.
 *
 * For information on licensing conditions, see the LICENSE file that is
 * distributed with this source code.
 */
namespace baladi\language\php;

class DocumentationApplicator extends Applicator {

  public function reduce() {
    $documenter = new documenter\Documenter($this->getEnvironment());
    $documenter->document();
  }

}
?>
<?php
/*
 * This file is part of Baladi, an API documentation generator.
 * Copyright (c) 2009 Bitextender GmbH. All rights reserved.
 *
 * For information on licensing conditions, see the LICENSE file that is
 * distributed with this source code.
 */
namespace baladi\language\php\model;

interface IDeclarative extends IElement, INamed {

  public function addConstant(_Constant $constant);
  public function getConstants();
  
}
?>
<?php
/*
 * This file is part of Baladi, an API documentation generator.
 * Copyright (c) 2009 Bitextender GmbH. All rights reserved.
 *
 * For information on licensing conditions, see the LICENSE file that is
 * distributed with this source code.
 */
namespace baladi\log\severity;

const NOTICE = 0x8;
const INFORMATION = 0x4;
const WARNING = 0x2;
const ERROR = 0x1;

/* NOTICE | INFORMATION | WARNING | ERROR */
const ALL = 0xF;

function at_least($severity) {
    return ($severity << 1) - 1;
}

function includes($mask, $severity) {
    return $mask & $severity;
}
?>
<?php
/*
 * This file is part of Baladi, an API documentation generator.
 * Copyright (c) 2009 Bitextender GmbH. All rights reserved.
 *
 * For information on licensing conditions, see the LICENSE file that is
 * distributed with this source code.
 */

error_reporting(E_ALL | E_STRICT);
date_default_timezone_set('America/New_York');

require_once('../../lib/baladi.php');

/* Set up logging. */
$log = new baladi\log\StdoutLog();
//$log->setSeverity(baladi\log\severity\ALL);
$log->setSeverity(baladi\log\severity\at_least(baladi\log\severity\NOTICE));

/* Create a file scanner. */
$scanner = new baladi\scanner\RegexScanner(
  new baladi\scanner\MercurialFilterScanner(
    new baladi\scanner\RecursiveFilesystemScanner('../../../../')));
$scanner->addInclusion('#(\.php|\.xsf)$#');
$scanner->addInclusion('#\.inc$#');

/* Create a new package. */
$package = new baladi\package\Package();
$package->setLog($log);

$package->addScanner($scanner);

/* We need a matcher for PHP source code. */
$matcher = new baladi\language\php\Matcher();
$matcher->addApplicator(new baladi\language\php\LanguageApplicator());
$matcher->addApplicator(new baladi\language\php\DocumentationApplicator());

$package->addMatcher($matcher);

foreach($package->create() as $generator)
  $generator->save();

/* Generate an XML overview. */
$xmlFormatter = new baladi\language\php\formatter\DitaFormatter();
$xmlFormatter->setGrouping(Yew\Language\PHP\Grouping\CLASSES);

$xmlGenerator = new baladi\generator\FilesystemGenerator($xmlFormatter);
$xmlGenerator->setDirectory('output');
$xmlGenerator->setFormat('${name}_${hash}.xml');

/* And generate some useful images. */
$imageFormatter = new baladi\language\php\formatter\DiagramFormatter();
$imageFormatter->setType(IMAGETYPE_PNG);

$imageGenerator = new baladi\generator\FilesystemGenerator($imageFormatter);
$imageGenerator->setDirectory('output');
$imageGenerator->setFormat('${name}_${hash}_relationships.png');
?>
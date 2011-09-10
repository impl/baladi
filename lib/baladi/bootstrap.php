<?php
/*
 * This file is part of Baladi, an API documentation generator.
 * Copyright (c) 2009 Bitextender GmbH. All rights reserved.
 *
 * For information on licensing conditions, see the LICENSE file that is
 * distributed with this source code.
 */
namespace baladi;

define('BALADI_DIRECTORY', realpath(dirname(__FILE__)));
const DIRECTORY = BALADI_DIRECTORY;

/**
 * This is some lols.
 */
function autoload($name) {
  static $map = null;
  if($map === null) {
    $map = array('baladi\Executor' => 'Executor.class.php',

                 'baladi\log\ILog' => 'log/ILog.interface.php',

                 'baladi\log\StdoutLog' => 'log/StdoutLog.class.php',
                 'baladi\log\NullLog' => 'log/NullLog.class.php',

                 'baladi\scanner\IScanner' => 'scanner/IScanner.interface.php',
                 'baladi\scanner\FilesystemScanner' => 'scanner/FilesystemScanner.class.php',
                 'baladi\scanner\RecursiveFilesystemScanner' => 'scanner/RecursiveFilesystemScanner.class.php',
                 'baladi\scanner\MercurialFilterScanner' => 'scanner/MercurialFilterScanner.class.php',
                 'baladi\scanner\RegexScanner' => 'scanner/RegexScanner.class.php',

                 'baladi\scanner\IMatchable' => 'scanner/IMatchable.interface.php',
                 'baladi\scanner\InvalidMatchExpressionException' => 'scanner/InvalidMatchExpressionException.class.php',

                 'baladi\source\ISource' => 'source/ISource.interface.php',
                 'baladi\source\IFileSource' => 'source/IFileSource.interface.php',

                 'baladi\source\FilesystemSource' => 'source/FilesystemSource.class.php',

                 'baladi\source\Location' => 'source/Location.class.php',

                 'baladi\language\IMatcher' => 'language/IMatcher.interface.php',
                 'baladi\language\MatcherException' => 'language/MatcherException.class.php',

                 'baladi\package\Package' => 'package/Package.class.php',

                 /* Support for specific languages. */

                 /* PHP. */
                 'baladi\language\php\Matcher' => 'language/php/Matcher.class.php',

                 'baladi\language\php\Environment' => 'language/php/Environment.class.php',
                 'baladi\language\php\EnvironmentException' => 'language/php/EnvironmentException.class.php',

                 'baladi\language\php\IApplicator' => 'language/php/IApplicator.interface.php',
                 'baladi\language\php\Applicator' => 'language/php/Applicator.class.php',
                 'baladi\language\php\ApplicatorException' => 'language/php/ApplicatorException.class.php',

                 'baladi\language\php\LanguageApplicator' => 'language/php/LanguageApplicator.class.php',
                 'baladi\language\php\DocumentationApplicator' => 'language/php/DocumentationApplicator.class.php',

                 'baladi\language\php\parser\Parser' => 'language/php/parser/Parser.class.php',
                 'baladi\language\php\parser\ParserContext' => 'language/php/parser/ParserContext.class.php',
                 'baladi\language\php\parser\ParserContextStack' => 'language/php/parser/ParserContextStack.class.php',
                 'baladi\language\php\parser\ParserException' => 'language/php/parser/ParserException.class.php',

                 'baladi\language\php\parser\Token' => 'language/php/parser/Token.class.php',
                 'baladi\language\php\parser\EngineToken' => 'language/php/parser/EngineToken.class.php',
                 'baladi\language\php\parser\CharacterToken' => 'language/php/parser/CharacterToken.class.php',
                 'baladi\language\php\parser\Tokenizer' => 'language/php/parser/Tokenizer.class.php',
                 'baladi\language\php\parser\TokenIterator' => 'language/php/parser/TokenIterator.class.php',

                 'baladi\language\php\documenter\Documenter' => 'language/php/documenter/Documenter.class.php',

                 'baladi\language\php\model\IElement' => 'language/php/model/IElement.interface.php',
                 'baladi\language\php\model\INamed' => 'language/php/model/INamed.interface.php',
                 'baladi\language\php\model\IType' => 'language/php/model/IType.interface.php',
                 'baladi\language\php\model\IDeclarative' => 'language/php/model/IDeclarative.interface.php',
                 'baladi\language\php\model\IClassifier' => 'language/php/model/IClassifier.interface.php',
                 'baladi\language\php\model\IOperation' => 'language/php/model/IOperation.interface.php',
                 'baladi\language\php\model\IAssignable' => 'language/php/model/IAssignable.interface.php',
                 'baladi\language\php\model\ILocatable' => 'language/php/model/ILocatable.interface.php',

                 'baladi\language\php\model\Parameter' => 'language/php/model/Parameter.class.php',
                 'baladi\language\php\model\BuiltinType' => 'language/php/model/BuiltinType.class.php',
                 'baladi\language\php\model\UnresolvedClassifier' => 'language/php/model/UnresolvedClassifier.class.php',

                 'baladi\language\php\model\ResolutionException' => 'language/php/model/ResolutionException.class.php',

                 'baladi\language\php\model\_Namespace' => 'language/php/model/_Namespace.class.php',
                 'baladi\language\php\model\_Class' => 'language/php/model/_Class.class.php',
                 'baladi\language\php\model\_Interface' => 'language/php/model/_Interface.class.php',
                 'baladi\language\php\model\_Function' => 'language/php/model/_Function.class.php',
                 'baladi\language\php\model\_Method' => 'language/php/model/_Method.class.php',
                 'baladi\language\php\model\_Variable' => 'language/php/model/_Variable.class.php',
                 'baladi\language\php\model\_Property' => 'language/php/model/_Property.class.php',
                 'baladi\language\php\model\_Constant' => 'language/php/model/_Constant.class.php');
  }

  if(isset($map[$name]))
    require($map[$name]);
}

function bootstrap() {
  spl_autoload_register('baladi\autoload');
}
?>
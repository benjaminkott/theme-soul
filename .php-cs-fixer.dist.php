<?php

declare(strict_types=1);

/*
 * The format of the PHP in this package: `composer cgl` rewrites, and
 * `composer cgl:ci` reports. The rules are `typo3/coding-standards`.
 */

$config = \TYPO3\CodingStandards\CsFixerConfig::create();
$config->getFinder()
    ->in(__DIR__)
    ->exclude(['.build', 'config', 'var'])
;

return $config;

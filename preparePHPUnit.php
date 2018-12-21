<?php

$versionsData = [
    '#^5\.6#' => 'https://phar.phpunit.de/phpunit-5.phar',
    '#^7\.0#' => 'https://phar.phpunit.de/phpunit-6.phar',
    '#^7\.#'  => 'https://phar.phpunit.de/phpunit.phar',
];

$phpVersion = phpversion();
$phpUnitUrl = null;

foreach ($versionsData as $versionRegexp => $phpunitPossibleUrl) {
    if (preg_match($versionRegexp, $phpVersion) === 1) {
        $phpUnitUrl = $phpunitPossibleUrl;
        break;
    }
}

if (is_null($phpUnitUrl)) {
    throw new \Exception('PHPUnit version is not recognized');
}

//download PHPUNIT
file_put_contents('phpunit.phar', file_get_contents($phpUnitUrl));








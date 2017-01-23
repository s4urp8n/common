<?php

$srcDirectory = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'src') . DIRECTORY_SEPARATOR;
$classesDirectory = realpath(__DIR__ . DIRECTORY_SEPARATOR . 'classes') . DIRECTORY_SEPARATOR;
$composerDirectory = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor') . DIRECTORY_SEPARATOR;

//PSR4 autoloader
spl_autoload_register(function ($className) use ($srcDirectory, $classesDirectory) {

    $fileName = mb_eregi_replace('[\\\/]+', DIRECTORY_SEPARATOR, $className);
    $fileName = mb_eregi_replace('^' . preg_quote(DIRECTORY_SEPARATOR) . '+', '', $fileName);
    $fileName = mb_eregi_replace(preg_quote(DIRECTORY_SEPARATOR) . '+$', '', $fileName);
    $fileName .= '.php';

    if (file_exists($srcDirectory . $fileName)) {
        include_once($srcDirectory . $fileName);
    }

    if (file_exists($classesDirectory . $fileName)) {
        include_once($classesDirectory . $fileName);
    }

}, false, false);

//Functions file
$functionsFile = $srcDirectory . 'Functions.php';
if (file_exists($functionsFile)) {
    include_once($functionsFile);
}

//Composer autoload
$composerFile = $composerDirectory . 'autoload.php';
if (file_exists($composerFile)) {
    include_once($composerFile);
}

if (!function_exists('packageFile')) {
    /**
     * @param $name Name of file in files folder
     *
     * @return string Full filename of package file
     */
    function packageFile($name)
    {
        return realpath(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . $name);
    }
}

if (!function_exists('packageTestFile')) {
    /**
     * @param $name Name of file in files folder
     *
     * @return string Full filename of package test file
     */
    function packageTestFile($name)
    {
        return realpath(__DIR__ . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . $name);
    }
}
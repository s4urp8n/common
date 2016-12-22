<?php

$srcDirectory = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'src') . DIRECTORY_SEPARATOR;
$classesDirectory = realpath(__DIR__ . DIRECTORY_SEPARATOR . 'classes') . DIRECTORY_SEPARATOR;
$composerDirectory =
    realpath(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor') . DIRECTORY_SEPARATOR;

//PSR4 autoloader
spl_autoload_register(
    function ($className) use ($srcDirectory, $classesDirectory) {

        $className = mb_eregi_replace('[\\\/]+', DIRECTORY_SEPARATOR, $className);
        $className = mb_eregi_replace('^' . preg_quote(DIRECTORY_SEPARATOR) . '+', '', $className);
        $className = mb_eregi_replace(preg_quote(DIRECTORY_SEPARATOR) . '+$', '', $className);
        $className .= '.php';

        if (file_exists($srcDirectory . $className)) {
            include_once($srcDirectory . $className);
        }

        if (file_exists($classesDirectory . $className)) {
            include_once($classesDirectory . $className);
        }

    }, false, false
);

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
        return realpath(
            __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . $name
        );
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
        return realpath(
            __DIR__ . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . $name
        );
    }
}
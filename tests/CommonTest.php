<?php

class CommonTest extends PHPUnit\Framework\TestCase
{

    use Package\Test;

    public function testDefaultEncoding()
    {
        $this->foreachSame(
            [
                [\Zver\Common::getDefaultEncoding(), 'UTF-8'],
            ]
        );
    }

    public function testReplaceSlashes()
    {
        $this->foreachSame([
                               [
                                   \Zver\Common::replaceSlashesToPlatformSlashes('/'),
                                   DIRECTORY_SEPARATOR,
                               ],
                               [
                                   \Zver\Common::replaceSlashesToPlatformSlashes('\\'),
                                   DIRECTORY_SEPARATOR,
                               ],
                               [
                                   \Zver\Common::replaceSlashesToPlatformSlashes('//\\\\\\\\'),
                                   DIRECTORY_SEPARATOR,
                               ],
                               [
                                   \Zver\Common::replaceSlashesToPlatformSlashes('//path\\\\\\\\'),
                                   DIRECTORY_SEPARATOR . "path" . DIRECTORY_SEPARATOR,
                               ],
                               [
                                   \Zver\Common::replaceSlashesToPlatformSlashes('///'),
                                   DIRECTORY_SEPARATOR,
                               ],
                               [
                                   \Zver\Common::replaceSlashesToPlatformSlashes('\\/////'),
                                   DIRECTORY_SEPARATOR,
                               ],
                               [
                                   \Zver\Common::replaceSlashesToPlatformSlashes('//\\\\\//\\//\\\\'),
                                   DIRECTORY_SEPARATOR,
                               ],
                               [
                                   \Zver\Common::replaceSlashesToPlatformSlashes('//path\\\\\\\\path\\//path\\'),
                                   DIRECTORY_SEPARATOR . "path" . DIRECTORY_SEPARATOR . "path" . DIRECTORY_SEPARATOR . 'path' . DIRECTORY_SEPARATOR,
                               ],
                           ]);
    }

    public function testConvertEncoding()
    {
        $this->foreachSame([
                               [
                                   \Zver\Common::convertToDefaultEncoding('string', 'UTF-8'),
                                   'string',
                               ],
                               [
                                   \Zver\Common::convertToDefaultEncoding('string', 'Windows-1251'),
                                   'string',
                               ],
                               [
                                   \Zver\Common::convertToDefaultEncoding('с т р о к а', 'UTF-8'),
                                   'с т р о к а',
                               ],
                               [
                                   \Zver\Common::convertToDefaultEncoding(file_get_contents(packageTestFile('StringWin1251.txt')), 'Windows-1251'),
                                   'строка',
                               ],
                               [
                                   \Zver\Common::convertToDefaultEncoding(file_get_contents(packageTestFile('StringUTF-8.txt')), 'UTF-8'),
                                   'строка',
                               ],
                           ]);
    }

    public function testAutoloader()
    {
        $this->foreachFalse(
            [
                class_exists('TestClass2'),
                class_exists('TestDir\TestClass'),
            ]
        );

        \Zver\Common::registerAutoloadClassesFrom(\Zver\Common::replaceSlashesToPlatformSlashes(__DIR__ . '/../autoloader/'));

        $this->foreachTrue(
            [
                class_exists('TestClass2'),
                class_exists('TestDir\TestClass'),
            ]
        );
    }

    public function testAutoloader2()
    {
        $this->foreachFalse(
            [
                class_exists('TestClass2'),
                class_exists('TestDir\TestClass'),
            ]
        );

        \Zver\Common::registerAutoloadClassesFrom(\Zver\Common::replaceSlashesToPlatformSlashes(__DIR__ . '/../autoloader'));

        $this->foreachTrue(
            [
                class_exists('TestClass2'),
                class_exists('TestDir\TestClass'),
            ]
        );
    }

    public function testAutoloader3()
    {
        $this->foreachFalse(
            [
                class_exists('\TestClass2'),
                class_exists('\TestDir\TestClass'),
            ]
        );

        \Zver\Common::registerAutoloadClassesFrom(\Zver\Common::replaceSlashesToPlatformSlashes(__DIR__ . '/../autoloader'));

        $this->foreachTrue(
            [
                class_exists('\TestClass2'),
                class_exists('\TestDir\TestClass'),
            ]
        );
    }

}
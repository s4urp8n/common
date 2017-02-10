<?php

class CommonTest extends PHPUnit\Framework\TestCase
{

    use \Zver\Package\Test;
    use \Zver\Package\Common;

    public function testDefaultEncoding()
    {
        $this->foreachSame(
            [
                [\Zver\Common::getDefaultEncoding(), 'UTF-8'],
            ]
        );

        $this->foreachNotSame(
            [
                [\Zver\Common::getDefaultEncoding(), 'Windows-1251'],
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
                                   \Zver\Common::convertToDefaultEncoding(file_get_contents(static::getPackageTestFilePath('StringWin1251.txt')), 'Windows-1251'),
                                   'строка',
                               ],
                               [
                                   \Zver\Common::convertToDefaultEncoding(file_get_contents(static::getPackageTestFilePath('StringUTF-8.txt')), 'UTF-8'),
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

    public function testPackageFile()
    {
        \Zver\Common::registerAutoloadClassesFrom(\Zver\Common::replaceSlashesToPlatformSlashes(__DIR__ . '/../autoloader'));

        $gitKeep = 'Save files for your packages in this folder';
        $gitTestKeep = 'Save files for your tests in this folder';

        $this->assertSame(file_get_contents(static::getPackageFilePath('.gitkeep')), $gitKeep);
        $this->assertSame(file_get_contents(\TestDir\TestClass::gitKeep('.gitkeep')), $gitKeep);
        $this->assertSame(file_get_contents(\TestClass2::gitKeep('.gitkeep')), $gitKeep);
        $this->assertSame(file_get_contents(\Zver\Common::getPackageFilePath('.gitkeep')), $gitKeep);

        $this->assertSame(file_get_contents(static::getPackageTestFilePath('.gitkeep')), $gitTestKeep);
        $this->assertSame(file_get_contents(\TestDir\TestClass::gitTestKeep('.gitkeep')), $gitTestKeep);
        $this->assertSame(file_get_contents(\TestClass2::gitTestKeep('.gitkeep')), $gitTestKeep);
        $this->assertSame(file_get_contents(\Zver\Common::getPackageTestFilePath('.gitkeep')), $gitTestKeep);
    }

}
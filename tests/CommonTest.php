<?php

use Zver\Common;

class CommonTest extends PHPUnit\Framework\TestCase
{

    use \Zver\Package\Test;

    public function testDefaultEncoding()
    {
        $this->foreachSame(
            [
                [Common::getDefaultEncoding(), 'UTF-8'],
            ]
        );

        $this->foreachNotSame(
            [
                [Common::getDefaultEncoding(), 'Windows-1251'],
            ]
        );
    }

    public function testReplaceSlashes()
    {
        $this->foreachSame([
                               [
                                   Common::replaceSlashesToPlatformSlashes('/'),
                                   DIRECTORY_SEPARATOR,
                               ],
                               [
                                   Common::replaceSlashesToPlatformSlashes('\\'),
                                   DIRECTORY_SEPARATOR,
                               ],
                               [
                                   Common::replaceSlashesToPlatformSlashes('//\\\\\\\\'),
                                   DIRECTORY_SEPARATOR,
                               ],
                               [
                                   Common::replaceSlashesToPlatformSlashes('//path\\\\\\\\'),
                                   DIRECTORY_SEPARATOR . "path" . DIRECTORY_SEPARATOR,
                               ],
                               [
                                   Common::replaceSlashesToPlatformSlashes('///'),
                                   DIRECTORY_SEPARATOR,
                               ],
                               [
                                   Common::replaceSlashesToPlatformSlashes('\\/////'),
                                   DIRECTORY_SEPARATOR,
                               ],
                               [
                                   Common::replaceSlashesToPlatformSlashes('//\\\\\//\\//\\\\'),
                                   DIRECTORY_SEPARATOR,
                               ],
                               [
                                   Common::replaceSlashesToPlatformSlashes('//path\\\\\\\\path\\//path\\'),
                                   DIRECTORY_SEPARATOR . "path" . DIRECTORY_SEPARATOR . "path" . DIRECTORY_SEPARATOR . 'path' . DIRECTORY_SEPARATOR,
                               ],
                           ]);
    }

    public function testConvertEncoding()
    {
        $this->foreachSame([
                               [
                                   Common::convertToDefaultEncoding('string', 'UTF-8'),
                                   'string',
                               ],
                               [
                                   Common::convertToDefaultEncoding('string', 'Windows-1251'),
                                   'string',
                               ],
                               [
                                   Common::convertToDefaultEncoding('с т р о к а', 'UTF-8'),
                                   'с т р о к а',
                               ],
                               [
                                   Common::convertToDefaultEncoding(file_get_contents(Common::getPackageTestFilePath('StringWin1251.txt')), 'Windows-1251'),
                                   'строка',
                               ],
                               [
                                   Common::convertToDefaultEncoding(file_get_contents(Common::getPackageTestFilePath('StringUTF-8.txt')), 'UTF-8'),
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

        Common::registerAutoloadClassesFrom(Common::replaceSlashesToPlatformSlashes(__DIR__ . '/../autoloader/'));

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

        Common::registerAutoloadClassesFrom(Common::replaceSlashesToPlatformSlashes(__DIR__ . '/../autoloader'));

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

        Common::registerAutoloadClassesFrom(Common::replaceSlashesToPlatformSlashes(__DIR__ . '/../autoloader'));

        $this->foreachTrue(
            [
                class_exists('\TestClass2'),
                class_exists('\TestDir\TestClass'),
            ]
        );
    }

    public function testPackageFile()
    {
        Common::registerAutoloadClassesFrom(Common::replaceSlashesToPlatformSlashes(__DIR__ . '/../autoloader'));

        $gitKeep = 'Save files for your packages in this folder';
        $gitTestKeep = 'Save files for your tests in this folder';

        $this->assertSame(file_get_contents(\TestDir\TestClass::gitKeep('.gitkeep')), $gitKeep);
        $this->assertSame(file_get_contents(\TestClass2::gitKeep('.gitkeep')), $gitKeep);
        $this->assertSame(file_get_contents(Common::getPackageFilePath('.gitkeep')), $gitKeep);

        $this->assertSame(file_get_contents(\TestDir\TestClass::gitTestKeep('.gitkeep')), $gitTestKeep);
        $this->assertSame(file_get_contents(\TestClass2::gitTestKeep('.gitkeep')), $gitTestKeep);
        $this->assertSame(file_get_contents(Common::getPackageTestFilePath('.gitkeep')), $gitTestKeep);
    }

    public function testIsOS()
    {
        $this->foreachTrue([
                               Common::isWindowsOS('win'),
                               Common::isWindowsOS('windows'),
                               Common::isWindowsOS('windows xp'),
                               Common::isWindowsOS('windows vista'),
                               Common::isWindowsOS('winnt'),
                               Common::isWindowsOS('win 200'),
                           ]);

        $this->foreachFalse([
                                Common::isWindowsOS('linux'),
                                Common::isWindowsOS('mac os'),
                                Common::isWindowsOS('free bsd'),
                                Common::isWindowsOS('internet explorer'),
                            ]);

        $this->foreachTrue([
                               Common::isLinuxOS('linux'),
                               Common::isLinuxOS('ubuntu'),
                               Common::isLinuxOS('mint'),
                               Common::isLinuxOS('fedora'),
                               Common::isLinuxOS('mandriva'),
                               Common::isLinuxOS('slackware'),
                               Common::isLinuxOS('debian'),
                           ]);

        $this->foreachFalse([
                                Common::isLinuxOS('win'),
                                Common::isLinuxOS('windows'),
                                Common::isLinuxOS('windows xp'),
                                Common::isLinuxOS('windows vista'),
                                Common::isLinuxOS('winnt'),
                                Common::isLinuxOS('win 200'),
                                Common::isLinuxOS('mac os'),
                                Common::isLinuxOS('free bsd'),
                                Common::isLinuxOS('internet explorer'),
                            ]);
    }

    public function testProcessRunning()
    {
        $pid = getmypid();

        $otherPid = getmypid() . rand(111, 999) . rand(111, 999);

        if (Common::isLinuxOS()) {
            $this->assertRegexp('/linux/i', Common::getOSName());
            $this->assertFalse(Common::isWindowsOS());
            $this->assertTrue(Common::isProcessRunning($pid));
            $this->assertTrue(Common::isProcessRunning($pid, 'php'));
            $this->assertFalse(Common::isProcessRunning($pid, 'phpVeryCool'));
            $this->assertFalse(Common::isProcessRunning($otherPid));
            $this->assertFalse(Common::isProcessRunning($otherPid, 'php'));
        } else {
            $this->assertRegexp('/win/i', Common::getOSName());
            $this->assertFalse(Common::isLinuxOS());
            $this->assertTrue(Common::isProcessRunning($pid));
            $this->assertTrue(Common::isProcessRunning($pid, 'php'));
            $this->assertFalse(Common::isProcessRunning($pid, 'phpVeryCool'));
            $this->assertFalse(Common::isProcessRunning($otherPid));
            $this->assertFalse(Common::isProcessRunning($otherPid, 'php'));
        }

    }

    public function testGetDirectoryContent()
    {
        $this->foreachSame([
                               [
                                   Common::getDirectoryContent(__DIR__),
                                   [
                                       __DIR__ . DIRECTORY_SEPARATOR . 'bootstrap.php',
                                       __DIR__ . DIRECTORY_SEPARATOR . 'classes',
                                       __DIR__ . DIRECTORY_SEPARATOR . 'CommonTest.php',
                                       __DIR__ . DIRECTORY_SEPARATOR . 'files',
                                   ],
                               ],
                               [
                                   Common::getDirectoryContent(__DIR__ . DIRECTORY_SEPARATOR . 'files/'),
                                   [
                                       __DIR__ . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . '.gitkeep',
                                       __DIR__ . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'StringUTF-8.txt',
                                       __DIR__ . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'StringWin1251.txt',
                                       __DIR__ . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'sync.php',
                                       __DIR__ . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'sync.txt',
                                   ],
                               ],
                           ]);
    }

    public function testGetDirectoryContentRecursive()
    {
        $this->foreachSame([
                               [
                                   Common::getDirectoryContentRecursive(__DIR__),
                                   [
                                       __DIR__ . DIRECTORY_SEPARATOR . 'bootstrap.php',
                                       __DIR__ . DIRECTORY_SEPARATOR . 'classes',
                                       __DIR__ . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . '.gitkeep',
                                       __DIR__ . DIRECTORY_SEPARATOR . 'CommonTest.php',
                                       __DIR__ . DIRECTORY_SEPARATOR . 'files',
                                       __DIR__ . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . '.gitkeep',
                                       __DIR__ . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'StringUTF-8.txt',
                                       __DIR__ . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'StringWin1251.txt',
                                       __DIR__ . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'sync.php',
                                       __DIR__ . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'sync.txt',
                                   ],
                               ],
                               [
                                   Common::getDirectoryContentRecursive(__DIR__ . DIRECTORY_SEPARATOR . 'files'),
                                   Common::getDirectoryContent(__DIR__ . DIRECTORY_SEPARATOR . 'files'),
                               ],
                           ]);
    }

    protected function getSyncFile()
    {
        return Common::getPackageTestFilePath('sync.txt');
    }

    protected function getSyncCommand()
    {
        return "php " . escapeshellarg(Common::getPackageTestFilePath('sync.php'));
    }

    public function testExec()
    {
        file_put_contents($this->getSyncFile(), "0");

        $this->assertSame("0", file_get_contents($this->getSyncFile()));

        Common::execShell($this->getSyncCommand());

        $this->assertSame("1", file_get_contents($this->getSyncFile()));
        file_put_contents($this->getSyncFile(), "0");
    }

}
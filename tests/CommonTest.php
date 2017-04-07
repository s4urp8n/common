<?php

use Zver\Common;

class CommonTest extends PHPUnit\Framework\TestCase
{

    use \Zver\Package\Helper;

    public static function setUpBeforeClass()
    {
        file_put_contents(static::getPackagePath('sync.txt'), 1, LOCK_EX);
    }

    public static function tearDownAfterClass()
    {
        static::setUpBeforeClass();
    }

    public function testHumanReadableBytes()
    {
        $this->foreachSame([
                               [Common::getHumanReadableBytes(0), '0 B'],
                               [Common::getHumanReadableBytes(1024 + 512), '1.5 KB'],
                               [Common::getHumanReadableBytes(1024 + 412), '1.4 KB'],
                               [Common::getHumanReadableBytes(pow(1024, 1)), '1 KB'],
                               [Common::getHumanReadableBytes(pow(1024, 2)), '1 MB'],
                               [Common::getHumanReadableBytes(pow(1024, 3)), '1 GB'],
                               [Common::getHumanReadableBytes(pow(1024, 4)), '1 TB'],
                               [Common::getHumanReadableBytes(pow(1024, 5)), '1 PB'],
                               [Common::getHumanReadableBytes(pow(1024, 6)), '1 EB'],
                               [Common::getHumanReadableBytes(pow(1024, 7)), '1 ZB'],
                               [Common::getHumanReadableBytes(pow(1024, 8)), '1 YB'],
                               [Common::getHumanReadableBytes(pow(1024, 8) * 2.5), '2.5 YB'],
                               [Common::getHumanReadableBytes(pow(1024, 8) * 2.5, ''), '2.5YB'],
                               [Common::getHumanReadableBytes(pow(1024, 8) * 2.5, '-'), '2.5-YB'],
                               [Common::getHumanReadableBytes(pow(1024, 8) * 2.5, '++'), '2.5++YB'],
                           ]);
    }

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
                                   Common::convertToDefaultEncoding(file_get_contents(static::getPackagePath('tests/files/StringWin1251.txt')), 'Windows-1251'),
                                   'строка',
                               ],
                               [
                                   Common::convertToDefaultEncoding(file_get_contents(static::getPackagePath('tests/files/StringUTF-8.txt')), 'UTF-8'),
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

    public function testStripSlashes()
    {
        $this->foreachSame([
                               [
                                   Common::stripBeginningSlashes('////path'),
                                   'path',
                               ],
                               [
                                   Common::stripBeginningSlashes('////path///'),
                                   'path///',
                               ],
                               [
                                   Common::stripBeginningSlashes('////path///\\'),
                                   'path///\\',
                               ],
                               [
                                   Common::stripBeginningSlashes('////\\\\\path'),
                                   'path',
                               ],
                               [
                                   Common::stripBeginningSlashes('////\\\\\path'),
                                   'path',
                               ],
                               [
                                   Common::stripBeginningSlashes('\\\\\path'),
                                   'path',
                               ],
                               [
                                   Common::stripEndingSlashes('path///'),
                                   'path',
                               ],
                               [
                                   Common::stripEndingSlashes('path\\\\'),
                                   'path',
                               ],
                               [
                                   Common::stripEndingSlashes('\\\path\\\\'),
                                   '\\\path',
                               ],
                               [
                                   Common::stripEndingSlashes('\\\path\\\\///'),
                                   '\\\path',
                               ],
                           ]);
    }

    public function testProcessRunning2()
    {
        $command = 'php ' . static::getPackagePath('inf.php');

        $descriptorspec = [
            0 => ["pipe", "r"],
            1 => ["pipe", "w"],
            2 => ["pipe", "w"],
        ];

        $pipes = [];

        $process = proc_open($command, $descriptorspec, $pipes);

        $status = proc_get_status($process);

        $pid = $status['pid'];

        $this->assertTrue(Common::isProcessRunning($pid));

        proc_close($process);

        $this->assertFalse(Common::isProcessRunning($pid));

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
        return realpath(__DIR__ . '/../sync.txt');
    }

    protected function getSyncCommand()
    {
        return "php " . escapeshellarg(realpath(__DIR__ . '/../sync.php'));
    }

    protected function getInfCommand()
    {
        return 'php "' . dirname($this->getSyncFile()) . DIRECTORY_SEPARATOR . 'inf.php"';
    }

    protected function get10SecCommand()
    {
        return 'php "' . dirname($this->getSyncFile()) . DIRECTORY_SEPARATOR . '10sec.php"';
    }

    protected function set0ToSync()
    {
        file_put_contents($this->getSyncFile(), "0");
    }

    protected function assertSync0()
    {
        $this->assertSame("0", file_get_contents($this->getSyncFile()));
    }

    protected function assertSync1()
    {
        $this->assertSame("1", file_get_contents($this->getSyncFile()));
    }

    public function testExecSync()
    {
        $this->set0ToSync();
        $this->assertSync0();
        Common::executeInSystem($this->getSyncCommand());
        $this->assertSync1();
    }

    public function testExecAsync()
    {
        $this->set0ToSync();
        $this->assertSync0();

        Common::executeInSystemAsync($this->getSyncCommand());
        $this->assertSync0();

        sleep(9);
        $this->assertSync1();
    }

    public function testExecuteAsyncInFile()
    {
        $file = __DIR__ . DIRECTORY_SEPARATOR . "async.txt";

        if (file_exists($file)) {
            unlink($file);
        }

        $this->assertFalse(file_exists($file));

        Common::executeInSystemAsync($this->get10SecCommand(), $file);

        sleep(15);

        $this->assertTrue(file_exists($file));

        $this->assertTrue(preg_match('/^\+{11}$/', file_get_contents($file)) === 1);

        if (file_exists($file)) {
            unlink($file);
        }

    }

    public function testKillProcess()
    {
        /**
         * Infinite process
         */
        Common::executeInSystemAsync($this->getInfCommand());

        sleep(1);

        /**
         * Get pid of infinite process
         */
        $pid = trim(file_get_contents($this->getSyncFile()));

        sleep(10);

        $this->assertTrue(Common::isProcessRunning($pid));

        sleep(10);

        $this->assertTrue(Common::isProcessRunning($pid));

        Common::killProcess($pid);

        $this->assertFalse(Common::isProcessRunning($pid));

    }

    protected static $testDireftoriesDepth = 5;
    protected static $testExt = 'txt';

    public function createTestDirectories()
    {
        $deepest = __DIR__ . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, range(1, static::$testDireftoriesDepth, 1)) . DIRECTORY_SEPARATOR;

        mkdir($deepest, 0777, true);
        $this->assertTrue(file_exists($deepest));

        for ($i = 1; $i <= static::$testDireftoriesDepth; $i++) {
            $file = __DIR__ . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, range(1, $i, 1)) . DIRECTORY_SEPARATOR . $i . '.' . static::$testExt;
            file_put_contents($file, md5(rand(1, 9999)));
            $this->assertTrue(file_exists($file));
        }

    }

    public function getTestDirectoryPath()
    {
        return __DIR__ . DIRECTORY_SEPARATOR . '1';
    }

    public function testRemoveDirectory()
    {
        $this->createTestDirectories();

        Common::removeDirectory($this->getTestDirectoryPath());

        $deepest = __DIR__ . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, range(1, static::$testDireftoriesDepth, 1)) . DIRECTORY_SEPARATOR;

        $this->assertFalse(file_exists($deepest));
        $this->assertFalse(is_dir($deepest));

    }

    public function testMakeEmptyDirectory()
    {
        $this->createTestDirectories();

        Common::removeDirectoryContents($this->getTestDirectoryPath());

        $this->assertTrue(is_dir($this->getTestDirectoryPath()));

        $this->assertSame([], Common::getDirectoryContentRecursive($this->getTestDirectoryPath()));

        $this->testRemoveDirectory();

    }

    public function testCreateDirectoryIfNotExists()
    {
        $this->assertFalse(file_exists($this->getTestDirectoryPath()));

        Common::createDirectoryIfNotExists($this->getTestDirectoryPath());

        $this->assertTrue(file_exists($this->getTestDirectoryPath()));

        Common::createDirectoryIfNotExists($this->getTestDirectoryPath());

        $this->assertTrue(file_exists($this->getTestDirectoryPath()));

        rmdir($this->getTestDirectoryPath());

        $this->assertFalse(file_exists($this->getTestDirectoryPath()));
    }

}
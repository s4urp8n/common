<?php

use Zver\Common;

class CommonTest extends PHPUnit\Framework\TestCase
{

    use \Zver\Package\Helper;

    protected static $testDireftoriesDepth = 5;
    protected static $testExt = 'txt';

    public static function setUpBeforeClass(): void
    {
        sleep(1);
        file_put_contents(static::getPackagePath('sync.txt'), 1, LOCK_EX);
        sleep(1);
    }

    public static function tearDownAfterClass(): void
    {
        static::setUpBeforeClass();
    }

    public function testArrayChunkOffset()
    {

    }

    public function testSortFilesAndFolders()
    {

        $test = [
            '3\WIN7\64\rt64win7.cat',
            '3\WIN7\64\install.exe',
            '3\SETuP.exe',
            '3\WIN7\32\rt86win7.inf',
            '3\WIN7\32\rt86win7.cat',
            'install.exe',
            '3\WIN7\64\rt64win7.inf',
        ];

        $deepestFirst = [
            '3\WIN7\32\rt86win7.cat',
            '3\WIN7\32\rt86win7.inf',
            '3\WIN7\64\install.exe',
            '3\WIN7\64\rt64win7.cat',
            '3\WIN7\64\rt64win7.inf',
            '3\SETuP.exe',
            'install.exe',
        ];

        $deepestLast = [
            'install.exe',
            '3\SETuP.exe',
            '3\WIN7\32\rt86win7.cat',
            '3\WIN7\32\rt86win7.inf',
            '3\WIN7\64\install.exe',
            '3\WIN7\64\rt64win7.cat',
            '3\WIN7\64\rt64win7.inf',
        ];

        $this->assertSame(Common::sortFilesAndFolders($deepestFirst), Common::sortFilesAndFolders($test));
        $this->assertSame(Common::sortFilesAndFolders($deepestLast), Common::sortFilesAndFolders($test));

    }

    public function testSortByDepth()
    {

        $test = [
            '3\WIN7\64\rt64win7.cat',
            '3\WIN7\64\install.exe',
            '3\SETuP.exe',
            '3\WIN7\32\rt86win7.inf',
            '3\WIN7\32\rt86win7.cat',
            'install.exe',
            '3\WIN7\64\rt64win7.inf',
        ];

        $deepestFirst = [
            '3\WIN7\32\rt86win7.cat',
            '3\WIN7\32\rt86win7.inf',
            '3\WIN7\64\install.exe',
            '3\WIN7\64\rt64win7.cat',
            '3\WIN7\64\rt64win7.inf',
            '3\SETuP.exe',
            'install.exe',
        ];

        $deepestLast = [
            'install.exe',
            '3\SETuP.exe',
            '3\WIN7\32\rt86win7.cat',
            '3\WIN7\32\rt86win7.inf',
            '3\WIN7\64\install.exe',
            '3\WIN7\64\rt64win7.cat',
            '3\WIN7\64\rt64win7.inf',
        ];

        $this->assertSame($deepestFirst, Common::sortPathsByDepth($test, true));
        $this->assertSame($deepestLast, Common::sortPathsByDepth($test, false));

    }

    public function testIsExt()
    {
        $rootDir = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR;
        $tests = [
            ['.travis.yml', 'Yml', true],
            ['.travis.yml', 'YmL', true],
            ['.travis.yml', 'yMl', true],
            ['.travis.yml', '.yMl', false],
            ['10sec.php', '.yMl', false],
            ['10sec.php', '.php', false],
            ['10sec.php', 'php', true],
        ];

        foreach ($tests as $test) {
            $this->assertSame(Common::isFileExtension($rootDir . $test[0], $test[1]), $test[2]);
        }
    }

    public function testIsIP()
    {

        $tests = [
            '192.168.0.1'    => true,
            '19.18.10.10'    => true,
            '0.0.00.0'       => true,
            '0.0.00.000'     => true,
            '266.168.0.1'    => false,
            '206,1.168.0.1'  => false,
            '266.168.01'     => false,
            '266.168,1.5.01' => false,
            '266.16801'      => false,
            'ccc.sss.ss.sds' => false,
            'c4.s3s.s1.2s'   => false,
            'c4.'            => false,
            'c4.фывфыв'      => false,
            'a.sfk.nr.93r'   => false,
            '4.3.1.2s'       => false,
        ];

        foreach ($tests as $input => $output) {
            $this->assertSame(Common::isIP($input), $output);
        }

    }

    public function testSecondsToTime()
    {
        $tests = [
            0                                       => '00:00:00:00',
            1                                       => '00:00:00:01',
            100                                     => '00:00:01:40',
            101                                     => '00:00:01:41',
            3600                                    => '00:01:00:00',
            3600 * 24 + 3600 + 60 + 3               => '01:01:01:03',
            99 * 86400 + 3600 + 63                  => '99:01:01:03',
            9999 * 86400 + 23 * 3600 + 59 * 60 + 59 => '9999:23:59:59',
        ];

        foreach ($tests as $input => $output) {
            $this->assertSame(Common::getTimeFromSeconds($input), $output);
            $this->assertSame(Common::getTimeFromSeconds($input, true), explode(':', $output));
        }

        $tests = [
            0                                       => '00d:00h:00m:00s',
            1                                       => '00d:00h:00m:01s',
            100                                     => '00d:00h:01m:40s',
            101                                     => '00d:00h:01m:41s',
            3600                                    => '00d:01h:00m:00s',
            3600 * 24 + 3600 + 60 + 3               => '01d:01h:01m:03s',
            99 * 86400 + 3600 + 63                  => '99d:01h:01m:03s',
            9999 * 86400 + 23 * 3600 + 59 * 60 + 59 => '9999d:23h:59m:59s',
        ];

        foreach ($tests as $input => $output) {
            $this->assertSame(Common::getTimeFromSeconds($input, false, true), $output);
            $this->assertSame(Common::getTimeFromSeconds($input, true, true), explode(':', $output));
        }

    }

    public function testGetIP()
    {
        $this->assertFalse(Common::getClientIP());
    }

    public function testGetProcessesList()
    {

        $processes = Common::getProcessesList();

        $this->assertTrue(is_array($processes));
        $this->assertTrue(!empty($processes));

        $searchStrings = ['cmd.exe'];

        if (Common::isLinuxOS()) {
            $searchStrings = ['sh', 'ps'];
        }

        $found = false;

        foreach ($searchStrings as $searchString) {
            foreach ($processes as $process) {
                if (mb_stripos($process, $searchString, 0, Common::getDefaultEncoding()) !== false) {
                    $found = true;
                    break 2;
                }
            }
        }

        $this->assertTrue($found);

    }

    public function testGetFileExtension()
    {
        $tests = [
            ''                                                            => false,
            '\file'                                                       => false,
            '\file.exe'                                                   => 'exe',
            '\file.exe.'                                                  => false,
            '\file.'                                                      => false,
            'file.zip'                                                    => 'zip',
            '\file.exec'                                                  => 'exec',
            '\file.gitkeep'                                               => 'gitkeep',
            '.gitkeep'                                                    => 'gitkeep',
            '..gitkeep'                                                   => 'gitkeep',
            '.img.gitkeep'                                                => 'gitkeep',
            '.ra.img.gitkeep'                                             => 'gitkeep',
            'sas/adad/fefef/ttt.rar.fd/efef.wfdwd/.dwddwd.dwdwd/file.exe' => 'exe',
            '/sas/adad/file.exe'                                          => 'exe',
            '\sas\adad\file.exe'                                          => 'exe',
        ];

        foreach ($tests as $file => $ext) {
            $this->assertSame(Common::getFileExtension($file), $ext);
        }
    }

    public function testGetFileWithoutExtension()
    {
        $tests = [
            ''                                                            => false,
            '\file'                                                       => 'file',
            '\file.exe'                                                   => 'file',
            '\efwqf\wqfwef\wqefwefg.fwefwef\file.exe'                     => 'file',
            'efwqf/wqfwef/wqefwefg.fwefwef/file.exe'                      => 'file',
            '\efwqf\wqfwef\wqefwefg.fwefwef\fsdf\file.exe'                => 'file',
            'file.zip'                                                    => 'file',
            '\file.exec'                                                  => 'file',
            '\file.gitkeep'                                               => 'file',
            '.gitkeep'                                                    => false,
            '..gitkeep'                                                   => false,
            '.img.gitkeep'                                                => '.img',
            '.ra.img.gitkeep'                                             => '.ra.img',
            'sas/adad/fefef/ttt.rar.fd/efef.wfdwd/.dwddwd.dwdwd/file.exe' => 'file',
        ];

        foreach ($tests as $file => $result) {
            $this->assertSame(Common::getFilenameWithoutExtension($file), $result);
        }
    }

    public function testExecuteWithTimeoutSystem()
    {

        if (Common::isLinuxOS()) {
            $this->assertTrue(Common::isTimeoutLinuxInstalled(), 'Timeout is not installed');
        }

        $testData = [
            [
                'command'  => 'php 10sec.php',
                'timeout'  => 20,
                'expected' => '++++++++++',
                'result'   => true,
            ],
            [
                'command'  => 'php 10sec.php',
                'timeout'  => 25,
                'expected' => '++++++++++',
                'result'   => true,
            ],
            [
                'command'  => 'php 10sec.php',
                'timeout'  => 12,
                'expected' => '++++++++++',
                'result'   => true,
            ],
            [
                'command'  => 'php 10sec.php',
                'timeout'  => 6,
                'expected' => '++++++',
                'result'   => Common::isWindowsOS(),
            ],
        ];

        if (Common::isLinuxOS()) {

            $testData[] = [
                'command'  => 'sleep 10',
                'timeout'  => 5,
                'expected' => '',
                'result'   => false,
            ];

            $testData[] = [
                'command'  => 'sleep 10',
                'timeout'  => 50,
                'expected' => '',
                'result'   => true,
            ];
        }

        foreach ($testData as $index => $test) {

            try {

                $result = Common::executeInSystemWithTimeout($test['command'], $test['timeout'], $output, $exitcode);

                $this->assertSame($result, $test['result'], 'Results of test ' . $index . ' not the same');
                $this->assertSame($output, $test['expected']);

            } catch (\Exception $e) {

            } catch (\Throwable $e) {

            }

        }

    }

    public function testExtensionsInstalled()
    {

        $functions = ['mb_strlen'];

        if (Common::isLinuxOS()) {
            $functions[] = 'posix_kill';
        }

        foreach ($functions as $function) {
            $this->assertTrue(function_exists($function));
        }

    }

    public function testGetNullDevice()
    {

        if (Common::isWindowsOS()) {
            $this->assertSame(Common::getNullDevice(), 'nul');
        } else {
            $this->assertSame(Common::getNullDevice(), '/dev/null');
        }

    }

    public function testCopyDirectory()
    {

        clearstatcache(true);

        $testsDirectory = __DIR__ . DIRECTORY_SEPARATOR;

        $copyDirectory = $testsDirectory . 'copy' . DIRECTORY_SEPARATOR;

        Common::removeDirectory($copyDirectory);

        $filesDir = $testsDirectory . 'files' . DIRECTORY_SEPARATOR;

        clearstatcache(true);

        $this->assertFalse(is_dir($copyDirectory));

        Common::createDirectoryIfNotExists($copyDirectory);

        $this->assertTrue(Common::copy($filesDir, $copyDirectory));

        clearstatcache(true);
        $this->assertTrue(is_dir($copyDirectory));
        $this->assertTrue(is_dir($copyDirectory . 'files'));

        $baseName = function ($array) {
            return array_map(function ($value) {
                return basename($value);
            }, $array);
        };

        $this->assertSame(
            $baseName(Common::getDirectoryContentRecursive($filesDir)),
            $baseName(Common::getDirectoryContentRecursive($copyDirectory . 'files' . DIRECTORY_SEPARATOR))
        );

        Common::removeDirectory($copyDirectory);

    }

    public function testCopyFile()
    {

        $testsDirectory = __DIR__ . DIRECTORY_SEPARATOR;

        $classesDir = $testsDirectory . 'classes' . DIRECTORY_SEPARATOR;
        $filesDir = $testsDirectory . 'files' . DIRECTORY_SEPARATOR;

        $source = $filesDir . 'lines.txt';
        $destination = $classesDir . 'lines.txt';

        @unlink($destination);

        $this->assertFalse(file_exists($destination));

        $this->assertTrue(Common::copy($source, $classesDir));

        $this->assertTrue(file_exists($destination));

        $this->assertSame(md5_file($source), md5_file($destination));

        @unlink($destination);

    }

    public function testFirstLastFileLines()
    {
        $testFile = __DIR__ . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'lines.txt';
        $emptyFile = __DIR__ . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'emptyLines.txt';

        $tests = [
            'first' => [
                [
                    $testFile,
                    4,
                    '0' . PHP_EOL .
                    'this is not a test' . PHP_EOL .
                    ' 2' . PHP_EOL .
                    'а это  русский текст',
                ],
                [
                    $emptyFile,
                    4,
                    '',
                ],
            ],
            'last'  => [
                [
                    $testFile,
                    4,
                    '6' . PHP_EOL .
                    '7' . PHP_EOL .
                    '8' . PHP_EOL .
                    ' 9',
                ],
                [
                    $emptyFile,
                    4,
                    '',
                ],
                [
                    $testFile,
                    8,
                    'а это  русский текст' . PHP_EOL .
                    PHP_EOL .
                    '4' . PHP_EOL .
                    ' 5' . PHP_EOL .
                    '6' . PHP_EOL .
                    '7' . PHP_EOL .
                    '8' . PHP_EOL .
                    ' 9',
                ],
            ],
        ];

        foreach ($tests as $testMethod => $testData) {
            foreach ($testData as $index => $test) {

                $result = '';

                if ($testMethod == 'first') {
                    $result = Common::getFirstFileLines($test[0], $test[1]);
                } else {
                    $result = Common::getLastFileLines($test[0], $test[1]);
                }

                $this->assertSame(
                    $result,
                    $test[2],
                    'Can\'t assert results is the same! Test method=' . $testMethod . ', test #' . $index
                );
            }
        }

    }

    public function testMicrotime()
    {

        $timestamps = [];

        for ($i = 0; $i < 300; $i++) {
            $timestamp = Common::getTimestampMicrotime();
            $this->assertTrue(is_numeric($timestamp));
            $timestamps[] = $timestamp;
        }

    }

    public function testReadFileByLinesFromEnd()
    {

        $testFile = __DIR__ . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'lines.txt';
        $emptyFile = __DIR__ . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'emptyLines.txt';
        $oneFile = __DIR__ . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'oneLine.txt';

        $string = '';

        $callback = function ($line) use (&$string) {
            $string .= $line;
        };

        $testData = [
            $testFile  => [
                [null, ' 9876 54а это  русский текст 2this is not a test0'],
                [999999999, ' 9876 54а это  русский текст 2this is not a test0'],
                [6, ' 9876 54'],
                [8, ' 9876 54а это  русский текст'],
                [9, ' 9876 54а это  русский текст 2'],
                [10, ' 9876 54а это  русский текст 2this is not a test'],
            ],
            $emptyFile => [
                [null, ''],
                [6, ''],
                [8, ''],
                [8999999, ''],
            ],
            $oneFile   => [
                [null, ' здесь всего одна  строка'],
                [1, ' здесь всего одна  строка'],
                [6, ' здесь всего одна  строка'],
                [8, ' здесь всего одна  строка'],
                [8999999, ' здесь всего одна  строка'],
            ],
        ];

        foreach ($testData as $file => $test) {

            foreach ($test as $data) {

                $string = '';

                Common::readFileByLinesFromEnd($file, $callback, $data[0]);

                $this->assertSame($data[1], $string);
            }

        }

    }

    public function testReadFileByLinesFromEnd2()
    {

        $testFile = __DIR__ . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'lines.txt';
        $emptyFile = __DIR__ . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'emptyLines.txt';
        $twoEmptyFile = __DIR__ . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'twoEmptyLines.txt';
        $threeEmptyFile = __DIR__ . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'threeEmptyLines.txt';
        $oneEmptyFile = __DIR__ . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'oneLine.txt';
        $oneFile = __DIR__ . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'oneLine.txt';

        $string = '';

        $lines = [];

        $callback = function ($line) use (&$lines) {
            $lines[] = $line;
        };

        $testData = [
            $testFile       => [
                [
                    null,
                    [
                        ' 9',
                        '8',
                        '7',
                        '6',
                        ' 5',
                        '4',
                        '',
                        'а это  русский текст',
                        ' 2',
                        'this is not a test',
                        '0',
                    ],
                ],
                [
                    999999999,
                    [
                        ' 9',
                        '8',
                        '7',
                        '6',
                        ' 5',
                        '4',
                        '',
                        'а это  русский текст',
                        ' 2',
                        'this is not a test',
                        '0',
                    ],
                ],
                [
                    6,
                    [
                        ' 9',
                        '8',
                        '7',
                        '6',
                        ' 5',
                        '4',
                    ],
                ],
                [
                    8,
                    [
                        ' 9',
                        '8',
                        '7',
                        '6',
                        ' 5',
                        '4',
                        '',
                        'а это  русский текст',
                    ],
                ],
                [
                    9,
                    [
                        ' 9',
                        '8',
                        '7',
                        '6',
                        ' 5',
                        '4',
                        '',
                        'а это  русский текст',
                        ' 2',
                    ],
                ],
                [
                    10,
                    [
                        ' 9',
                        '8',
                        '7',
                        '6',
                        ' 5',
                        '4',
                        '',
                        'а это  русский текст',
                        ' 2',
                        'this is not a test',
                    ],
                ],
            ],
            $oneEmptyFile   => [
                [
                    null,
                    [
                        '',
                    ],
                ],
            ],
            $twoEmptyFile   => [
                [
                    null,
                    [
                        '',
                        '',
                    ],
                ],
            ],
            $threeEmptyFile => [
                [
                    null,
                    [
                        '',
                        '',
                        '',
                    ],
                ],
            ],
            $emptyFile      => [
                [
                    null,
                    [],
                ],
                [
                    6,
                    [],
                ],
                [
                    8,
                    [],
                ],
                [
                    8999999,
                    [],
                ],
            ],
            $oneFile        => [
                [
                    null,
                    [' здесь всего одна  строка'],
                ],
                [
                    1,
                    [' здесь всего одна  строка'],
                ],
                [
                    6,
                    [' здесь всего одна  строка'],
                ],
                [
                    8,
                    [' здесь всего одна  строка'],
                ],
                [
                    8999999,
                    [' здесь всего одна  строка'],
                ],
            ],
        ];

        foreach ($testData as $file => $test) {

            foreach ($test as $index => $data) {

                $lines = [];

                Common::readFileByLinesFromEnd($file, $callback, $data[0]);

                $this->assertSame(
                    $data[1],
                    $lines,
                    "Can't assert results the same in test file " . $file . " test #" . $index
                );
            }

        }

    }

    public function testReadFileCallbackFalseAndLimit()
    {
        $testFile = __DIR__ . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'lines.txt';

        /**
         * Return false
         */
        $lines = [];

        Common::readFileByLines($testFile, function ($line) use (&$lines) {

            if ($line == '') {
                return false;
            }

            $lines[] = $line;

        }, null);

        $this->assertSame($lines, [
            '0',
            'this is not a test',
            ' 2',
            'а это  русский текст',
        ]);

        /**
         * Limit before
         */

        $lines = [];

        Common::readFileByLines($testFile, function ($line) use (&$lines) {

            if ($line == '') {
                return false;
            }

            $lines[] = $line;

        }, 4, null);

        $this->assertSame($lines, [
            '0',
            'this is not a test',
            ' 2',
            'а это  русский текст',
        ]);

        $lines = [];

        Common::readFileByLines($testFile, function ($line) use (&$lines) {

            if ($line == '') {
                return false;
            }

            $lines[] = $line;

        }, 3, null);

        $this->assertSame($lines, [
            '0',
            'this is not a test',
            ' 2',
        ]);
    }

    public function testReadFileFromEndCallbackFalseAndLimit()
    {
        $testFile = __DIR__ . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'lines.txt';

        /**
         * Return false
         */
        $lines = [];

        Common::readFileByLinesFromEnd($testFile, function ($line) use (&$lines) {

            if ($line == '6') {
                return false;
            }

            $lines[] = $line;

        }, null);

        $this->assertSame($lines, [
            ' 9',
            '8',
            '7',
        ]);

        /**
         * Limit before
         */
        $lines = [];

        Common::readFileByLinesFromEnd($testFile, function ($line) use (&$lines) {

            if ($line == '6') {
                return false;
            }

            $lines[] = $line;

        }, 3);

        $this->assertSame($lines, [
            ' 9',
            '8',
            '7',
        ]);

        $lines = [];

        Common::readFileByLinesFromEnd($testFile, function ($line) use (&$lines) {

            if ($line == '6') {
                return false;
            }

            $lines[] = $line;

        }, 2);

        $this->assertSame($lines, [
            ' 9',
            '8',
        ]);

        $lines = [];

        Common::readFileByLinesFromEnd($testFile, function ($line) use (&$lines) {

            if ($line == '6') {
                return false;
            }

            $lines[] = $line;

        }, 1);

        $this->assertSame($lines, [
            ' 9',
        ]);

    }

    public function testReadFileByLines()
    {

        $testFile = __DIR__ . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'lines.txt';
        $emptyFile = __DIR__ . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'emptyLines.txt';
        $oneFile = __DIR__ . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'oneLine.txt';

        $string = '';

        $callback = function ($line) use (&$string) {
            $string .= $line;
        };

        $testData = [
            $testFile  => [
                [null, '0this is not a test 2а это  русский текст4 5678 9'],
                [9999999999, '0this is not a test 2а это  русский текст4 5678 9'],
                [6, '0this is not a test 2а это  русский текст4'],
                [8, '0this is not a test 2а это  русский текст4 56'],
            ],
            $emptyFile => [
                [null, ''],
                [6, ''],
                [8, ''],
                [8999999, ''],
            ],
            $oneFile   => [
                [null, ' здесь всего одна  строка'],
                [1, ' здесь всего одна  строка'],
                [6, ' здесь всего одна  строка'],
                [8, ' здесь всего одна  строка'],
                [8999999, ' здесь всего одна  строка'],
            ],
        ];

        foreach ($testData as $file => $test) {

            foreach ($test as $data) {

                $string = '';

                Common::readFileByLines($file, $callback, $data[0]);

                $this->assertSame($data[1], $string);
            }

        }

    }

    public function testCombinations()
    {
        $this->assertTrue(count(Common::getAllCombinations(['a', 'b', 'c'])) == 15);
        $this->assertTrue(count(Common::getAllCombinations(['a', 'b', 'c', 'f'])) == 64);
    }

    public function testMoveDirectory()
    {

        $dir1 = __DIR__ . DIRECTORY_SEPARATOR . '1' . DIRECTORY_SEPARATOR;

        $file1 = $dir1 . 'file1.txt';

        $dir2 = __DIR__ . DIRECTORY_SEPARATOR . '2' . DIRECTORY_SEPARATOR;

        Common::removeDirectory($dir1);
        Common::removeDirectory($dir2);

        clearstatcache(true);

        $this->assertFalse(is_dir($dir1));
        $this->assertFalse(is_dir($dir2));

        mkdir($dir1);
        touch($file1);

        $this->assertTrue(is_dir($dir1));
        $this->assertTrue(file_exists($file1));

        $this->assertFalse(is_dir($dir2));

        Common::move($dir1, $dir2);

        clearstatcache(true);

        $this->assertFalse(is_dir($dir1));
        $this->assertFalse(file_exists($file1));
        $this->assertTrue(is_dir($dir2));
        $this->assertTrue(file_exists($dir2 . 'file1.txt'));

        Common::removeDirectory($dir1);
        Common::removeDirectory($dir2);

        clearstatcache(true);

        $this->assertFalse(is_dir($dir1));
        $this->assertFalse(is_dir($dir2));

    }

    public function testMoveFile()
    {
        $dir = __DIR__ . DIRECTORY_SEPARATOR;

        $file1 = $dir . 'file1.txt';
        $file2 = $dir . 'file2.txt';

        @unlink($file1);
        @unlink($file2);

        clearstatcache(true);

        $this->assertFalse(file_exists($file1));
        $this->assertFalse(file_exists($file2));

        touch($file1);

        clearstatcache(true);
        $this->assertTrue(file_exists($file1));
        $this->assertFalse(file_exists($file2));

        Common::move($file1, $file2);

        clearstatcache(true);
        $this->assertFalse(file_exists($file1));
        $this->assertTrue(file_exists($file2));

        clearstatcache(true);

        @unlink($file1);
        @unlink($file2);

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
                                       __DIR__ . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'emptyLines.txt',
                                       __DIR__ . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'lines.txt',
                                       __DIR__ . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'oneEmptyLines.txt',
                                       __DIR__ . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'oneLine.txt',
                                       __DIR__ . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'only-for-test',
                                       __DIR__ . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'StringUTF-8.txt',
                                       __DIR__ . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'StringWin1251.txt',
                                       __DIR__ . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'threeEmptyLines.txt',
                                       __DIR__ . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'twoEmptyLines.txt',
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
                                       __DIR__ . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'emptyLines.txt',
                                       __DIR__ . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'lines.txt',
                                       __DIR__ . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'oneEmptyLines.txt',
                                       __DIR__ . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'oneLine.txt',
                                       __DIR__ . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'only-for-test',
                                       __DIR__ . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'only-for-test' . DIRECTORY_SEPARATOR . '!file.empty-for-test',
                                       __DIR__ . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'StringUTF-8.txt',
                                       __DIR__ . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'StringWin1251.txt',
                                       __DIR__ . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'threeEmptyLines.txt',
                                       __DIR__ . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'twoEmptyLines.txt',
                                   ],
                               ],
                               [
                                   Common::getDirectoryContent(__DIR__ . DIRECTORY_SEPARATOR . 'classes'),
                                   Common::getDirectoryContentRecursive(__DIR__ . DIRECTORY_SEPARATOR . 'classes'),
                               ],
                               [
                                   Common::getDirectoryContent(__DIR__ . DIRECTORY_SEPARATOR . 'unexisweqf3f34f'),
                                   [],
                               ],
                               [
                                   Common::getDirectoryContentRecursive(__DIR__ . DIRECTORY_SEPARATOR . 'unexisweqf3f34f'),
                                   [],
                               ],
                           ]);

    }

    public static function getDuration($callback, $args = [])
    {
        $startTime = time();

        call_user_func($callback, $args);

        return time() - $startTime;
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
                               [Common::getHumanReadableBytes(pow(1024, 8) * 2.5, ' - '), '2.5 - YB'],
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
                                   Common::convertToDefaultEncoding(file_get_contents(static::getPackagePath('tests/files/StringWin1251.txt')),
                                                                    'Windows-1251'),
                                   'строка',
                               ],
                               [
                                   Common::convertToDefaultEncoding(file_get_contents(static::getPackagePath('tests/files/StringUTF-8.txt')),
                                                                    'UTF-8'),
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

    protected function getSleepSystemCommand($seconds)
    {

        $command = Common::isWindowsOS() ? 'timeout' : 'sleep';

        $command .= ' ' . $seconds . ' > ' . Common::getNullDevice() . ' 2>&1';

        return $command;
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

        $this->assertTrue(preg_match('/^\+{10}$/', file_get_contents($file)) === 1);

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

        Common::executeInSystemWithTimeout(static::getSleepSystemCommand(30), 10);

    }

    public function createTestDirectories()
    {
        $deepest = __DIR__ . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, range(1, static::$testDireftoriesDepth,
                                                                                      1)) . DIRECTORY_SEPARATOR;

        mkdir($deepest, 0777, true);
        $this->assertTrue(file_exists($deepest));

        for ($i = 1; $i <= static::$testDireftoriesDepth; $i++) {
            $file = __DIR__ . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, range(1, $i,
                                                                                       1)) . DIRECTORY_SEPARATOR . $i . '.' . static::$testExt;
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
        clearstatcache();

        $this->createTestDirectories();

        $this->assertTrue(is_dir($this->getTestDirectoryPath()));

        Common::removeDirectory($this->getTestDirectoryPath());

        clearstatcache();

        $this->assertFalse(file_exists($this->getTestDirectoryPath()));
        $this->assertFalse(is_dir($this->getTestDirectoryPath()));

    }

    public function testMakeEmptyDirectory()
    {
        $this->createTestDirectories();

        clearstatcache();

        $this->assertTrue(is_dir($this->getTestDirectoryPath()));

        Common::removeDirectoryContents($this->getTestDirectoryPath());

        clearstatcache();
        $this->assertTrue(is_dir($this->getTestDirectoryPath()));

        clearstatcache();
        $this->assertSame([], Common::getDirectoryContentRecursive($this->getTestDirectoryPath()));

        clearstatcache();
        Common::removeDirectory($this->getTestDirectoryPath());

    }

    public function testCreateDirectoryIfNotExists()
    {
        clearstatcache();
        $this->assertFalse(file_exists($this->getTestDirectoryPath()));

        Common::createDirectoryIfNotExists($this->getTestDirectoryPath());

        clearstatcache();
        $this->assertTrue(file_exists($this->getTestDirectoryPath()));

        Common::createDirectoryIfNotExists($this->getTestDirectoryPath());

        clearstatcache();
        $this->assertTrue(file_exists($this->getTestDirectoryPath()));

        rmdir($this->getTestDirectoryPath());

        clearstatcache();
        $this->assertFalse(file_exists($this->getTestDirectoryPath()));
    }

}
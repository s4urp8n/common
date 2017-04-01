<?php

namespace Zver {

    /**
     * Class Common
     *
     * Common project methods and properties
     *
     * @package Zver
     */
    class Common
    {

        /**
         * Get default encoding of project
         *
         * @return string
         */
        public static function getDefaultEncoding()
        {
            return 'UTF-8';
        }

        /**
         * Replace slashes to current platform slashes
         *
         * @param $path
         * @return string
         */
        public static function replaceSlashesToPlatformSlashes($path)
        {
            return mb_eregi_replace('[' . preg_quote('/') . preg_quote('\\') . ']+', DIRECTORY_SEPARATOR, $path);
        }

        /**
         * Convert string from another encoding to default project encoding
         *
         * @param $string
         * @param $fromEncoding
         * @return string
         */
        public static function convertToDefaultEncoding($string, $fromEncoding)
        {
            return iconv($fromEncoding, self::getDefaultEncoding() . '//IGNORE', $string);
        }

        /**
         * Register autoloading PSR-4 from directory
         *
         * @param string $directory
         */
        public static function registerAutoloadClassesFrom($directory)
        {
            spl_autoload_register(function ($className) use ($directory) {

                $realDirectory = realpath(static::replaceSlashesToPlatformSlashes($directory));

                if (file_exists($realDirectory)) {

                    /**
                     * Trailing slash
                     */
                    $realDirectory = mb_eregi_replace(preg_quote(DIRECTORY_SEPARATOR) . '+$', '', $realDirectory);

                    /**
                     * Full class name
                     */
                    $fileName = $realDirectory . DIRECTORY_SEPARATOR
                                . trim(static::replaceSlashesToPlatformSlashes($className), '\\/')
                                . '.php';

                    if (file_exists($fileName)) {
                        include_once $fileName;
                    }

                }
            });
        }

        /**
         * Get common path of both path's
         *
         * @param $path1
         * @param $path2
         * @return string
         */
        public static function getCommonPath($path1, $path2)
        {
            $common = [];

            $parts1 = explode(DIRECTORY_SEPARATOR, realpath(\Zver\Common::replaceSlashesToPlatformSlashes($path1)));
            $parts2 = explode(DIRECTORY_SEPARATOR, realpath(\Zver\Common::replaceSlashesToPlatformSlashes($path2)));

            foreach ($parts1 as $key => $value) {
                if ($parts2[$key] == $value) {
                    $common[] = $value;
                } else {
                    break;
                }
            }

            $common = implode(DIRECTORY_SEPARATOR, $common);

            if ($common !== DIRECTORY_SEPARATOR) {
                $common = $common . DIRECTORY_SEPARATOR;
            }

            return $common;

        }

        /**
         * Get full path to file in package tests files folder
         *
         * @param $name Name of file
         * @return string Full path to file in package tests folder
         */
        public static function getPackageTestFilePath($name)
        {
            $calledFile = debug_backtrace()[0]['file'];
            $calledFileName = pathinfo($calledFile, PATHINFO_FILENAME);

            /**
             * Test environment, using from phpunit testing class
             */
            $test = mb_substr($calledFileName, -4, null, \Zver\Common::getDefaultEncoding()) == 'Test';

            /**
             * Using in current (present) test (in this file) (here!)
             */
            if ($test) {
                return realpath(dirname($calledFile) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . \Zver\Common::replaceSlashesToPlatformSlashes($name);
            }

            $commonPart = static::getCommonPath($calledFile, __DIR__);

            $currentVendor = mb_substr(__DIR__, mb_strlen($commonPart, Common::getDefaultEncoding()));
            $currentVendor = mb_split(static::getSlashesRegexp(), $currentVendor)[0];

            $calledVendor = mb_substr($calledFile, mb_strlen($commonPart, Common::getDefaultEncoding()));
            $calledVendor = mb_split(static::getSlashesRegexp(), $calledVendor)[0];

            if ($currentVendor == 'src') {
                $calledVendor = '';
            } else {
                $calledVendor .= DIRECTORY_SEPARATOR;
            }

            return $commonPart . $calledVendor . 'tests' . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . \Zver\Common::replaceSlashesToPlatformSlashes($name);
        }

        protected static function getSlashesRegexp()
        {
            return '[' . preg_quote('\\') . preg_quote('/') . ']+';
        }

        /**
         * Get full path to file in package files folder
         *
         * @param $name Name of file
         * @return string Full path to file
         */
        public static function getPackageFilePath($name)
        {
            $calledFile = debug_backtrace()[0]['file'];
            $calledFileName = pathinfo($calledFile, PATHINFO_FILENAME);

            /**
             * Test environment, using from phpunit testing class
             */
            $test = mb_substr($calledFileName, -4, null, \Zver\Common::getDefaultEncoding()) == 'Test';

            /**
             * Using in current (present) test (in this file) (here!)
             */
            if ($test) {
                return realpath(dirname($calledFile) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . \Zver\Common::replaceSlashesToPlatformSlashes($name);
            }

            $commonPart = static::getCommonPath($calledFile, __DIR__);

            $currentVendor = mb_substr(__DIR__, mb_strlen($commonPart, Common::getDefaultEncoding()));
            $currentVendor = mb_split(static::getSlashesRegexp(), $currentVendor)[0];

            $calledVendor = mb_substr($calledFile, mb_strlen($commonPart, Common::getDefaultEncoding()));
            $calledVendor = mb_split(static::getSlashesRegexp(), $calledVendor)[0];

            if ($currentVendor == 'src') {
                $calledVendor = '';
            } else {
                $calledVendor .= DIRECTORY_SEPARATOR;
            }

            return $commonPart . $calledVendor . 'files' . DIRECTORY_SEPARATOR . \Zver\Common::replaceSlashesToPlatformSlashes($name);
        }

        public static function getOSName()
        {
            return PHP_OS;
        }

        public static function isWindowsOS($forcedString = null)
        {
            $string = empty($forcedString) ? static::getOSName() : $forcedString;

            $regexps = [
                '/\bwindows\b/i',
                '/\bwinnt\b/i',
                '/\bwin\b/i',
            ];

            foreach ($regexps as $regexp) {
                if (preg_match($regexp, $string) === 1) {
                    return true;
                }
            }

            return false;
        }

        public static function isLinuxOS($forcedString = null)
        {
            $string = empty($forcedString) ? static::getOSName() : $forcedString;

            $regexps = [
                '/linux/i',
                '/\bubuntu\b/i',
                '/\bfedora\b/i',
                '/\bfedoracore\b/i',
                '/\bdebian\b/i',
                '/\bmandriva\b/i',
                '/\bslackware\b/i',
                '/\bmint\b/i',
                '/\bgentoo\b/i',
                '/\bmageia\b/i',
                '/\barch\b/i',
                '/\bcentos\b/i',
            ];

            foreach ($regexps as $regexp) {
                if (preg_match($regexp, $string) === 1) {
                    return true;
                }
            }

            return false;
        }

        public static function isProcessRunning($pid, $processName = null)
        {
            $windowsCommand = 'tasklist';
            $windowsRegexp = '\s+' . $pid . '\s+';

            $linuxCommand = "ps -A";
            $linuxRegexp = '^\s*' . $pid . '\s+';

            if (!empty($processName)) {
                $windowsRegexp = '^' . $processName . '\S+' . $windowsRegexp;
                $linuxCommand = $linuxCommand . " | grep " . escapeshellarg($processName);
            }

            $command = $windowsCommand;
            $regexp = $windowsRegexp;

            if (static::isLinuxOS()) {
                $command = $linuxCommand;
                $regexp = $linuxRegexp;
            }

            $regexp = "/" . $regexp . "/i";

            $outputs = preg_split("/[\n\r]+/i", static::executeInSystem($command));

            foreach ($outputs as $output) {
                if (preg_match($regexp, $output) === 1) {
                    return true;
                }
            }

            return false;
        }

        protected static function sortFilesAndFolders($filesAndFolders)
        {
            usort($filesAndFolders, function ($a, $b) {
                return strcasecmp($a, $b);
            });

            return $filesAndFolders;
        }

        public static function getDirectoryContent($directory)
        {
            $content = [];

            if (is_dir($directory)) {

                $content = scandir($directory, SCANDIR_SORT_ASCENDING);
                array_shift($content);
                array_shift($content);

                $content = array_map(function ($value) use ($directory) {
                    return realpath($directory . DIRECTORY_SEPARATOR . $value);
                }, $content);

                $content = static::sortFilesAndFolders($content);

            }

            return $content;
        }

        public static function getDirectoryContentRecursive($directory)
        {
            $content = [];

            foreach (static::getDirectoryContent($directory) as $path) {
                if (is_file($path)) {
                    $content[] = $path;
                } else {
                    $content[] = $path;
                    $content = array_merge($content, static::getDirectoryContentRecursive($path));
                }
            }

            return static::sortFilesAndFolders($content);
        }

        public static function executeInSystem($command)
        {
            $handle = popen($command, 'r');
            $output = stream_get_contents($handle);
            pclose($handle);

            return $output;
        }

        public static function killProcess($pid)
        {
            if (static::isWindowsOS()) {
                static::executeInSystem('taskkill /F /s localhost /PID ' . $pid);
            } else {
                posix_kill($pid, SIGKILL);
            }
        }

        public static function executeInSystemAsync($command, $outputFile = null)
        {
            if (static::isWindowsOS()) {

                $windowsCommand = 'start /b "async bg command" ' . $command;

                if (!empty($outputFile)) {
                    $windowsCommand .= ' > "' . $outputFile . '"';
                }

                pclose(popen($windowsCommand, 'r'));

            } else {

                $output = empty($outputFile) ? '/dev/null' : $outputFile;

                shell_exec($command . ' > ' . $output . ' &');
            }
        }

        public static function getHumanReadableBytes($bytes, $spaceBefore = ' ')
        {

            $sizes = ["B", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB"];
            $index = 0;

            $divided = $bytes;

            while ($divided >= 1024) {
                $divided /= 1024;
                $index++;
            }

            $result = round($divided, 1);

            return $result . $spaceBefore . $sizes[$index];
        }

        public static function createDirectoryIfNotExists($directory, $mode = 0777)
        {
            if (!is_dir($directory)) {
                mkdir($directory, $mode, true);
            }
        }

        public static function removeDirectory($directory, $removeSelf = true)
        {
            foreach (static::getDirectoryContent($directory) as $path) {
                if (is_file($path)) {
                    unlink($path);
                } else {
                    static::removeDirectory($path, true);
                }
            }

            if ($removeSelf && is_dir($directory)) {
                rmdir($directory);
            }
        }

        public static function removeDirectoryContents($directory)
        {
            return static::removeDirectory($directory, false);
        }

    }
}

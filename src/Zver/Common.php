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
            return mb_eregi_replace('[' . static::getSlashesRegExp() . ']+', DIRECTORY_SEPARATOR, $path);
        }

        public static function stripBeginningSlashes($path)
        {
            return mb_eregi_replace('^[' . static::getSlashesRegExp() . ']+', '', $path);
        }

        public static function stripEndingSlashes($path)
        {
            return mb_eregi_replace('[' . static::getSlashesRegExp() . ']+$', '', $path);
        }

        protected static function getSlashesRegExp()
        {
            return preg_quote('/') . preg_quote('\\');
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

        public static function executeInSystemWithTimeout($command, $timeout = 30, &$output = null, &$exitcode = null)
        {

            try {

                $descriptors = [
                    fopen('php://stdin', 'r'),
                    fopen('php://stdout', 'w'),
                    fopen('php://stderr', 'w'),
                ];

                $startTime = time();

                $handler = proc_open($command, $descriptors, $pipes);

                $isRunning = function ($handler) {
                    if (is_resource($handler)) {

                        $status = proc_get_status($handler);
                        if (!empty($status)) {
                            return $status['running'];
                        }
                    }

                    return false;
                };

                while ($isRunning($handler)) {

                    usleep(10);

                    if (time() - $startTime > $timeout) {

                        /**
                         * Timeout reached
                         */

                        @fclose($pipes[0]);
                        @fclose($pipes[1]);
                        @fclose($pipes[2]);

                        if (static::isWindowsOS()) {
                            $status = proc_get_status($handler);
                            static::killProcess($status['pid']);
                        } else {
                            proc_terminate($handler);
                        }

                        return false;
                    }

                }

                /**
                 * Process finished normally
                 */
                if (!is_null($output)) {
                    $output = stream_get_contents($pipes[1]);
                }

                @fclose($pipes[0]);
                @fclose($pipes[1]);
                @fclose($pipes[2]);

                if (is_null($exitcode)) {
                    proc_close($handler);
                } else {
                    $exitcode = proc_close($handler);
                }

                return true;

            }
            catch (\Throwable $t) {

            }
            catch (\Exception $e) {

            }

            return false;

        }

        public static function killProcess($pid)
        {
            if (static::isWindowsOS()) {
                static::executeInSystem('taskkill /F /T /s localhost /PID ' . $pid . ' 2>&1');
            } else {
                \posix_kill($pid, SIGKILL);
            }
        }

        public static function executeInSystemAsync($command, $outputFile = null)
        {
            if (static::isWindowsOS()) {

                $windowsCommand = 'start /b "async bg command" ' . $command;

                if (!empty($outputFile)) {
                    $windowsCommand .= ' > "' . $outputFile . '" 2>&1';
                }

                pclose(popen($windowsCommand, 'r'));

            } else {

                $output = empty($outputFile) ? '/dev/null' : $outputFile;

                shell_exec($command . ' > ' . $output . ' 2>&1 &');
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

        public static function removeDirectory($directory)
        {
            clearstatcache();
            if (is_dir($directory)) {

                $command = static::isWindowsOS()
                    ? sprintf('rmdir /s /q "%s"', $directory)
                    : sprintf('rm -rf "%s"', $directory);

                shell_exec($command);
            }
        }

        public static function removeDirectoryContents($directory)
        {
            clearstatcache();
            static::removeDirectory($directory);
            clearstatcache();
            static::createDirectoryIfNotExists($directory);
        }

    }
}

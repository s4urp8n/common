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
         * Return associative array where keys are pids and values are commands
         *
         * @return array
         */
        public static function getProcessesList()
        {

            $processes = [];

            $command = static::isWindowsOS() ? 'WMIC path win32_process get Commandline,Processid' : 'ps axo command,pid';

            @exec($command, $output, $exitcode);

            foreach ($output as $line) {

                $cmd = mb_eregi_replace('\s*\d+$', '', $line);

                if (trim($cmd) != '') {

                    $pid = mb_eregi_replace('\D+', '',
                                            mb_substr($line, mb_strlen($cmd, static::getDefaultEncoding()), null, static::getDefaultEncoding()));

                    if (is_numeric($pid) && $pid != 0) {

                        $processes[$pid] = $cmd;

                    }

                }
            }

            return $processes;

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

        public static function sortFilesAndFolders($filesAndFolders)
        {
            usort($filesAndFolders, function ($a, $b) {
                return strcasecmp($a, $b);
            });

            return $filesAndFolders;
        }

        public static function getDirectoryContent($directory)
        {
            clearstatcache(true);

            $content = [];

            if (is_dir($directory)) {

                $content = scandir($directory, SCANDIR_SORT_NONE);

                /**
                 * Dots
                 */
                $content = array_filter($content, function ($path) {
                    return ($path != '.' && $path != '..');
                });

                $content = array_map(function ($value) use ($directory) {
                    return realpath($directory . DIRECTORY_SEPARATOR . $value);
                }, $content);

                $content = static::sortFilesAndFolders($content);

            }

            return $content;
        }

        public static function getDirectoryContentRecursive($directory)
        {

            clearstatcache(true);

            $content = static::getDirectoryContent($directory);

            foreach ($content as $item) {
                if (is_dir($item)) {
                    $content = array_merge($content, static::getDirectoryContentRecursive($item));
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

        public static function isTimeoutLinuxInstalled()
        {

            $output = [];

            @exec('timeout --help 2>&1', $output, $exitcode);

            if (is_array($output)) {
                $output = implode("\n", $output);
            }

            if (preg_match('#\-\-help#i', $output) == 1
                &&
                preg_match('#\-\-kill#i', $output) == 1
                &&
                preg_match('#\-\-foreground#i', $output) == 1
                &&
                preg_match('#timeout#i', $output) == 1
                &&
                preg_match('#124#', $output) == 1
            ) {
                return true;
            }

            return false;

        }

        /**
         * Execute command. If timeout reached return false and kill programm.
         * If programm normally executed return true if exitcode is 0, false otherwise.
         * Output will placed to $output variable
         * Exitcode will placed to $exitcode variable.
         * Timeout functionality available only on linux systems
         *
         * @param      $command
         * @param int  $timeout
         * @param null $output
         * @param null $exitcode
         * @return bool
         */
        public static function executeInSystemWithTimeout(
            $command,
            $timeout = 30,
            &$output = null,
            &$exitcode = null
        ) {

            if (static::isLinuxOS()) {

                @exec('timeout --kill-after=' . ($timeout + 2) . ' ' . $timeout . ' ' . $command, $output, $exitcode);

                if ($exitcode == 137 || $exitcode == 124) {
                    //Timeout reached
                    return false;
                }

                return $exitcode == 0;

            } else {

                @exec($command, $output, $exitcode);

                return $exitcode == 0;

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

            if (is_null($outputFile)) {
                $outputFile = static::isWindowsOS() ? 'nul' : '/dev/null';
            }

            if (static::isWindowsOS()) {

                $windowsCommand = 'start /b "async bg command" ' . $command;

                $windowsCommand .= ' > "' . $outputFile . '" 2>&1';

                pclose(popen($windowsCommand, 'r'));

            } else {
                shell_exec($command . ' > ' . $outputFile . ' 2>&1 &');
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
            clearstatcache(true);

            if (is_dir($directory)) {

                $output = $exitCode = '';

                if (static::isWindowsOS()) {

                    $uniqHash = '____' . md5(uniqid(rand(), true) . microtime(true)) . rand(11, 99) . rand(11, 99);

                    $fullOutput = '';

                    /**
                     * Robocopy method
                     */
                    @exec('mkdir "' . $uniqHash . '" 2>&1', $output);
                    $fullOutput .= "\n\n" . implode("\n", $output);

                    @exec('robocopy "' . $uniqHash . '" "' . $directory . '" /s /mir 2>&1', $output);
                    $fullOutput .= "\n\n" . implode("\n", $output);

                    @exec('rmdir /s /q "' . $uniqHash . '" 2>&1', $output);
                    $fullOutput .= "\n\n" . implode("\n", $output);

                    /**
                     * Regular deletion
                     */
                    @exec('rmdir /s /q "' . $directory . '" 2>&1', $output, $exitCode);
                    $fullOutput .= "\n\n" . implode("\n", $output);

                    /**
                     * Some hack maybe worked 1
                     */
                    @exec('rmdir /s /q "\\.\\' . $uniqHash . '" 2>&1', $output);
                    $fullOutput .= "\n\n" . implode("\n", $output);

                    /**
                     * Some hack maybe worked 2
                     */
                    @exec('rmdir /s /q "\\\\' . $uniqHash . '" 2>&1', $output);
                    $fullOutput .= "\n\n" . implode("\n", $output);

                    $output = $fullOutput;
                    unset($fullOutput);

                } else {
                    @exec(sprintf('rm -rf "%s" 2>&1', $directory), $output, $exitCode);
                }

                return $exitCode == 0;
            }

            return false;
        }

        /**
         * Remove file or directory
         *
         * @param $path
         * @return bool
         */
        public static function remove($path)
        {
            clearstatcache(true);

            if (is_file($path)) {
                return unlink($path);
            } elseif (is_dir($path)) {
                return static::removeDirectory($path);
            }

        }

        public static function removeDirectoryContents($directory)
        {
            clearstatcache(true);
            static::removeDirectory($directory);
            clearstatcache(true);
            static::createDirectoryIfNotExists($directory);
        }

        public static function getNullDevice()
        {
            return static::isWindowsOS() ? 'nul' : '/dev/null';
        }

        /**
         * Copy file or directory to specified directory.
         * Destination directory must exists before copy started
         *
         * @param $source
         * @param $destinationDirectory
         * @return bool
         */
        public static function copy($source, $destinationDirectory)
        {

            if (file_exists($source) && is_dir($destinationDirectory)) {

                $source = static::replaceSlashesToPlatformSlashes($source);

                clearstatcache(true);

                $commandTemplate = 'xcopy "%s" "%s" /R /Q /Y /I /H';

                if (is_dir($source)) {

                    $commandTemplate .= ' /E';
                    /**
                     * Trailing directory separator
                     */
                    if ($source[mb_strlen($source) - 1] != DIRECTORY_SEPARATOR) {
                        if (static::isLinuxOS()) {
                            $source .= DIRECTORY_SEPARATOR;
                        }
                    } else {
                        $source = mb_substr($source, 0, -1, static::getDefaultEncoding());
                    }

                    /**
                     * On windows create folder structure
                     */
                    if (static::isWindowsOS()) {
                        static::createDirectoryIfNotExists(static::replaceSlashesToPlatformSlashes($destinationDirectory . DIRECTORY_SEPARATOR . basename($source)));
                    }

                }

                $destination =
                    is_dir($source)
                        ? static::replaceSlashesToPlatformSlashes($destinationDirectory . DIRECTORY_SEPARATOR . basename($source))
                        : $destinationDirectory;

                $command = sprintf($commandTemplate, $source, $destination);

                if (static::isLinuxOS()) {
                    $command = sprintf('\cp -fr --no-preserve=mode,ownership "%s" "%s"', $source, $destinationDirectory);
                }

                @exec($command . ' 2>&1', $output, $exitCode);

                return $exitCode == 0;
            }

            return false;
        }

        public
        static function move(
            $source,
            $destination
        ) {

            $source = static::stripEndingSlashes($source);
            $destination = static::stripEndingSlashes($destination);

            clearstatcache(true);

            if (file_exists($source) || is_dir($source)) {

                $command = 'move /Y ' . escapeshellarg($source) . ' ' . escapeshellarg($destination);

                if (static::isLinuxOS()) {
                    $command = 'mv -f ' . escapeshellarg($source) . ' ' . escapeshellarg($destination);
                }

                @exec($command . ' 2>&1', $output, $exitCode);

                return $exitCode == 0;

            }

            return false;
        }

        public
        static function getAllCombinations(
            array $array
        ) {
            $current = $combinations = [];
            $count = count($array);
            $max = pow(2, $count) - 1;

            $presentations = [];

            for ($i = 1; $i <= $max; $i++) {

                $presentation = decbin($i);

                while (strlen($presentation) < $count) {
                    $presentation = '0' . $presentation;
                }

                $current = [];

                for ($j = 0; $j < $count; $j++) {
                    if ($presentation[$j] == '1') {
                        $current[] = $array[$j];
                    }
                }

                $presentations[] = $current;

            }

            foreach ($presentations as $presentation) {

                $presentationCount = count($presentation);

                if ($presentationCount == 1) {
                    $combinations[] = $presentation;
                } else {

                    $maxPosition = str_repeat($presentationCount - 1, $presentationCount);

                    while ($maxPosition > 0) {

                        $positions = $maxPosition;
                        while (strlen($positions) < $presentationCount) {
                            $positions = '0' . $positions;
                        }

                        $positions = str_split($positions);

                        /**
                         * Now we must ensure that all positions is unique
                         */
                        $positionsCounts = array_count_values($positions);
                        rsort($positionsCounts);

                        $unique = ($positionsCounts[0] == 1);

                        if ($unique) {

                            /**
                             * Now we must ensure that all position is less or equals $presentationCount-1
                             */
                            $valid = true;

                            foreach ($positions as $p) {
                                if ($p > $presentationCount - 1) {
                                    $valid = false;
                                    break;
                                }
                            }

                            if ($valid) {

                                $combination = [];

                                foreach ($positions as $key => $position) {
                                    $combination[$position] = $presentation[$key];
                                }

                                ksort($combination);

                                $combinations[] = $combination;

                            }
                        }

                        $maxPosition--;
                    }

                }

            }

            return $combinations;

        }

        public
        static function getFileExtension(
            $filename
        ) {
            $lastDot = mb_strrpos($filename, '.', false, static::getDefaultEncoding());

            if ($lastDot !== false) {
                return mb_substr($filename, $lastDot + 1, null, static::getDefaultEncoding());
            }

            return false;
        }

        /**
         * Read file line-by-line until end of file is reached or $linesLimit reached or $callback return FALSE
         *
         * @param          $path
         * @param callable $callback
         * @param null     $linesLimit
         */
        public
        static function readFileByLines(
            $path,
            callable $callback,
            $linesLimit = null
        ) {

            $currentLine = -1;

            $fh = fopen($path, 'r');

            while (!feof($fh)) {

                $line = trim(fgets($fh), "\r\n");

                $currentLine++;

                $callbackResult = call_user_func($callback, $line);

                if (
                    (
                        !is_null($linesLimit)
                        &&
                        $currentLine >= $linesLimit - 1
                    )
                    ||
                    $callbackResult === false
                ) {
                    break;
                }

            }

            fclose($fh);

        }

        public
        static function readFileByLinesFromEnd(
            $path,
            callable $callback,
            $linesLimit = null
        ) {

            $lines = [];
            $fh = fopen($path, "r");
            fseek($fh, 0, SEEK_END);
            $min = 0;
            $max = ftell($fh);

            $lineCount = 0;
            $currentLine = '';

            for ($i = $max - 1; $i >= 0; $i--) {

                fseek($fh, $i, SEEK_SET);
                $char = fread($fh, 1);

                if ($char === "\r") {
                    continue;
                } else {
                    if ($char === "\n") {
                        /**
                         * END OF LINE
                         */
                        $lineCount++;

                        $lines[$lineCount] = $currentLine;

                        $callbackResult = call_user_func($callback, $lines[$lineCount]);

                        $currentLine = '';

                        if ($lineCount == $linesLimit || $callbackResult === false) {
                            break;
                        }

                    } elseif ($i == 0) {
                        /**
                         * END OF FILE
                         */
                        $lineCount++;
                        $lines[$lineCount] = $char . $currentLine;

                        $callbackResult = call_user_func($callback, $lines[$lineCount]);

                        if ($lineCount == $linesLimit || $callbackResult === false) {
                            break;
                        }

                    } else {
                        $currentLine = $char . $currentLine;
                    }
                }

            }

            fclose($fh);
        }

        public
        static function getTimestampMicrotime()
        {
            return number_format(
                microtime(true),
                20,
                '',
                ''
            );
        }

        public
        static function getLastFileLines(
            $path,
            $linesCount
        ) {
            $lines = [];

            static::readFileByLinesFromEnd($path, function ($line) use (&$lines) {
                $lines[] = $line;
            }, $linesCount);

            return implode(PHP_EOL, array_reverse($lines));
        }

        public
        static function getFirstFileLines(
            $path,
            $linesCount
        ) {
            $lines = [];

            static::readFileByLines($path, function ($line) use (&$lines) {
                $lines[] = $line;
            }, $linesCount);

            return implode(PHP_EOL, $lines);
        }
    }

}

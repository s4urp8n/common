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
                return dirname($calledFile) . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . \Zver\Common::replaceSlashesToPlatformSlashes($name);
            }

            return static::getCommonPath($calledFile, __DIR__) . 'tests' . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . \Zver\Common::replaceSlashesToPlatformSlashes($name);
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

            return static::getCommonPath($calledFile, __DIR__) . 'files' . DIRECTORY_SEPARATOR . \Zver\Common::replaceSlashesToPlatformSlashes($name);
        }

    }
}

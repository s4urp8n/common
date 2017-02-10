<?php
namespace Zver\Package {

    trait Common
    {

        protected static function getCommonPath($path1, $path2)
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
            if ($calledFileName == 'CommonTest' || $test) {
                return dirname($calledFile) . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . \Zver\Common::replaceSlashesToPlatformSlashes($name);
            }

            return static::getCommonPath($calledFile, __DIR__) . 'tests' . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . \Zver\Common::replaceSlashesToPlatformSlashes($name);
        }

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
            if ($calledFileName == 'CommonTest' || $test) {
                return realpath(dirname($calledFile) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . \Zver\Common::replaceSlashesToPlatformSlashes($name);
            }

            return static::getCommonPath($calledFile, __DIR__) . 'files' . DIRECTORY_SEPARATOR . \Zver\Common::replaceSlashesToPlatformSlashes($name);
        }
    }
}
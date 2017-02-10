<?php
namespace Zver\Package {

    trait Common
    {
        protected static function getPackageTestFilePath($name)
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

        }

        protected static function getPackageFilePath($name)
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
        }
    }
}
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
    }
}

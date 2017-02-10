<?php
namespace TestDir {

    use Zver\Package\Common;

    class TestClass
    {
        use Common;

        public static function gitKeep()
        {
            return static::getPackageFilePath('.gitkeep');
        }

        public static function gitTestKeep()
        {
            return static::getPackageTestFilePath('.gitkeep');
        }
    }
}
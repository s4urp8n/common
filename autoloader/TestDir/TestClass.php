<?php
namespace TestDir {

    use Zver\Common;

    class TestClass
    {
        public static function gitKeep()
        {
            return Common::getPackageFilePath('.gitkeep');
        }

        public static function gitTestKeep()
        {
            return Common::getPackageTestFilePath('.gitkeep');
        }
    }
}
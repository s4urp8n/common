<?php

use Zver\Common;

class TestClass2
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
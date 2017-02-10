<?php

class TestClass2
{
    public static function gitKeep()
    {
        return \Zver\Common::getPackageFilePath('.gitkeep');
    }

    public static function gitTestKeep()
    {
        return \Zver\Common::getPackageTestFilePath('.gitkeep');
    }
}
<?php

namespace Zver\Package {

    use Zver\Common;

    trait Helper
    {

        public static function getPackageDir($path = '')
        {
            $directory = realpath(implode(
                                      DIRECTORY_SEPARATOR,
                                      [
                                          __DIR__,
                                          '..',
                                          '..',
                                          '..',
                                      ])) . DIRECTORY_SEPARATOR;

            $parts = array_values(array_filter(explode(DIRECTORY_SEPARATOR, $directory)));

            $partsCount = count($parts);

            if (array_key_exists($partsCount - 3, $parts) && $parts[$partsCount - 3] == 'vendor') {
                unset($parts[$partsCount - 3]);
                unset($parts[$partsCount - 2]);
                unset($parts[$partsCount - 1]);

                $directory = implode(DIRECTORY_SEPARATOR, $parts) . DIRECTORY_SEPARATOR;
            }

            return $directory . Common::stripBeginningSlashes(Common::replaceSlashesToPlatformSlashes($path));
        }

        public function foreachTrue(array $values)
        {
            foreach ($values as $value) {
                $this->assertTrue($value);
            }
        }

        public function foreachFalse(array $values)
        {
            foreach ($values as $value) {
                $this->assertFalse($value);
            }
        }

        public function foreachSame(array $values)
        {
            foreach ($values as $value) {
                $this->assertSame($value[0], $value[1]);
            }
        }

        public function foreachNotSame(array $values)
        {
            foreach ($values as $value) {
                $this->assertNotSame($value[0], $value[1]);
            }
        }

    }
}

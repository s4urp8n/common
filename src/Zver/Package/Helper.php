<?php

namespace Zver\Package {

    use Zver\Common;

    trait Helper
    {

        public static function getPackagePath($path = '')
        {
            $directory = realpath(implode(
                                      DIRECTORY_SEPARATOR,
                                      [
                                          __DIR__,
                                          '..',
                                          '..',
                                          '..',
                                      ])) . DIRECTORY_SEPARATOR;

            $sep = preg_quote(DIRECTORY_SEPARATOR);

            if (preg_match("#{$sep}vendor{$sep}[^{$sep}]+{$sep}[^{$sep}]+{$sep}$#i", $directory) === 1) {
                $directory = realpath($directory . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..') . DIRECTORY_SEPARATOR;
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

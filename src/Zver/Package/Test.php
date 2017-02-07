<?php

namespace Zver\Package {

    trait Test
    {

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

        /**
         * @param $name Name of file in files folder
         *
         * @return string Full filename of package file
         */
        public function packageFile($name)
        {
            return realpath(__DIR__ . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, [
                                '..',
                                '..',
                                '..',
                                'files',
                                $name,
                            ]));
        }

        /**
         * @param $name Name of file in files folder
         *
         * @return string Full filename of package test file
         */
        public function packageTestFile($name)
        {
            return realpath(__DIR__ . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, [
                                '..',
                                '..',
                                '..',
                                'tests',
                                'files',
                                $name,
                            ]));
        }

    }
}

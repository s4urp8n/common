<?php

namespace Package {

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

        public function foreachEquals(array $values)
        {
            foreach ($values as $value) {
                $this->assertEquals($value[0], $value[1]);
            }
        }

        public function foreachNotEquals(array $values)
        {
            foreach ($values as $value) {
                $this->assertNotEquals($value[0], $value[1]);
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
<?php

class CommonTest extends PHPUnit\Framework\TestCase
{

    use Package\Test;

    public function testDefaultEncoding()
    {
        $this->foreachSame(
            [
                [\Zver\Common::getDefaultEncoding(), 'UTF-8'],
            ]
        );
    }

}
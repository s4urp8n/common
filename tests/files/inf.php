<?php

if (!getenv('phpunit')) {
    while (true) {
        echo getmypid() . "\n";
        sleep(1);
    }
}

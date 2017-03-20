<?php

file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . 'sync.txt', getmypid());

while (true) {
    sleep(1);
    echo "+";
}

<?php
sleep(5);
file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . 'sync.txt', 1, LOCK_EX);

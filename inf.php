<?php

file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . 'sync.txt', getmypid());
sleep(60);
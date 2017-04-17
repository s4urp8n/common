<?php

$i = 0;
while (true) {
    sleep(1);
    echo "+";
    $i++;
    if ($i >= 10) {
        break;
    }
}

exit(0);

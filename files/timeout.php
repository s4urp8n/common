<?php

echo "PID=" . getmypid() . "\n";

echo shell_exec($argv[1]);
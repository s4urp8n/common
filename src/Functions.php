<?php

function array_chunk_offset(array $array, $chunkSize)
{
    if (count($array) <= $chunkSize) {
        return [$array];
    }
    $chunks = [];
    $offset = 0;
    while (true) {
        $current = array_slice($array, $offset++, $chunkSize);
        if (count($current) == $chunkSize) {
            $chunks[] = $current;
        } else {
            break;
        }
    }
    return $chunks;
}
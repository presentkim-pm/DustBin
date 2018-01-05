<?php

namespace presentkim\dustbin\util;

/**
 * @param Object[] $list
 *
 * @return string[]
 */
function listToPairs(array $list) : array{
    $pairs = [];
    $size = sizeOf($list);
    for ($i = 0; $i < $size; ++$i) {
        $pairs["{%$i}"] = $list[$i];
    }
    return $pairs;
}
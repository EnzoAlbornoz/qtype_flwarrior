<?php

defined('MOODLE_INTERNAL') || die();


function flu_array_lambda_find_index(array $array, Closure $fn): int {
    for ($idx = 0; $idx < count($array); $idx++) {
        if ($fn($array[$idx], $idx, $array)) {
            return $idx;
        }
    }
    return -1;
}

function flu_object_from_entries(array $entries): object {
    return array_reduce($entries, function ($agg, $el) {
        $agg->{$el[0]} = $el[1];
        return $agg;
    }, new stdClass());
}

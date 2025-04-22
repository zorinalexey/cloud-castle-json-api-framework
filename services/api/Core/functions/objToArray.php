<?php

/**
 * @param object $obj
 * @return array
 */
function objToArray(object $obj) : array
{
    return json_decode(json_encode($obj), true);
}
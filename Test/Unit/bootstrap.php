<?php
/**
 * User: Andreas Penz <office@dopa.media>
 * Date: 10.05.17
 */

require_once realpath(__DIR__ . '/../../vendor/autoload.php');

/**
 * @SuppressWarnings(PHPMD.ShortMethodName)
 */
function __()
{
    return $argc = func_get_args();
}
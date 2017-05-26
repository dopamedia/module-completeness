<?php
/**
 * User: Andreas Penz <office@dopa.media>
 * Date: 10.05.17
 */

require_once realpath(__DIR__ . '/../../vendor/autoload.php');

/**
 * Create value-object \Magento\Framework\Phrase
 *
 * @return \Magento\Framework\Phrase
 */
function __()
{
    $argc = func_get_args();

    $text = array_shift($argc);
    if (!empty($argc) && is_array($argc[0])) {
        $argc = $argc[0];
    }

    return new \Magento\Framework\Phrase($text, $argc);
}
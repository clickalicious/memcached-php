<?php

/**
 * The base path to /Lib/ if we don't have Composer we need to know root path
 */
define(
    'CLICKALICIOUS_MEMCACHED_BASE_PATH',
    realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR
);

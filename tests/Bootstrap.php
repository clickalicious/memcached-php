<?php

/**
 * The base path to /Lib/ if we don't have Composer we need to know root path
 */
define(
    'MEMCACHED_BASE_PATH',
    realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Lib') . DIRECTORY_SEPARATOR
);

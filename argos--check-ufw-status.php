<?php
/**
 * .
 * 
 * @for use with Argos, Bitbar, Infinite Menu Bar, ect
 * 
 * @link https://github.com/p-e-w/argos
 * @link https://github.com/matryer/xbar-plugins
 * @link https://apps.apple.com/us/app/infinite-menu-bar/id1439179659?mt=12 
 */

require('_helpers.php');

echo sanitize( file_get_contents( __DIR__ . '/cache/fire-wall-status.txt' ), true );
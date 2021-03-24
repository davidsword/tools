#!/usr/bin/env php
<?php
/**
 * Fuzzy search for Espanso matches
 * 
 * Helps when forgetting the trigger (which happens to me all the time)
 * 
 * @see espanso.org
 * 
 * usage searching for a match that has `backup` in it: 
 *   `$ esp backup`
 * 
 * @for PATH
 */

require '_helpers.php';
require 'vendor/autoload.php';

define('ESP_SHOW_LIMIT', 2);
define('ESP_SHOW_CHARS', 999);
define('ESP_THRESHOLD', 0.3); //@see https://github.com/loilo/Fuse#options

//@TODO should be a helper library for cli output instead.
define('CLI_NORMAL', "\e[0m");
define('CLI_DIM', "\e[2m");

$query = sanitize($_SERVER['argv'][1]) ?? die("❌ a query is needed\n"); 

$cmd = '/usr/bin/espanso match list -j';
$espanso_list = shell_exec( $cmd );

if ( ! $espanso_list || empty( $espanso_list ) )
    die("❌ unable to run `{$cmd}`\n");

$matches = json_decode( $espanso_list, JSON_OBJECT_AS_ARRAY );

// @TODO probably can just edit keys of Fuse instead of creating a whole new array
$for_fuse = [];
foreach ( $matches as $match ) {
    $for_fuse[] = [
        "trigger" => $match['triggers'][0], //@TODO multiple trigger support
        "replace" => $match['replace']
    ];
}

$fuse = new \Fuse\Fuse($for_fuse, [
    "keys" => [ "trigger", "replace" ],
    "threshold" => ESP_THRESHOLD
]);
  
$results = $fuse->search( $query );

$i = 0;
foreach ( $results as $result ) {
    if ( $i == ESP_SHOW_LIMIT) 
        break;
    echo CLI_NORMAL."{$result['trigger']}\n";
    $replace = truncate( $result['replace'], ESP_SHOW_CHARS );
    $indented_txt = str_replace("\n","\n",$replace);
    echo CLI_DIM.$indented_txt."\n\n";
    $i++;
}
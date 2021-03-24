#!/usr/bin/env php
<?php
/**
 * Convert UTC to PT. Convert PT to UTC
 * 
 * Called to in shell with an arg from `/usr/local/bin/`
 * 
 * usage: 
 *   `$ utc 12`
 *   `$ pt 12:30`
 * 
 * @for PATH
 */

define('MAIN_TIMEZONE', 'PT');

$timezones = [
	'UTC' => 'UTC',
	'PT'  => 'America/Vancouver',
//	'ET'  => 'America/Toronto',
];

// UTC hour. Can be any format, but it has to be in 24 hour format, no am/pm.
// eg: "2", "4:20", "5", "06", "17:21", "23".
$query = $_SERVER['argv'][1] ?? die("❌ add a time\n"); 

// @todo handle if the incoming command isn't for UTC to PT, instead PT to UTC
$invert = $_SERVER['argv'][2] ?? false;

$local = isset($invert) && $invert ? MAIN_TIMEZONE : 'UTC' ;
date_default_timezone_set( $timezones[$local] );

// load library to handle the view.
// if just hour passed.
if ( ! strstr( $query, ':' ) ) {
	$query = $query . ':00:00';
}

// if passed hour and still typing the minutes, finish the string so it doesn't flicker.
if ( ':' == substr( $query, -1 ) ) {
	$query = $query . '00:00';
}

// build the full date in UTC.
$query_date = date( "Y-m-d {$query}" );
$dt         = new DateTime( $query_date, new DateTimeZone( $timezones[$local]  ) );

foreach ( $timezones as $key => $tz ) {
	if ( $key == $local)
		continue; // bail

	// convert to local timezone.
	$dt->setTimezone( new DateTimeZone( $tz ) );

	echo "{$dt->format( 'H:i' )} {$key}\n";
}

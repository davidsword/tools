<?php
/**
 * Get the state of Home Assistants Busy Light entity.
 * 
 * Output just the emoji, ie "🔴". 
 * 
 * @for use with Argos, Bitbar, Infinite Menu Bar, ect
 * 
 * @link https://github.com/p-e-w/argos
 * @link https://github.com/matryer/xbar-plugins
 * @link https://apps.apple.com/us/app/infinite-menu-bar/id1439179659?mt=12 
 */

require('_config.php'); // constants.
require('_helpers.php');

echo sanitize( str_replace("\n",'',explode(" ",get_busy_light_ha_status()->state)[0]), true );

/**
 * https://developers.home-assistant.io/docs/api/rest/
 */
function get_busy_light_ha_status( ) {
	$ch = curl_init();
	$url = HOME_ASSISTANT_HOST_URL.'/api/states/'.HOME_ASSISTANT_BUSYLIGHT_ENTITY_FRIENDLY; // from _config.php
	curl_setopt( $ch, CURLOPT_URL, $url );
	curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 2 );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt(
		$ch,
		CURLOPT_HTTPHEADER,
		array(
			'Authorization: Bearer ' . HOME_ASSISTANT_TOKEN, // from _config.php
			'Content-type: application/json; charset=utf-8',
		)
	);
	$buffer = curl_exec( $ch );
	if ( $buffer === false )
		die( curl_error($ch) );
	curl_close( $ch );
	return json_decode($buffer);
}
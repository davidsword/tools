<?php
/**
 * Get the state of Home Assistants Busy Light entity.
 * 
 * Output just the emoji. For use in Argos top bar.
 */
require('_config.php');

echo sanitize( str_replace("\n",'',explode(" ",get_busy_light_ha_status()->state)[0]) );

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

/**
 * keep it simple, only normal chars
 *
 * @param string $dirty
 * @return string sanitized string
 */
function sanitize( $dirty) {
	return filter_var( htmlspecialchars( strip_tags($dirty) ), FILTER_SANITIZE_STRING );
}
<?php
/**
 * Get the state of Home Assistants weather.
 * 
 * @for use with Argos, Bitbar, Infinite Menu Bar, ect
 * 
 * @see https://developers.home-assistant.io/docs/api/rest/
 * 
 * @link https://github.com/p-e-w/argos
 * @link https://github.com/matryer/xbar-plugins
 * @link https://apps.apple.com/us/app/infinite-menu-bar/id1439179659?mt=12 
 */

require '_config.php'; // constants.
require '_helpers.php';

// @TODO check data before outputting for cleaner errors

echo get_outside_temp() . ' ( '. get_outside_airquality() .'pm2.5 ) / ' . get_inside_temp();

function get_outside_temp() {
	$outside = get_homeassistant_state( HOME_ASSISTANT_OUTSIDE_TEMP );
	return temp( $outside->state );
}

function get_outside_airquality() {
	$outside_air = get_homeassistant_state( HOME_ASSISTANT_OUTSIDE_AIR );
	return intval( $outside_air->state );
}

function get_inside_temp() {
	$inside = get_homeassistant_state( HOME_ASSISTANT_OFFICE_TEMP );
	return temp( $inside->state );
}

function get_homeassistant_state( $entity ) {
	return get_rest_response( HOME_ASSISTANT_HOST_URL . '/api/states/' . $entity, HOME_ASSISTANT_TOKEN );
}

function temp( $int ) {
	return intval( round( $int ) ). '°';
}
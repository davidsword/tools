<?php
/**
 * Get the state of Home Assistants weather.
 * 
 * @for use with Argos, Bitbar, Infinite Menu Bar, ect
 * 
 * @link https://github.com/p-e-w/argos
 * @link https://github.com/matryer/xbar-plugins
 * @link https://apps.apple.com/us/app/infinite-menu-bar/id1439179659?mt=12 
 */

require '_config.php'; // constants.
require '_helpers.php';

echo get_outside_temp() . ' ( '. get_outside_airquality() .' ) / ' . get_inside_temp();

function get_outside_temp() {
	// via https://www.mathworks.com/help/thingspeak/rest-api.html
	$outside = get_rest_response(HOME_ASSISTANT_HOST_URL.'/api/states/'.HOME_ASSISTANT_OUTSIDE_TEMP, HOME_ASSISTANT_TOKEN);

	// @TODO check response before outputting
	return temp($outside->state);
}

function get_outside_airquality() {
	// via https://www.mathworks.com/help/thingspeak/rest-api.html
	$outside_air = get_rest_response(HOME_ASSISTANT_HOST_URL.'/api/states/'.HOME_ASSISTANT_OUTSIDE_AIR, HOME_ASSISTANT_TOKEN);

	// @TODO check response before outputting
	return intval($outside_air->state);
}

function get_inside_temp() {
	// via https://developers.home-assistant.io/docs/api/rest/
	$inside = get_rest_response(HOME_ASSISTANT_HOST_URL.'/api/states/'.HOME_ASSISTANT_OFFICE_TEMP, HOME_ASSISTANT_TOKEN);
	return temp($inside->state);
}

function temp( $int ) {
	return intval(round($int)). '°';
}
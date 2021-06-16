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

require('_config.php'); // constants.
require('_helpers.php');

// https://developers.home-assistant.io/docs/api/rest/
$weather = get_rest_response( HOME_ASSISTANT_HOST_URL.'/api/states/'.HOME_ASSISTANT_WEATHER, HOME_ASSISTANT_TOKEN );
$office = get_rest_response( HOME_ASSISTANT_HOST_URL.'/api/states/'.HOME_ASSISTANT_OFFICE_TEMP, HOME_ASSISTANT_TOKEN);

$emojis = [
    // https://www.piliapp.com/symbol/
    'cloudy'        => '☁',
    'partlycloudy'  => '🌥',
    'sunny'         => '☀',
    'clear-night'   => '☾',
];

$emoji = $emojis[$weather->state] ?? $weather->state;

//print_r($office);die;

echo $emoji.' ' . intval( $weather->attributes->temperature ) . '°'; // / ' . intval(round($office->state)). '°'
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

// https://developers.home-assistant.io/docs/api/rest/
$status = get_rest_response( HOME_ASSISTANT_HOST_URL.'/api/states/'.HOME_ASSISTANT_BUSYLIGHT_ENTITY_FRIENDLY, HOME_ASSISTANT_TOKEN );
echo sanitize( str_replace("\n",'',explode(" ",$status->state)[0]), true );
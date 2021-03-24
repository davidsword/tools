#!/usr/bin/env php
<?php
/**
 * Change Slack status and Home Assistant Busy Light with command, ie
 * 
 * Called to in shell with an arg from `/usr/local/bin/`
 * 
 * usage: 
 *   `$ ss busy`
 *   `$ ss active`
 *   `$ ss meeting`
 * 
 * @for PATH
 */

require '_config.php';
require '_helpers.php';

$query = $_SERVER['argv'][1] ?? die("error: add a status\n"); 

define( 'SS_STATUSES_FILE', 'ss-statuses.json' );

$statuses = get_statuses();

foreach ( $statuses as $status ) {
	if ( trim( strtolower( $query ) ) === trim( strtolower( $status['title'] ) ) ) {
		$match_found = true;
		break;
	}
}

if ( ! $query || ! isset( $match_found ) ) {
	echo "❌ incorrect input query, use: \n";
	foreach ( $statuses as $status )
		echo " - ".trim( strtolower( $status['title'] ) )."\n";
	die;
}

$text     = sanitize( $status['text'] );
$title    = sanitize( $status['title'] );
$emoji    = str_replace( ':', '', sanitize( $status['emoji'] ) );
$presence = ( strstr( $status['presence'], 'way' ) ) ? 'away' : 'auto';

$emoji = $emoji ? ":{$emoji}:" : '';

$data = [
	'profile' => [
		'status_text'       => $text,
		'status_emoji'      => $emoji,
		'status_expiration' => 0,
	],
];

// https://api.slack.com/methods/users.profile.set
$url = 'https://slack.com/api/users.profile.set';
$body = json_encode( $data );
curl_payload( $url, $body, get_slack_token() );

// https://api.slack.com/methods/users.setPresence
$url = 'https://slack.com/api/users.setPresence';
$body = json_encode( [ 'presence' => $presence ] );
curl_payload( $url, $body, get_slack_token() );

// https://developers.home-assistant.io/docs/api/rest/
$url = HOME_ASSISTANT_HOST_URL . '/api/states/'. HOME_ASSISTANT_BUSYLIGHT_ENTITY;
$body = json_encode( [ 'state' => ucfirst( $status['busy-light'] ) ] );
curl_payload( $url, $body, HOME_ASSISTANT_TOKEN );

$light = [
	'available' => '🟢',
	'away'      => '🟡',
	'busy'      => '🔴',
	'offline'   => '⚪',
];

$presence = 'auto' === $presence ? 'Active' : ucfirst( $presence );
$slack_status = ( 'Active' === $title ) ? '' : ": {$title}";
echo "{$light[$status['busy-light']]} {$presence} | {$text} \n";

// HELPERS ---

function get_statuses() {
	$get_statuses = file_get_contents( __DIR__ . '/' . SS_STATUSES_FILE );
	return json_decode( $get_statuses, JSON_OBJECT_AS_ARRAY );
}

function search_keyword_in_string( $needle, $haystack ) {
	return strstr( strtolower( $haystack ), strtolower( $needle ) );
}

function get_slack_token() {
	if ( defined( 'SLACKTOKEN' ) ) {
		return SLACKTOKEN;
	}
	return 'no-token';
}

function curl_payload( $url, $body, $token ) {
	$ch = curl_init();
	curl_setopt( $ch, CURLOPT_URL, $url );
	curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 2 );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt( $ch, CURLOPT_POSTFIELDS, $body );
	curl_setopt(
		$ch,
		CURLOPT_HTTPHEADER,
		array(
			'Authorization: Bearer ' . $token,
			'Content-type: application/json; charset=utf-8',
		) 
	);
	$buffer = curl_exec( $ch );
	if ( $buffer === false ) {
		echo 'Curl error to ' . $url . ' : ' . curl_error($ch);
	}
	curl_close( $ch );
}
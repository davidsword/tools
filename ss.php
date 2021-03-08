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
 */

require '_config.php';

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
	foreach ( $statuses as $status ) {
		echo " - ".trim( strtolower( $status['title'] ) )."\n";
	}
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

$data = json_encode( $data );

// CUSTOM STATUS
$ch = curl_init();
curl_setopt( $ch, CURLOPT_URL, 'https://slack.com/api/users.profile.set' );
curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 2 );
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
curl_setopt( $ch, CURLOPT_POSTFIELDS, $data ); 
curl_setopt(
	$ch,
	CURLOPT_HTTPHEADER,
	array(
		'Authorization: Bearer ' . get_slack_token(),
		'Content-type: application/json; charset=utf-8',
	) 
);
$buffer = curl_exec( $ch );
curl_close( $ch );

$body = json_decode( $buffer );

if ( ! $body->ok ) {
	echo print_r( $body );
	echo 'Error ' . $body->error;
}

// https://api.slack.com/methods/users.setPresence
$ch = curl_init();
curl_setopt( $ch, CURLOPT_URL, 'https://slack.com/api/users.setPresence' );
curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 2 );
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( [ 'presence' => $presence ] ) ); 
curl_setopt(
	$ch,
	CURLOPT_HTTPHEADER,
	array(
		'Authorization: Bearer ' . get_slack_token(),
		'Content-type: application/json; charset=utf-8',
	) 
);
$buffer = curl_exec( $ch );
if ( $buffer === false ) {
	echo 'Slack Curl error: ' . curl_error($ch);
}
curl_close( $ch );

change_home_assistant_busy_light_state_to( $status['busy-light'] );

$light = [
	'available' => '🟢',
	'away'      => '🟡',
	'busy'      => '🔴',
	'offline'   => '⚪',
];

$presence = 'auto' === $presence ? 'Active' : ucfirst( $presence );
$slack_status = ( 'Active' === $title ) ? '' : ": {$title}";
echo "{$light[$status['busy-light']]} | {$presence} \n";


// HELPERS ---

function get_statuses() {
	$get_statuses = file_get_contents( __DIR__ . '/' . SS_STATUSES_FILE );
	return json_decode( $get_statuses, JSON_OBJECT_AS_ARRAY );
}

function search_keyword_in_string( $needle, $haystack ) {
	return strstr( strtolower( $haystack ), strtolower( $needle ) );
}

function sanitize( $v ) {
	return preg_replace( '/[^a-zA-Z0-9 _\-]/', '', $v );
}

function get_slack_token() {
	if ( defined( 'SLACKTOKEN' ) ) {
		return SLACKTOKEN;
	}
	return 'no-token';
}

function change_home_assistant_busy_light_state_to( $new_status ) {
	// Tell `Home Assistant`
	// https://developers.home-assistant.io/docs/api/rest/
	$ch = curl_init();
	curl_setopt( $ch, CURLOPT_URL, HOME_ASSISTANT_HOST_URL . '/api/states/'. HOME_ASSISTANT_BUSYLIGHT_ENTITY );
	curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 2 );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( [ 'state' => ucfirst( $new_status ) ] ) );
	curl_setopt(
		$ch,
		CURLOPT_HTTPHEADER,
		array(
			'Authorization: Bearer ' . HOME_ASSISTANT_TOKEN,
			'Content-type: application/json; charset=utf-8',
		) 
	);
	$buffer = curl_exec( $ch );
	if ( $buffer === false ) {
		echo 'HA Curl error: ' . curl_error($ch);
	}
	curl_close( $ch );
}

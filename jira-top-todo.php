<?php
/**
 * Jira top IN PROGRESS Task.
 *
 * results in string output of:
 * `JIRA-123 Task Name`
 * 
 * For use with Argos 
 * @see https://github.com/p-e-w/argos
 */
require __DIR__.'/_config.php';

echo truncate( jira_get_top_issue(), 50 );

/**
 * Curl JIRA for high priority in IN PROGRESS colunm.
 *
 * 1 = Highest
 * 2 = High
 * 3 = Medium
 * 4 = Low
 * 5 = Lowest
 *
 * @return string task name of highest priority task
 */
function jira_get_top_issue() {
	$jira_response = json_decode( curl_jira() );
	$output        = '';
	$priority      = false;
	foreach ( $jira_response->issues as $issue ) {
		$is_higher_priority = intval( $issue->fields->priority->id ) < $priority;
		if ( ! $priority || $is_higher_priority ) {
			$priority = (int) $issue->fields->priority->id;
			$output   = $issue->key . ' ' . $issue->fields->summary;
		}
	}
	if ( empty( $output ) ) {
		$output = 'no tasks found';
	}
	return sanitize( $output );
}

/**@TODO
 * Send request to JIRA using the constants.
 *
 * @return string json result from JIRA API, as-is.
 */
function curl_jira() {
	$jql = 'assignee = "' . JIRA_ASSIGNEE . '" AND status = "In Progress"';
	$url = 'https://vipjira.atlassian.net/rest/agile/latest/board/' . JIRA_BOARD . '/issue?jql=' . rawurlencode( $jql );
	$ch  = curl_init();
	curl_setopt( $ch, CURLOPT_URL, $url );
	curl_setopt( $ch, CURLOPT_USERPWD, JIRA_USER . ':' . JIRA_TOKEN );
	curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 2 );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
	$buffer = curl_exec( $ch );
	if ( $buffer === false ) {
		$buffer = '❌ Curl error: ' . curl_error( $ch );
	}
	curl_close( $ch );
	return $buffer;
}

function sanitize( $v ) {
	return preg_replace( '/[^a-zA-Z0-9 _\-]/', '', $v );
}

/**
 * Cut string to a set length to shorten task names
 *
 * @param string $string in full
 * @return string $string at maybe a shorter length
 */
function truncate( $string, $length = 30 ) {
	if ( strlen( $string ) > $length ) {
		$string    = strip_tags( $string );
		$first     = substr_replace( $string, '', ( floor( $length ) ), strlen( $string ) );
		$newstring = $first . '...';
		return ( strlen( $newstring ) > ( strlen( $string ) ) ) ? $string : $newstring;
	} else {
		return $string;
	}
}
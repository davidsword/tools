<?php

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

function sanitize( $v, $preserve_emojis = false ) {
	if ( $preserve_emojis ) {
		return filter_var( htmlspecialchars( strip_tags($v) ), FILTER_SANITIZE_STRING );
	}
	return preg_replace( '/[^a-zA-Z0-9 _\-]/', '', $v );
}
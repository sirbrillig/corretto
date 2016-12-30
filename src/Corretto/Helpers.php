<?php
namespace Corretto;

class Helpers {
	public function recursiveAssocSort( $elements ) {
		if ( ! is_array( $elements ) ) {
			return $elements;
		}
		$elements = array_map( function( $el ) {
			return $this->recursiveAssocSort( $el );
		}, $elements );
		if ( ! $this->hasStringKeys( $elements ) ) {
			return $elements;
		}
		sort( $elements );
		return $elements;
	}

	public function hasStringKeys( array $array ) {
		return count( array_filter( array_keys( $array ), 'is_string' ) ) > 0;
	}
}

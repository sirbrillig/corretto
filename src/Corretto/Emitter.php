<?php
namespace Corretto;

class Emitter {
	private $handlers = [];

	public function on( $key, callable $handler ) {
		if ( ! isset( $this->handlers[ $key ] ) ) {
			$this->handlers[ $key ] = [];
		}
		$this->handlers[ $key ][] = $handler;
	}

	protected function emit( $key, $data = null ) {
		debug( 'emitting', $key );
		if ( isset( $this->handlers[ $key ] ) ) {
			array_map( function( $handler ) use ( $data ) {
				$handler( $data );
			}, $this->handlers[ $key ] );
		}
	}
}

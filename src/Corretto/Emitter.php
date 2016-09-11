<?php
namespace Corretto;

class Emitter {
	private $handlers = [];

	public function on( string $key, callable $handler ) {
		if ( ! isset( $this->handlers[ $key ] ) ) {
			$this->handlers[ $key ] = [];
		}
		$this->handlers[ $key ][] = $handler;
	}

	protected function emit( string $key, $data = null ) {
		if ( isset( $this->handlers[ $key ] ) ) {
			array_map( function( $handler ) use ( $data ) {
				$handler( $data );
			}, $this->handlers[ $key ] );
		}
	}
}

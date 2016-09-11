<?php
namespace Corretto;

class Description {
	private $descriptions = [];
	private $tests = [];
	private $callable = null;
	private $name = '';
	private $handlers = [];

	public $parent;

	public function __construct( $name, $callable ) {
		$this->name = $name;
		$this->callable = $callable;
	}

	public function addDescription( Description $description ) {
		$description->parent = $this;
		$this->descriptions[] = $description;
	}

	public function addTest( Test $test ) {
		$test->parent = $this;
		$this->tests[] = $test;
	}

	public function getName() {
		return $this->parent ? $this->parent->getName() . ' ' . $this->name : $this->name;
	}

	public function on( string $key, callable $handler ) {
		if ( ! isset( $this->handlers[ $key ] ) ) {
			$this->handlers[ $key ] = [];
		}
		$this->handlers[ $key ][] = $handler;
	}

	protected function emit( string $key, $data ) {
		if ( isset( $this->handlers[ $key ] ) ) {
			array_map( function( $handler ) use ( $data ) {
				$handler( $data );
			}, $this->handlers[ $key ] );
		}
	}

	public function doForAllTests( callable $action ) {
		$this->emit( 'startDescription', $this );
		( $this->callable )();
		// TODO: move this echo to a reporter
		echo $this->name . "\n";
		array_map( $action, $this->tests );
		$runDescription = function( Description $description ) use ( $action ) {
			$description->doForAllTests( $action );
		};
		array_map( $runDescription, $this->descriptions );
		$this->emit( 'endDescription', $this );
	}
}


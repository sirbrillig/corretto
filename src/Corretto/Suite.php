<?php
namespace Corretto;

class Suite {
	private $suites = [];
	private $tests = [];
	private $callable = null;
	private $name = '';
	private $handlers = [];

	public $parent;

	public function __construct( $name, $callable ) {
		$this->name = $name;
		$this->callable = $callable;
	}

	public function addSuite( Suite $suite ) {
		$suite->parent = $this;
		$this->suites[] = $suite;
	}

	public function addTest( Test $test ) {
		$test->parent = $this;
		$this->tests[] = $test;
	}

	public function getName() {
		return $this->name;
	}

	public function getFullName() {
		return $this->parent ? $this->parent->getFullName() . ' ' . $this->name : $this->name;
	}

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

	public function addFailure( Failure $failure ) {
		$this->parent && $this->parent->addFailure( $failure );
	}

	public function doForAllTests( callable $action ) {
		$this->emit( 'startSuite', $this );
		( $this->callable )();
		array_map( $action, $this->tests );
		$runSuite = function( Suite $suite ) use ( $action ) {
			$suite->doForAllTests( $action );
		};
		array_map( $runSuite, $this->suites );
		$this->emit( 'endSuite', $this );
	}
}


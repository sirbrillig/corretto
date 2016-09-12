<?php
namespace Corretto;

class Suite extends Emitter {
	private $suites = [];
	private $tests = [];
	private $callable = null;
	private $name = '';

	public $parent;

	// TODO: add before/after
	public function __construct( string $name, callable $callable = null ) {
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

	public function doForAllTests( callable $action ) {
		$this->emit( 'suite-start', $this );
		$this->callable && ( $this->callable )( $this );
		array_map( $action, $this->tests );
		$runSuite = function( Suite $suite ) use ( $action ) {
			$suite->doForAllTests( $action );
		};
		array_map( $runSuite, $this->suites );
		$this->emit( 'suite-end', $this );
	}
}


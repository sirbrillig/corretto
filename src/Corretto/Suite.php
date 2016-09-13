<?php
namespace Corretto;

class Suite extends Emitter {
	private $suites = [];
	private $tests = [];
	private $callable = null;
	private $name = '';

	public $parent;
	public $skip;
	public $beforeEach;
	public $afterEach;
	public $before;
	public $after;
	public $context;
	public $grep;

	public function __construct( string $name, callable $callable = null ) {
		$this->name = $name;
		$this->callable = $callable;
		$this->context = new \StdClass();
	}

	public function addSuite( Suite $suite ) {
		$suite->parent = $this;
		$suite->grep = $this->grep;
		$this->suites[] = $suite;
	}

	public function addTest( Test $test ) {
		$test->parent = $this;
		if ( $this->grep && ! preg_match( '/' . $this->grep . '/', $test->getFullName() ) ) {
			return;
		}
		$this->tests[] = $test;
	}

	public function getTests() {
		return $this->tests;
	}

	public function getSuites() {
		return $this->suites;
	}

	public function getName() {
		return $this->name;
	}

	public function getFullName() {
		return $this->parent ? $this->parent->getFullName() . ' ' . $this->name : $this->name;
	}

	public function prepareSuite() {
		if ( ! $this->callable ) {
			return;
		}
		$this->emit( 'suite-prepare-start', $this );
		( $this->callable )( $this );
		$prepare = function( Suite $suite ) {
			$suite->prepareSuite();
		};
		array_map( $prepare, $this->suites );
		$this->emit( 'suite-prepare-end', $this );
	}

	public function getTestCount() {
		return count( $this->tests );
	}

	public function getDeepTestCount() {
		$count = $this->getTestCount();
		$addToCount = function( $suite ) use ( &$count ) {
			$count += $suite->getDeepTestCount();
		};
		array_map( $addToCount, $this->getSuites() );
		return $count;
	}
}


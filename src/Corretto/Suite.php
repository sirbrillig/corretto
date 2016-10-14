<?php
namespace Corretto;

class Suite {
	protected $suites = [];
	protected $tests = [];
	protected $callable = null;
	protected $name = '';
	protected $context;

	public $parent;
	public $skip;
	public $beforeEach;
	public $afterEach;
	public $before;
	public $after;

	public function __construct( string $name = '', callable $callable = null ) {
		$this->name = $name;
		$this->callable = $callable;
		$this->context = new \StdClass();
	}

	public function getContext() {
		if ( $this->parent ) {
			return $this->parent->getContext();
		}
		return $this->context;
	}

	public function addSuite( Suite $suite ) {
		$suite->parent = $this;
		$this->suites[] = $suite;
		$suite->prepareSuite();
	}

	public function addTest( Test $test ) {
		$test->parent = $this;
		$this->tests[] = $test;
	}

	public function getTests( $matching = null ) {
		$doesTestMatch = function( $test ) use ( &$matching ) {
			return $test->doesTestMatch( $matching );
		};
		return array_filter( $this->tests, $doesTestMatch );
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
		( $this->callable )( $this );
	}

	public function getTestCount( $matching = null ) {
		return count( $this->getTests( $matching ) );
	}

	public function getDeepTestCount( $matching = null ) {
		$count = $this->getTestCount( $matching );
		$addToCount = function( $suite ) use ( &$count, &$matching ) {
			$count += $suite->getDeepTestCount( $matching );
		};
		array_map( $addToCount, $this->getSuites() );
		return $count;
	}

	public function callBeforeEach() {
		if ( $this->parent ) {
			$this->parent->callBeforeEach();
		}
		if ( isset( $this->beforeEach ) ) {
			( $this->beforeEach )( $this->getContext() );
		}
	}

	public function callAfterEach() {
		if ( isset( $this->afterEach ) ) {
			( $this->afterEach )( $this->getContext() );
		}
		if ( $this->parent ) {
			$this->parent->callAfterEach();
		}
	}
}


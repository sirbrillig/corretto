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

	public function __construct( $name = '', callable $callable = null ) {
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

	public function getTests( $grep = null, $filter = null, $only = null ) {
		$doesTestMatch = function( $test ) use ( &$grep, &$filter, &$only ) {
			if ( isset( $only ) && $test->getFullName() !== $only ) {
				return false;
			}
			if ( isset( $filter ) && ! $test->doesTestMatchString( $filter ) ) {
				return false;
			}
			return $test->doesTestMatchPattern( $grep );
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
		$parentName = $this->parent ? $this->parent->getFullName() : '';
		return $parentName ? $parentName . ' ' . $this->name : $this->name;
	}

	public function prepareSuite() {
		if ( ! $this->callable ) {
			return;
		}
		call_user_func( $this->callable, $this );
	}

	public function getTestCount( $grep = null, $filter = null, $only = null ) {
		return count( $this->getTests( $grep, $filter, $only ) );
	}

	public function getDeepTestCount( $grep = null, $filter = null, $only = null ) {
		$count = $this->getTestCount( $grep, $filter, $only );
		$addToCount = function( $suite ) use ( &$count, &$grep, &$filter, &$only ) {
			$count += $suite->getDeepTestCount( $grep, $filter, $only );
		};
		array_map( $addToCount, $this->getSuites() );
		return $count;
	}

	public function callBeforeEach() {
		if ( $this->parent ) {
			$this->parent->callBeforeEach();
		}
		if ( isset( $this->beforeEach ) ) {
			call_user_func( $this->beforeEach, $this->getContext() );
		}
	}

	public function callAfterEach() {
		if ( isset( $this->afterEach ) ) {
			call_user_func( $this->afterEach, $this->getContext() );
		}
		if ( $this->parent ) {
			$this->parent->callAfterEach();
		}
	}
}


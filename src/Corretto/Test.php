<?php
namespace Corretto;

class Test {
	protected $name;
	protected $callable;
	protected $exception;
	protected $context;

	public $parent;
	public $skip = false;

	public function __construct( $name, callable $callable = null ) {
		$this->name = $name;
		$this->callable = $callable;
		$this->context = new \StdClass();
		if ( ! $this->callable ) {
			$this->skip = true;
		}
	}

	public function getTestInfo() {
		return [ 'fullName' => $this->getFullName() ];
	}

	public function getFullName() {
		$parentName = $this->parent ? $this->parent->getFullName() : '';
		return $parentName ? $parentName . ' ' . $this->name : $this->name;
	}

	public function getName() {
		return $this->name;
	}

	public function doesTestMatchPattern( $matching = null ) {
		return ( ! $matching || preg_match( '/' . $matching . '/', $this->getFullName() ) );
	}

	public function doesTestMatchString( $matching = null ) {
		return ( ! $matching || strpos( $this->getFullName(), $matching ) !== false );
	}

	public function getTest() {
		return $this->callable;
	}

	public function setException( \Exception $e ) {
		$this->exception = $e;
	}

	public function getException() {
		return $this->exception;
	}

	public function shouldSkip() {
		return $this->skip || ( $this->parent && $this->parent->skip ) || ! $this->getTest();
	}

	public function getContext() {
		if ( $this->parent ) {
			return $this->parent->getContext();
		}
		return $this->context;
	}

	public function callBeforeEach() {
		if ( $this->parent ) {
			$this->parent->callBeforeEach();
		}
	}

	public function callAfterEach() {
		if ( $this->parent ) {
			$this->parent->callAfterEach();
		}
	}
}


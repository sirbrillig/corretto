<?php
namespace Corretto;

class Test {
	protected $name;
	protected $callable;
	protected $exception;

	public $parent;
	public $skip = false;

	public function __construct( string $name, callable $callable = null ) {
		$this->name = $name;
		$this->callable = $callable;
		if ( ! $this->callable ) {
			$this->skip = true;
		}
	}

	public function getFullName() {
		return $this->parent ? $this->parent->getFullName() . ' ' . $this->name : $this->name;
	}

	public function getName() {
		return $this->name;
	}

	public function doesTestMatch( $matching = null ) {
		return ( ! $matching || preg_match( '/' . $matching . '/', $this->getFullName() ) );
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
			return $this->parent->context;
		}
		return new \StdClass();
	}
}


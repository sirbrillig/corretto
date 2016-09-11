<?php
namespace Corretto;

class Test {
	private $name;
	private $callable;
	private $exception;

	public $parent;

	public function __construct( string $name, callable $callable ) {
		$this->name = $name;
		$this->callable = $callable;
	}

	public function getFullName() {
		return $this->parent ? $this->parent->getFullName() . ' ' . $this->name : $this->name;
	}

	public function getName() {
		return $this->name;
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
}


<?php
namespace Corretto;

const SKIP = 'SKIP';

function setRunner( $suite ) {
	global $runner;
	$runner = $suite;
}

function describe( string $name, callable $callable ) {
	global $runner;
	$desc = new Suite( $name, $callable );
	$runner->addSuite( $desc );
}

function context( string $name, callable $callable = null ) {
	describe( $name, $callable );
}

function suite( string $name, callable $callable = null ) {
	describe( $name, $callable );
}

function specify( string $name, $callable = null ) {
	it( $name, $callable );
}

function test( string $name, $callable = null ) {
	it( $name, $callable );
}

function it( string $name, $callable = null ) {
	global $runner;
	if ( $name === 'SKIP' ) {
		$name = $callable;
		$callable = null;
	}
	$test = new Test( $name, $callable );
	$runner->addTest( $test );
}

// TODO: move to assertions file
function assert( $expression = false ) {
	if ( ! $expression ) {
		throw new \Exception( "Failed asserting that '" . var_export( $expression, true ) . "' is true" );
	}
}

<?php
namespace Corretto;

const SKIP = 'SKIP';

function setRunner( $suite ) {
	global $runner;
	$runner = $suite;
}

function describe( string $name, $callable = null, $maybeCallable = null ) {
	global $runner;
	$skip = false;
	if ( $name === 'SKIP' ) {
		$name = $callable;
		$callable = $maybeCallable;
		$skip = true;
	}
	$suite = new Suite( $name, $callable );
	$suite->skip = $skip;
	$runner->addSuite( $suite );
}

function context( string $name, $callable = null ) {
	describe( $name, $callable );
}

function suite( string $name, $callable = null ) {
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

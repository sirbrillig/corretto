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
	$runner->addSuiteToCurrentSuite( $suite );
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
	$runner->addTestToCurrentSuite( $test );
}

function color( string $message, string $type ) {
	$key = "";
	switch( $type ) {
	case 'FAIL':
		$key = '[31m';
		break;
	case 'OK':
		$key = '[32m';
		break;
	case 'WARN':
		$key = '[33m';
		break;
	case 'INFO':
		$key = '[38;2;127;127;127m';
		break;
	default:
		return $message;
	}
	return chr( 27 ) . "$key" . "$message" . chr( 27 ) . "[0m";
}


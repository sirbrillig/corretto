<?php
namespace Corretto;

const SKIP = 'SKIP';

function setRunner( $suite ) {
	global $runner;
	$runner = $suite;
}

function debug() {
	global $isDebugMode;
	if ( ! isset( $isDebugMode ) ) {
		return;
	}
	$args = array_map( function( $arg ) {
		if ( is_string( $arg ) ) {
			return $arg;
		}
		return var_export( $arg, true );
	}, func_get_args() );
	$message = implode( ' ', $args );
	echo $message . PHP_EOL;
}

function describe( $name, $callable = null, $maybeCallable = null ) {
	global $runner;
	$runner->createAndAddSuite( $name, $callable, $maybeCallable );
}

function context( $name, $callable = null ) {
	describe( $name, $callable );
}

function suite( $name, $callable = null ) {
	describe( $name, $callable );
}

function specify( $name, $callable = null ) {
	it( $name, $callable );
}

function test( $name, $callable = null ) {
	it( $name, $callable );
}

function it( $name, $callable = null ) {
	global $runner;
	$runner->createAndAddTest( $name, $callable );
}

function before( callable $callable ) {
	global $runner;
	$runner->addBeforeToCurrentSuite( $callable );
}

function after( callable $callable ) {
	global $runner;
	$runner->addAfterToCurrentSuite( $callable );
}

function beforeEach( callable $callable ) {
	global $runner;
	$runner->addBeforeEachToCurrentSuite( $callable );
}

function afterEach( callable $callable ) {
	global $runner;
	$runner->addAfterEachToCurrentSuite( $callable );
}

function color( $message, $type ) {
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


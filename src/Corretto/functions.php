<?php
namespace Corretto;

$rootDescription;

function setRootDescription( $description ) {
	global $rootDescription;
	$rootDescription = $description;
}

function describe( string $name, callable $callable ) {
	global $rootDescription;
	$desc = new Description( $name, $callable );
	$rootDescription->addDescription( $desc );
}

function it( string $name, callable $callable ) {
	global $rootDescription;
	$test = new Test( $name, $callable );
	$rootDescription->addTest( $test );
}

function assert( $expression = false ) {
	if ( ! $expression ) {
		throw new \Exception( "Failed asserting that '" . strval( $expression ) . "' is true" );
	}
}

<?php
namespace Corretto;

function expect( $actual ) {
	return new BaseExpectation( $actual );
}

function extendExpectation( $newExpectation ) {
	BaseExpectation::extendExpectation( $newExpectation );
}

extendExpectation( '\Corretto\ContainExpectation' );

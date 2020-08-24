<?php
/*
	Input:
		$_GET[ 'broj1' ] - prvi operand
		$_GET[ 'broj2' ] - drugi operand
		$_GET[ 'op' ] - operator ("zbroji", "oduzmi", "mnozi", "dijeli")

	Output: JSON sa svojstvima
		rezultat - rezultat operacije
*/

function sendJSONandExit( $message )
{
    // Kao izlaz skripte pošalji $message u JSON formatu i prekini izvođenje.
    header( 'Content-type:application/json;charset=utf-8' );
    echo json_encode( $message );
    flush();
    exit( 0 );
}


$broj1 = (int) $_GET[ 'broj1' ];
$broj2 = (int) $_GET[ 'broj2' ];
$op = $_GET[ 'op' ];

if( strcmp( $op, 'zbroji' ) === 0 )
	$rez = $broj1 + $broj2;
else if( strcmp( $op, 'oduzmi' ) === 0 )
	$rez = $broj1 - $broj2;
else if( strcmp( $op, 'mnozi' ) === 0 )
	$rez = $broj1 * $broj2;
else if( strcmp( $op, 'dijeli' ) === 0 )
	$rez = $broj1 / $broj2;

$message = [];
$message[ 'rezultat' ] = $rez;
sendJSONandExit( $message );

?>

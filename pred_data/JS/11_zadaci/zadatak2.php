<?php
/*
	Input:
		$_GET['fileName'] = ime datoteke za koju želimo info.

	Output: JSON
		{
			fileName = ime datoteke,
			lastModified = datum zadnje promjene,
			size = veličina u byteovima
		}
		ili
		{
			error = poruka o grešci.
		}
*/

function sendJSONandExit( $message )
{
    // Kao izlaz skripte pošalji $message u JSON formatu i prekini izvođenje.
    header( 'Content-type:application/json;charset=utf-8' );
    echo json_encode( $message );
    flush();
    exit( 0 );
}


function sendErrorAndExit( $messageText )
{
	$message = [];
	$message[ 'error' ] = $messageText;
	sendJSONandExit( $message );
}


if( !isset( $_GET['fileName'] ) )
	sendErrorAndExit( "Nije poslano ime datoteke." );


// Iz sigurnosnih razloga dozvoli samo datoteke u trenutnom direktoriju.
// Recimo, imena sastavljena samo od slova, brojki, razmaka, crtica, underscorea i jedne točke.
// Prvo provjeri jel se zaista šalje link oblika http://nesto.com/imeDatoteke
$url = $_GET['fileName'];
if( filter_var( $url, FILTER_VALIDATE_URL ) === false )
	sendErrorAndExit( "Poslani link (" . $url . ") nije ispravan." );

// Zatim dohvati samo ime datoteke.
$fileName = basename( $url );

if( !file_exists( $fileName ) )
	sendErrorAndExit( "Ne postoji ta datoteka (" + $fileName + ")" );


// Dohvati info
$message = [];
$message[ 'fileName' ] = $fileName;
$message[ 'lastModified' ] = filemtime( $fileName );
$message[ 'size' ] = filesize( $fileName );

sendJSONandExit( $message );

?>

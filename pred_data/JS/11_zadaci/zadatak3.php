<?php 
require_once 'db.class.php';

/*
	Input:
		$_GET['vrijemeZadnjegPristupa'] = timestamp kad je bio zadnji pristup

	Output: JSON
		{
			dionice: Polje objekata oblika { oznaka: xxx, ime: xxx, cijena: xxx },
			vrijemeZadnjegPristupa: timestamp kad je bio zadnji pristup
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

if( !isset( $_GET['vrijemeZadnjegPristupa'] ) )
	sendErrorAndExit( 'Nije postavljeno $_GET["vrijemeZadnjegPristupa"].' );

$zadnjiPristup = (int) $_GET[ 'vrijemeZadnjegPristupa' ];

while( 1 )
{
	// Provjeri najkasnije vrijeme zadnje promjene
	$db = DB::getConnection();
	$st = $db->prepare( "SELECT MAX(lastModified) AS maxLastModified FROM Dionice" );
	$st->execute();

	$row = $st->fetch();

	$timestamp = strtotime( $row['maxLastModified'] );

	if( $timestamp > $zadnjiPristup )
	{
		// Dohvati sve dionice
		$st = $db->prepare( "SELECT Oznaka, Ime, Cijena FROM Dionice" );
		$st->execute();

		$message = [];
		$message[ 'vrijemeZadnjegPristupa' ] = $timestamp;
		$message[ 'dionice' ] = [];

		while( $row = $st->fetch() )
		{
			$message[ 'dionice' ][] = array( 'ime' => $row['Ime'], 'oznaka' => $row['Oznaka'], 'cijena' => $row['Cijena'] );			
		}

		sendJSONandExit( $message );
	}

	// Odspavaj 5 sekundi.
	usleep( 5000000 );
}

?>

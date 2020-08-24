<?php

// Stvaramo tablice u bazi (ako već ne postoje od ranije).
require_once __DIR__ . '/db.class.php';

create_table_doc_users();
create_table_documents();
create_table_messages();

exit( 0 );

// --------------------------
function has_table( $tblname )
{
	$db = DB::getConnection();

	try
	{
		$st = $db->prepare(
			'SHOW TABLES LIKE :tblname'
		);

		$st->execute( array( 'tblname' => $tblname ) );
		if( $st->rowCount() > 0 )
			return true;
	}
	catch( PDOException $e ) { exit( "PDO error [show tables]: " . $e->getMessage() ); }

	return false;
}


function create_table_doc_users()
{
	$db = DB::getConnection();

	if( has_table( 'doc_users' ) )
		exit( 'Tablica doc_users vec postoji. Obrisite ju pa probajte ponovno.' );


	try
	{
		$st = $db->prepare(
			'CREATE TABLE IF NOT EXISTS doc_users (' .
			'first_name CHAR(50),' .
			'last_name CHAR(50),' .
			'username VARCHAR(50) NOT NULL PRIMARY KEY,' .
			'password VARCHAR(255) NOT NULL,' .
			'email VARCHAR(50) NOT NULL,' .
			'documents JSON NOT NULL )'
		);

		$st->execute();
	}
	catch( PDOException $e ) { exit( "PDO error [create doc_users]: " . $e->getMessage() ); }

	echo "Napravio tablicu doc_users.<br />";
}


function create_table_documents()
{
	$db = DB::getConnection();

	if( has_table( 'documents' ) )
		exit( 'Tablica documents vec postoji. Obrisite ju pa probajte ponovno.' );

	try
	{
		$st = $db->prepare(
			'CREATE TABLE IF NOT EXISTS documents (' .
			'id VARCHAR(6) NOT NULL PRIMARY KEY,' .
			'title VARCHAR(100) DEFAULT "Novi dokument",' .
			'creator_username VARCHAR(50),' .
			'content JSON,' .
			'last_edit_time DATETIME DEFAULT CURRENT_TIMESTAMP,' .
			'last_edit_user VARCHAR(50) ) '
		);

		$st->execute();
	}
	catch( PDOException $e ) { exit( "PDO error [create documents]: " . $e->getMessage() ); }

	echo "Napravio tablicu documents.<br />";
}


function create_table_messages()
{
	$db = DB::getConnection();

	if( has_table( 'messages' ) )
		exit( 'Tablica messages vec postoji. Obrisite ju pa probajte ponovno.' );

	try
	{
		$st = $db->prepare(
			'CREATE TABLE IF NOT EXISTS messages (' .
			'id VARCHAR(6) NOT NULL PRIMARY KEY,' .
			'username VARCHAR(50),' .
			'document_id VARCHAR(6),' .
			'time DATETIME DEFAULT CURRENT_TIMESTAMP,'.
			'content VARCHAR(1000) )'
		);

		$st->execute();

	}
	catch( PDOException $e ) { exit( "PDO error [create messages]: " . $e->getMessage() ); }

	echo "Napravio tablicu messages.<br />";
}

?> 

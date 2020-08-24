<?php

// Popunjavamo tablice u bazi "probnim" podacima.
require_once __DIR__ . '/db.class.php';

//seed_table_users();
//seed_table_documents();
seed_table_messages();

exit( 0 );

// ------------------------------------------
function seed_table_users()
{
	$db = DB::getConnection();

	try
	{
		$st = $db->prepare( 'INSERT INTO doc_users(first_name, last_name, username, password, email, documents) VALUES (:first_name, :last_name, :username, :password, \'a@b.com\', :documents)' );

		$st->execute( array( 'first_name' => 'Maja', 'last_name' => 'Majić', 'username' => 'majama', 'password' => password_hash( 'majinasifra', PASSWORD_DEFAULT ), 'documents' => '["abc123"]' ) );
		$st->execute( array( 'first_name' => 'Mario', 'last_name' => 'Marić', 'username' => 'marioma', 'password' => password_hash( 'mariovasifra', PASSWORD_DEFAULT ), 'documents' => '["abc123"]' ) );

	}
	catch( PDOException $e ) { exit( "PDO error [insert doc_users]: " . $e->getMessage() ); }

	echo "Ubacio u tablicu doc_users.<br />";
}

// ------------------------------------------
function seed_table_documents()
{
	$db = DB::getConnection();

	try
	{
		$st = $db->prepare( 'INSERT INTO documents(id, creator_username, content, last_edit_user) VALUES (:id, :creator_username, :content, :last_edit_user)' );

		$st->execute( array( 'id' => 'abc123', 'creator_username' => 'majama', 'content' => '[
			{
				"id_line": "3d8201",
				"content": "",
				"editing": "",
				"is_locked": 0
			} ]',
			'last_edit_user' => 'majama' ) );

	}
	catch( PDOException $e ) { exit( "PDO error [insert documents]: " . $e->getMessage() ); }

	echo "Ubacio u tablicu documents.<br />";
}

// ------------------------------------------
function seed_table_messages()
{
	$db = DB::getConnection();

	try
	{
		$st = $db->prepare( 'INSERT INTO messages(id, username, document_id, content ) VALUES (:id, :username, :document_id, :content)' );

		$st->execute( array( 'id'=>'aaa111', 'username' => 'majama', 'document_id' => 'abc123', 'content' => 'Bok ljudi! Jeste spremni za pisanje seminara?' ) );
		$st->execute( array( 'id'=>'bbb111', 'username' => 'marioma', 'document_id' => 'abc123', 'content' => 'Bok! Ja sam spreman, idemo raditi :)' ) );

	}
	catch( PDOException $e ) { exit( "PDO error [messages]: " . $e->getMessage() ); }

	echo "Ubacio u tablicu messages.<br />";
}

?> 

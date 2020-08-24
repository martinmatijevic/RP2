<?php

class DocumentService
{
	function getDocumentById( $id )
	{
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT id, title, creator_username, content, last_edit_time, last_edit_user FROM documents WHERE id=:id' );
			$st->execute( array( 'id' => $id ) );
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }
		$row = $st->fetch();

		if( $row === false )
			return null;
		else
            return new Document(
                $row['id'], $row['title'], $row['creator_username'],
                $row['content'], $row['last_edit_time'], $row['last_edit_user']
            );
	}

	function getDocumentsByIds( $list_of_ids )
	{
		$arr = array();

		if( $list_of_ids === null )
			return null;

		for( $i = 0; $i < count($list_of_ids); $i++ )
		{
			$id = $list_of_ids[ $i ];
			try
			{
				$db = DB::getConnection();
				$st = $db->prepare( 'SELECT id, title, creator_username, content, last_edit_time, last_edit_user FROM documents WHERE id=:id' );
				$st->execute( array( 'id' => $id ) );
			}
			catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

			$row = $st->fetch();
			if( $row === false )
				return null;
			else
				$arr[] = new Document(
					$row['id'], $row['title'], $row['creator_username'],
					$row['content'], $row['last_edit_time'], $row['last_edit_user']
				);
		}
		return $arr;

	}


	function getAllDocuments( )
	{
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT id, title, creator_username, content, last_edit_time, last_edit_user FROM documents' );
			$st->execute();
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

		$arr = array();
		while( $row = $st->fetch() )
		{
			$arr[] = new Document(
                $row['id'], $row['title'], $row['creator_username'],
                $row['content'], $row['last_edit_time'], $row['last_edit_user']
            );
		}

		return $arr;
	}

	function addDocument( $title, $creator_username )
	{
		//generiraj 6-znamenkasti id za document i za defaultnu praznu liniju
		$id_document = substr(md5(microtime()),rand(0,26),6);
		$id_line = substr(md5(microtime()),rand(0,26),6);

		//kreiraj defaultnu praznu liniju i dodaj ju u content
		$line = (object) array(
			'id_line' => $id_line,
			'content' => "",
			'is_locked' => 0,
			'editing' => ""
		);
		$content = array();
		$content[] = $line;

		try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'INSERT INTO documents(id, title, creator_username, last_edit_user, content ) VALUES (:id, :title, :creator_username, :last_edit_user, :content)' );
			$st->execute( array( 'id' => $id_document, 'title' => $title, 'creator_username' => $creator_username, 'last_edit_user' => $creator_username, 'content' => json_encode( $content ) ) );
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

		return $id_document;
	}

	function updateDocument( $document )
	{
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'UPDATE documents SET content=:content,
					last_edit_user=:last_edit_user, last_edit_time=now() WHERE id=:id');
			$st->execute( array( 'id' => $document->id, 'content' => json_encode($document->content ),
					'last_edit_user' => $document->last_edit_user ) );
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }
	}

	//funkcija koja vraća liniju kao objekt
	//vraća null ako ta linija ne postoji u dokumentu
	function findLineInDocumentById( $document, $id_line )
	{
		$document_content = json_decode( $document->content );

		$index = -1;

		for( $i = 0; $i < count( $document_content ); $i++ ) //dokument po defaultu ima jednu praznu liniju (content ne može biti null)
        {
            if ( $document_content[$i]->id_line === $id_line  )
            {
                $index = $i;
                break;
            }
		}

		if( $index === -1 )
			return null;

		$line =	(object) array(
				'id_line' => $id_line,
				'content' => $document_content[$index]->content,
				'is_locked' => $document_content[$index]->is_locked,
				'editing' => $document_content[$index]->editing
			);

		return $line;
	}
};
?>

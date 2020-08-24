<?php

class ChatService{

    function getMessagesById( $document_id )
	{
        $arr = array();
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT id, username, document_id, time, content FROM messages WHERE document_id=:document_id ORDER BY time' );
			$st->execute( array( 'document_id' => $document_id ) );
		}
        catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }
        $row = $st->fetch();
        if( $row === false )
			return null;
        do{
            $arr[] = new Chat(
                $row['id'], $row['username'], $row['document_id'],
                $row['time'], $row['content']
            );
        $row = $st->fetch();
        }
        while($row !== false);
        return $arr;

    }
    function addMessage( $username, $document_id, $content )
	{
        $id = substr(md5(microtime()),rand(0,26),6);

		try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'INSERT INTO messages(id, username, document_id, content) VALUES (:id, :username, :document_id, :content)' );
			$st->execute( array( 'id' => $id, 'username'=> $username, 'document_id' => $document_id, 'content' => $content ) );
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }
	
		return $id;
	}
}

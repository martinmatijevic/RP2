<?php

class UserService
{
	function getUserByUsername( $username )
	{
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'SELECT first_name, last_name, username, password, email, documents FROM doc_users WHERE username=:username' );
			$st->execute( array( 'username' => $username ) );
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

		$row = $st->fetch();
		if( $row === false )
			return null;
		else
			return new User( $row['first_name'], $row['last_name'], $row['username'], $row['password'], $row['email'], json_decode($row['documents'], true));
	}

	function addNewUser ( $first_name, $last_name, $username, $password, $email )
	{
		try{
		$db = DB::getConnection();
			$st = $db->prepare( 'INSERT INTO doc_users(first_name, last_name, username, password, email) VALUES (:first_name, :last_name, :username, :password, :email)' );
			$st->execute( array( 'first_name' => $first_name, 'last_name' => $last_name, 'username' => $username, 'password' => password_hash( $password, PASSWORD_DEFAULT), 'email' => $email ) );
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }
		return true;
	}

	function addDocumentToUser( $id, $user )
	{
		$arr = $user->documents;
		$arr[] = $id;
		try
		{
			$db = DB::getConnection();
			$st = $db->prepare( 'UPDATE doc_users SET documents = :documents WHERE username= :username' );
			$st->execute( array( 'documents' => json_encode($arr), 'username' => $user->username  ) );
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }
	}
};
?>

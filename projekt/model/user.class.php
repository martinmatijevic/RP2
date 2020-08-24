<?php

class User
{
	protected $first_name, $last_name, $username, $password, $email, $documents;

	function __construct( $first_name, $last_name, $username, $password, $email, $documents)
	{
        $this->first_name = $first_name;
        $this->last_name = $last_name;
		$this->username = $username;
		$this->password = $password;
		$this->email = $email;
		$this->documents = $documents;
	}

	function __get( $prop ) { return $this->$prop; }
	function __set( $prop, $val ) { $this->$prop = $val; return $this; }
}

?>

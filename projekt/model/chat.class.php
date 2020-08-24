<?php

class Chat
{
	protected $id, $username, $document_id, $time, $content;

	function __construct( $id, $username, $document_id, $time, $content )
	{
		$this->id = $id;
		$this->username = $username;
        $this->document_id = $document_id;
        $this->time = $time;
        $this->content = $content;

	}

	function __get( $prop ) { return $this->$prop; }
	function __set( $prop, $value ) { $this->$prop = $value; return $this; }
}

?>

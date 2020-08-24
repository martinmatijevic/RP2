<?php

class Document
{
	protected $id, $title, $creator_username, $content, $last_edit_time, $last_edit_user;

	function __construct( $id, $title, $creator_username, $content, $last_edit_time, $last_edit_user )
	{
		$this->id = $id;
		$this->title = $title;
        $this->creator_username = $creator_username;
        $this->content = $content;
        $this->last_edit_time = $last_edit_time;
        $this->last_edit_user = $last_edit_user;
	}

	function __get( $prop ) { return $this->$prop; }
	function __set( $prop, $value ) { $this->$prop = $value; return $this; }
}

?>

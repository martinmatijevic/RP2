<?php

/*
	Singleton klasa koja "omotava" $_SESSION -- pazi da se samo jednom pozove session_start.
	Također, ima __get i __set koji čitaju i postavljaju vrijednosti u superglobalnu varijablu $_SESSION.

	Dakle, pristup $_SESSION-u se radi ovako:
		$ss = Session::getInstance();
		$ss->nesto = 8; // kao da smo napisali $_SESSION['nesto'] = 8.
*/

class Session
{
	private static $instance = null;

	private function __construct() { }
	private function __clone() { }

	public static function getInstance() 
	{
		if( Session::$instance === null )
	    {
	    	session_start();
	    	Session::$instance = new Session;
	    }
		return Session::$instance;
	}

	public function destroy() { session_unset(); session_destroy(); Session::$instance = null; }

	// Podatke dohvaćamo i spremamo u $_SESSION['var'] preko Session::getInstance()->var
	// Pazi: vraćamo referencu (&).
	public function &__get( $prop ) 
	{ 
		if( isset( $_SESSION[ $prop ] ) ) 
			return $_SESSION[ $prop ]; 

		throw new Exception( 'Property ' . $prop . ' non-existent.' );
	}

	public function __set( $prop, $val ) { $_SESSION[ $prop ] = $val; }
}

?>

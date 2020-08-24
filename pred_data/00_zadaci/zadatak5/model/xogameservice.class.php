<?php

/*
	Glavni dio modela - klasa koja pruža "servis" za igranje igre križić-kružić.
*/

class XOGameService
{
	function initializeGame( $playerX_name, $playerO_name )
	{
		// Dohvati ili započni session.
		$ss = Session::getInstance();

		// Spremi imena igrača, tko je idući na redu i slično u session.
		$ss->players = array( 'x' => $playerX_name, 'o' => $playerO_name );
		$ss->currentPlayer = 'x';
		$ss->lastError = false;

		// Napravi praznu ploču za igru. Na početku je puna praznih polja (piše '?' u njima).
		$ss->board = array( array('?', '?', '?' ), array('?', '?', '?' ), array('?', '?', '?' ) );

		$ss->initialized = true;
	}


	function isInitialized()
	{
		// Dohvati ili započni session
		$ss = Session::getInstance();

		// Provjeri jel definirana varijabla $ss->initialized
		try
		{
			if( $ss->initialized === true )
				return true;
		}
		catch( Exception $e ) { return false; }
	}


	function getPlayerNames() { return Session::getInstance()->players;	}
	function getcurrentPlayer() { return Session::getInstance()->currentPlayer; }
	function getBoard() { return Session::getInstance()->board; }

	function setError( $msg ) { Session::getInstance()->lastError = $msg; }

	function getLastError() 
	{ 
		// Vraća zadnje postavljenu poruku o grešci.
		$tmp = Session::getInstance()->lastError;

		// Nakon što netko "pročita" zadnju grešku, brišemo ju.
		$this->setError( false );	

		return $tmp;
	}

	function isGameOver() { return $this->getWinner() !== false; }

	function getWinner() 
	{ 
		// Vraća 'x' ako je 'x' pobjedio, 'o' ako je 'o' pobijedio, '?' ako je ploča puna, false ako ništa od navedenog.
		$ss = Session::getInstance();

		// Otkrij da li je netko pobijedio.
		// Provjeri redove
		for( $r = 0; $r < 3; ++$r )
			if( $ss->board[$r][0] == $ss->board[$r][1] && $ss->board[$r][0] == $ss->board[$r][2] && $ss->board[$r][0] !== '?' )
				return $ss->board[$r][0];

		// Provjeri stupce
		for( $c = 0; $c < 3; ++$c )
			if( $ss->board[0][$c] == $ss->board[1][$c] && $ss->board[0][$c] == $ss->board[2][$c] && $ss->board[0][$c] !== '?' )
				return $ss->board[0][$c];

		// Provjeri gl. dijagonalu
		if( $ss->board[0][0] == $ss->board[1][1] && $ss->board[0][0] == $ss->board[2][2] && $ss->board[0][0] !== '?' )
			return $ss->board[0][0];

		// Provjeri sp. dijagonalu
		if( $ss->board[2][0] == $ss->board[1][1] && $ss->board[2][0] == $ss->board[0][2] && $ss->board[2][0] !== '?' )
			return $ss->board[2][0];

		// Da li su sve polja popunjena, a nitko nije pobijedio?
		$isBoardFull = true;
		for( $r = 0; $r < 3 && $isBoardFull; ++$r )
			for( $c = 0; $c < 3 && $isBoardFull; ++$c )
				if( $ss->board[$r][$c] === '?' )
					$isBoardFull = false;

		if( $isBoardFull )
			return '?';

		// Još nije gotovo
		return false;
	}


	function playAtCell( $r, $c )
	{
		// Odigrava potez aktualnog igrača na polje ($r, $c).
		// Ako potez nije legalan, isti igrač ostaje na potezu.
		// Ako potez je legalan, mijenja se igrač na potezu, osim ako je igra upravo završila ovim potezom.

		// Da li je ($r, $c) izvan granica ploče?
		if( !( 0 <= $r && $r <= 2 && 0 <= $c && $c <= 2 ) )
		{
			$this->setError( 'Odigrano je izvan granica ploče?!' );
			return;
		}

		$ss = Session::getInstance();

		// Da li je ($r, $c) već zauzeto polje?
		if( $ss->board[$r][$c] !== '?' )
		{
			$this->setError( 'Odigrano je na već zauzeto polje?!' );
			return;
		}

		// Stavi oznaku aktualnog igrača. PAZI: Ovdje je bitno da Session::__get vraća REFERENCU (&).
		// U protivnom (bez reference) mora ići ovako: 
		// $tmp=$ss->board; $tmp[$r][$c]=$ss->currentPlayer; $ss->board=$tmp;
		$ss->board[$r][$c] = $ss->currentPlayer;

		if( $this->getWinner() !== $ss->currentPlayer )
		{
			if( $ss->currentPlayer === 'x' )
				$ss->currentPlayer = 'o';
			else
				$ss->currentPlayer = 'x';
		}
	}


	function endGame() { Session::getInstance()->destroy();	}
}

?>


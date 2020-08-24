<?php 

/*
	Kontroler zadužen za igranje igre XO. Akcije:
		- index - prikazuje view koji ima formu s 9 buttona za igru križić-kružić.
		- processBoardForm - obrađuje podatke iz gore navedene forme za igru.
*/

class GameController extends BaseController
{
	public function index() 
	{
		// Dohvati servis za upravljanje igrom.
		$XOs = new XOGameService();

		// Ako igra uopće nije inicijalizirana (nisu unesena imena igrača), 
		// preusmjeri na početnu stranicu za unos imena.
		if( !$XOs->isInitialized() )
		{
			header( 'Location: ' . __SITE_URL . '/index.php' );
			exit();
		}

		// U protivnom, dohvati aktualno stanje igre: imena igrača, tko je na potezu.
		$players = $XOs->getPlayerNames();
		$currentPlayer = $XOs->getCurrentPlayer();

		// Pripremi varijable za ispis.
		// Da li je igra završila? 
		$this->registry->template->isGameOver = $XOs->isGameOver();

		if( $XOs->isGameOver() )
		{
			// Tko je pobijedio?
			$winner = $XOs->getWinner();
			if( $winner === '?' )
				$this->registry->template->title = 'Igra je završila izjednačenim rezultatom!';
			else	
				$this->registry->template->title = 'Pobjednik je igrač ' . $winner . ' (' . $players[$winner] . ')!';
		}
		else
			$this->registry->template->title = 'Na potezu je igrač ' . $currentPlayer . ' (' . $players[$currentPlayer] . ')';
		
		$this->registry->template->errorMessage = $XOs->getLastError();
		$this->registry->template->board = $XOs->getBoard();


		// Prikaži view (formu) s tablicom za igru.
        $this->registry->template->show( 'game_index' );
	}


	public function processBoardForm()
	{
		// Akcija koja analizira što je kliknuto u view-u game_index -- tj. u formi s tablicom za igru.
		// Ne prikazuje niti jedan view, nego samo mijenja model i onda preusmjerava na odgovarajuću stranicu u ovisnosti o 
		// tome što je kliknuto u formi.

		// Dohvati servis za upravljanje igrom.
		$XOs = new XOGameService();

		// Ako igra uopće nije inicijalizirana, preusmjeri na početnu stranicu za unos imena.
		if( !$XOs->isInitialized() )
		{
			header( 'Location: ' . __SITE_URL . '/index.php' );
			exit();
		}

		// Netko je kliknuo na Reset?
		if( isset( $_POST['reset'] ) )
		{
			// Prekini postojeću igru.
			$XOs->endGame();

			// Preusmjeri sve na početnu stranicu.
			header( 'Location: ' . __SITE_URL . '/index.php' );
			exit();
		}

		// Netko je kliknuo na gumb u 3x3 tablici?
		if( isset( $_POST['btn'] ) )
		{
			// Koji gumb? Bit će npr. $_POST['btn'] = '21' --> spremi $r=2, $c=1
			$r = (int) $_POST['btn'][0];
			$c = (int) $_POST['btn'][1];

			// Odigraj taj potez, ako je dozvoljen -- za sve se pobrine playAtCell.
			$XOs->playAtCell( $r, $c );

			// Prikaži ponovno ploču za igru (kontroler game/index).
			header( 'Location: ' . __SITE_URL . '/index.php?rt=game' );
			exit();			
		}

		// Morao je biti kliknut ili reset ili neki gumb u 3x3 tablici. Ako ne, dogodila se neka čudna greška.
		$XOs->setError( 'Nije kliknuto ni na reset ni na gumb?!' );
		header( 'Location: ' . __SITE_URL . '/index.php?rt=game' );
		exit();
	}
}; 

?>

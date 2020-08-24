<?php 

/*
	Kontroler zadužen za inicijalizaciju igre. Akcije:
		- index - prikazuje formu za unos imena igrača.
		- processNamesForm - obrađuje podatke iz forme za unos imena igrača.
*/

class IndexController extends BaseController
{
	public function index() 
	{
		// Popuni template potrebnim podacima
		$this->registry->template->title = 'Dobro došli u igru križić-kružić!';
		$this->registry->template->errorMessage = '';
        
		// Prikaži view sa formom za unos imena.
        $this->registry->template->show( 'index_index' );
	}


	public function processNamesForm() 
	{
		// Kontroler za procesiranje podataka poslanih preko forme za unos imena koja se nalazi u view-u index_index.

		// Provjeri jesu li podaci dobro uneseni.
		if( isset( $_POST['nameX'] ) && preg_match( '/^[a-zA-Z]{1,20}$/', $_POST['nameX'] ) &&
			isset( $_POST['nameO'] ) && preg_match( '/^[a-zA-Z]{1,20}$/', $_POST['nameO'] ) )
		{
			// Ako je sve OK, stvori novi servis za upravljanje igrom.
			$XOs = new XOGameService();

			// Inicijaliziraj novu igru tako da pošalješ imena igrača.
			// Spremi imena (kontrolera se ne tiče kud će to XOs spremiti -- u bazu, session ili negdje treće.)
			$XOs->initializeGame( $_POST['nameX'], $_POST['nameO'] );

			// Sad bismo mogli dohvatiti stanje na (praznoj) ploči i iscrtati view game_index.
			// Ali posve isto radi i kontroler game_index. Zato samo preusmjerimo sve na njega.
			header( 'Location: ' . __SITE_URL . '/index.php?rt=game' );
			exit();
		}
		else 
		{
			// Imena nisu dobro unesena. Iscrtaj ponovno formu za imena.
			$this->registry->template->title = 'Dobro došli u igru križić-kružić!';
			$this->registry->template->errorMessage = 'Imena igrača nisu ispravno unešena!';
	        
			// Prikaži view sa formom za unos imena.
	        $this->registry->template->show( 'index_index' );
		}
	}
}; 

?>

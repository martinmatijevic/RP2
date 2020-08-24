<?php

session_start();

class UsersController extends BaseController
{
	public function index()
	{
		// Provjeravamo je li definirana $_SESSION['login'].
		// Ako je, znači da je korisnik sad (ili ranije u sesiji) prošao validaciju.
		// Dohvaćamo vrijednost $_SESSION['login'], te iz nje username.

		if( isset( $_SESSION['login'] ) )
		{
			header( 'Location: ' . __SITE_URL . '/index.php?rt=homepage/index' );
		}
		//ako $_SESSION['login'] nije definirana, preusmjeri na login
		header( 'Location: ' . __SITE_URL . '/index.php?rt=users/login' );
	}

	public function login()
	{
		if( isset( $_SESSION['login'] ) )
			header( 'Location: ' . __SITE_URL . '/index.php?rt=homepage/index');
		else
		{
			$this->registry->template->title = 'Login';
			$this->registry->template->show( 'users_login' );
		}
	}

	// Funkcija koja procesira što se dogodi nakon klika na gumb "Ulogiraj se!" ili klika na gumb Registriraj se!
	function loginResults()
	{
		if($_POST['action'] == 'login')
		{
			// Provjeri sastoji li se ime samo od slova; ako ne, crtaj login formu.
			if( !isset( $_POST["username"] ) || preg_match( '/[a-zA-Z]+{1, 20}/', $_POST["username"] ) || empty($_POST['username']) )
			{
				header( 'Location: ' . __SITE_URL . '/index.php?rt=users/login');
				exit();
			}

			// Možda se ne šalje password; u njemu smije biti bilo što.
			if( !isset( $_POST["password"] ) || empty($_POST['password']))
			{
				header( 'Location: ' . __SITE_URL . '/index.php?rt=users/login');
				exit();
			}


			// Sve je OK, provjeri jel ga ima u bazi.
			$cs = new UserService();
			$user = $cs->getUserByUsername( $_POST['username'] );

			if( $user === null )
			{
				// Taj user ne postoji, upit u bazu nije vratio ništa.
				$this->registry->template->title = "Username i lozinka nisu ispravni, pokušaj ponovno.";
				$this->registry->template->show( 'users_login' );
			}
			else
			{
				// Postoji user. Dohvati hash njegovog passworda.
				$hash = $user->password;

				// Da li je password dobar?
				if( password_verify( $_POST['password'], $hash ) )
				{
					//Dobar je. Ulogiraj ga.
					$secret_word = 'racunarski praktikum 2!!!';
					$_SESSION['login'] = $_POST['username'] . ',' . md5( $_POST['username'] . $secret_word );

					//popuni Template podacima
					//$this->registry->template->userInfo = $user;

					//prikaži mu documente
					header( 'Location: ' . __SITE_URL . '/index.php?rt=homepage/index');
					//$this->registry->template->show( 'home_page' );
					exit();
				}
				else
				{
					// Nije dobar. Crtaj opet login formu s pripadnom porukom.
					$this->registry->template->title = "Username i lozinka nisu ispravni, pokušaj ponovno.";
					$this->registry->template->show( 'users_login' );
				}
			}
		}
		else if($_POST['action'] == 'registration')
		{
			$this->registry->template->title = "Registracija";
			$this->registry->template->show( 'users_registration' );
			exit();
		}
		else
		{
			$this->registry->template->title = "greška";
			$this->registry->template->show( 'users_login' );
		}
	}

	function registrationResults()
	{
		if($_POST['action'] == 'registration')
		{
			// Provjeri sastoji li se ime,prezime i username samo od slova; ako ne, crtaj registration formu.
			if( !isset( $_POST["firstname"] ) || preg_match( '/[a-zA-Z]{1, 20}/', $_POST["firstname"] ) || empty($_POST['firstname']) )
			{
				$this->registry->template->title = "Nepravilno ime, pokušaj ponovno";
				$this->registry->template->show( 'users_registration' );
				exit();
			}

			if( !isset( $_POST["lastname"] ) || preg_match( '/[a-zA-Z]{1, 20}/', $_POST["lastname"] ) || empty($_POST['lastname']) )
			{
				$this->registry->template->title = "Nepravilno prezime, pokušaj ponovno";
				$this->registry->template->show( 'users_registration' );
				exit();
			}

			if( !isset( $_POST["username"] ) || preg_match( '/[a-zA-Z]{1, 20}/', $_POST["username"] ) || empty($_POST['username']) )
			{
				$this->registry->template->title = "Nepravilno korisničko ime, pokušaj ponovno";
				$this->registry->template->show( 'users_registration' );
				exit();
			}

			// Možda se ne šalje password; u njemu smije biti bilo što.
			if( !isset( $_POST["password"] ) || empty($_POST['password']))
			{
				$this->registry->template->title = "Nepravilna lozinka, pokušaj ponovno";
				$this->registry->template->show( 'users_registration' );
				exit();
			}
			// Provjeri je li e-mail adresa važeća
			if( !isset( $_POST["email"] ) ||  !filter_var($_POST["email"],FILTER_VALIDATE_EMAIL) )
			{
				$this->registry->template->title = "Nepravilna email adresa, pokušaj ponovno";
				$this->registry->template->show( 'users_registration' );
				exit();
			}

			// Sve je OK, provjeri jel ga ima u bazi.
			$cs = new UserService();
			$user = $cs->getUserByUsername( $_POST['username'] );

			if( $user === null )
			{
				// Taj user ne postoji,dakle stvori ga
				$cs->addNewUser( $_POST['firstname'],  $_POST['lastname'],  $_POST['username'],  $_POST['password'],  $_POST['email'], );
				$this->registry->template->title = "Registracija uspješna,možete se ulogirati";
				$this->registry->template->show( 'users_registration' );
			}
			else
			{
				//u bazi već postoji korisnik s tim usernameom
				$this->registry->template->title = "ovaj username već postoji!";
				$this->registry->template->show( 'users_registration' );
			}


		}
		else if($_POST['action'] == 'login')
		{
			$this->registry->template->title = "Login";
			$this->registry->template->show( 'users_login' );
			exit();
		}
		else
		{
			$this->registry->template->title = "greška";
			$this->registry->template->show( 'users_registration' );
			return;
		}
	}

	function logout()
	{
		session_unset();
		session_destroy();

		$this->registry->template->title = 'Login';

		header( 'Location: ' . __SITE_URL . '/index.php?rt=users/login' );
	}

	public function ajaxCheckLogin()
	{
		function sendJSONandExit( $message )
        {
            // Kao izlaz skripte pošalji $message u JSON formatu i prekini izvođenje.
            header( 'Content-type:application/json;charset=utf-8' );
            echo json_encode( $message );
            flush();
            exit( 0 );
		}

		if( isset( $_SESSION['login'] ) )
			$response = "logged";
		else
			$response = "not logged";

		sendJSONandExit( $response );
	}

};

?>

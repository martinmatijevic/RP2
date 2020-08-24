<?php
session_start();

class HomepageController extends BaseController
{
	public function index()
	{
        if( isset( $_SESSION['login'] ) )
        {
            $us = new UserService();
            $ds = new DocumentService();

            $login_info = explode(",", $_SESSION['login']);

            if ( $login_info !== null )
            {
				$this->registry->template->title = 'Popis dokumenata korisnika <small class="text-muted">' . $login_info[0] . '</small>';
                $user = $us->getUserByUsername( $login_info[0] );
                $this->registry->template->document_list = $ds->getDocumentsByIds( $user->documents );

                $this->registry->template->show( 'home_page' );
            }
            else
                header( 'Location: ' . __SITE_URL . '/index.php?rt=users/login' );
        }
        else
            header( 'Location: ' . __SITE_URL . '/index.php?rt=users/login' );
    }

    function sendJSONandExit( $message )
    {
        // Kao izlaz skripte pošalji $message u JSON formatu i prekini izvođenje.
        header( 'Content-type:application/json;charset=utf-8' );
        echo json_encode( $message );
        flush();
        exit( 0 );
    }

    public function ajaxSendNewUsersDocuments()
    {
        $ds = new DocumentService();

        if( isset( $_SESSION['login'] ) )
        {
            $us = new UserService();

            // File u kojem se nalazi samo zadnje dodani korisnik nekom dokumentu
            $filename  = '/'.__SITE_PATH . '/../../../add_collaborator_info.log';
            $error = "";

            if( !file_exists( $filename ) )
                $error = $error . "Datoteka " . $filename . " ne postoji. ";
            else
            {
                if( !is_readable( $filename ) )
                    $error = $error . "Ne mogu čitati iz datoteke " . $filename . ". ";

                if( !is_writable( $filename ) )
                    $error = $error . "Ne mogu pisati u datoteku " . $filename . ". ";
            }

            if( $error !== "" )
            {
                $response = [];
                $response[ 'error' ] = $error;

                $this->sendJSONandExit( $response );
            }

            $login_info = explode(",", $_SESSION['login']);
            $user = $us->getUserByUsername( $login_info[0] );

            session_write_close();

            //detektiramo je li ubacen novi dokument za korisnika
            //long polling

            // Klijent će u zahtjevu poslati svoje trenutno vrijeme.
            $lastmodif = isset( $_GET['timestamp'] ) ? $_GET['timestamp'] : 0;

            // Otkrij kad je zadnji put neki korisnik dodan u neki dokument
            $currentmodif = filemtime( $filename );

            $relevantchange = 0;
            // Petlja se vrti sve dok promjena nije relevantna za ulogiranog korisnika
            while( !$relevantchange )
            {
                $relevantchange = 1;
                // Petlja koja se vrti sve dok se datoteka ne promijeni
                while( $currentmodif <= $lastmodif )
                {
                    usleep( 10000 ); // odspavaj 10ms da CPU malo odmori :)
                    clearstatcache();
                    $currentmodif = filemtime( $filename ); // ponovno dohvati vrijeme zadnje promjene datoteke
                }
                // Kad dođemo do ovdje, znamo da je datoteka bila promijenjena.
                // Provjerimo tiče li se promjena ovog korisnika.
                $content = explode(",", file_get_contents( $filename ) );
                if( $content[0] !== $user->username ) {
                    $relevantchange = 0;
                    $lastmodif = $currentmodif;
                }
                else
                {
                    //Provjerimo postoji li dokument u bazi
                    $document = $ds->getDocumentById( trim( $content[1], "\n" ) );
                    if ( $document === null )
                        {
                            $relevantchange = 0;
                            $lastmodif = $currentmodif;
                        }
                }
            }

            // Kad dođemo do ovdje znamo da smo našli relevantnu promjenu.
            // Spremi njen sadržaj u $response[ 'id' ] i $response['title']
            // i vrijeme zadnje promjene u $response[ 'timestamp' ]
            $response = array();
            $response[ 'id' ]       = $document->id;
            $response[ 'title' ]    = $document->title;
            $response[ 'timestamp' ] = $currentmodif;

            // Napravi JSON string od ovog i ispiši ga (tj. pošalji klijentu).
            $this->sendJSONandExit( $response );
        }
        else
            header( 'Location: ' . __SITE_URL . '/index.php?rt=users/login' );
    }
};

?>

<?php
session_start();

class TexteditorController extends BaseController
{
	public function index()
	{
        if( isset( $_SESSION['login'] ) )
        {
            //linkovi iz home_page.php šalju ove podatke
            $this->registry->template->document_id = $_GET['document_id'];
            $this->registry->template->title = $_GET['title'];

            $username = explode( ',', $_SESSION['login'] )[0];

            if ( $username !== null )
            {
                $this->registry->template->username = $username;
                $_SESSION['document_id']=$_GET['document_id'];
                $cs = new ChatService();
                $ds = new DocumentService();
                $this->registry->template->messages_list = $cs->getMessagesById( $_GET['document_id'] );
                $this->registry->template->document = $ds->getDocumentById( $_GET['document_id'] );
                $this->registry->template->show( 'texteditor' );
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

    public function createNewDocument()
    {
        $error = "";

        // File u kojem se nalazi samo zadnja poruka.
        $filename  = '/'.__SITE_PATH . '/../../../add_collaborator_info.log';

        $error = "";

        //provjeri postoji li file
        //i ima li potrebna prava čitanja/pisanja
        if( !file_exists( $filename ) )
            fopen( $filename, 'w+' );
        if( !file_exists( $filename ) )
            $error = $error . "Datoteka " . $filename . " ne postoji. ";
        else
        {
            if( !is_readable( $filename ) )
                $error = $error . "Ne mogu čitati iz datoteke " . $filename . ". ";

            if( !is_writable( $filename ) )
                $error = $error . "Ne mogu pisati u datoteku " . $filename . ". ";
        }

        if( !isset( $_POST['title'] ) )
            $error = 'Nije postavljeno $_POST["title"] ili $_POST["creator_username"].';

        if( $error !== "" )
        {
            $response = [];
            $response['error'] = $error;

            $this->sendJSONandExit( $response );
        }

        $ds = new DocumentService();
        $us = new UserService;
        $login_info = explode(",", $_SESSION['login']);
        $id = $ds->addDocument( $_POST['title'], $login_info[0] );


        $user = $us->getUserByUsername( $login_info[0] );
        file_put_contents( $filename, $login_info[0] . ',' . $id );
        $us->addDocumentToUser( $id, $user );

        $response = [];
        $response['id_document'] = $id;
        $this->sendJSONandExit( $response );
    }

    public function waitMessage()
    {
        /*
        Ova skripta nam služi i za spremanje novih poruka koje klijent šalje
        i za slanje poruka klijentu koje je netko drugi napisao.
        */
		set_time_limit(300);
        $id = $_SESSION[ 'document_id' ];
        session_write_close();

        // File u kojem se nalazi samo zadnja poruka.
        $filename  = '/'.__SITE_PATH . '/../../../chat.log';

        $error = "";

        //provjeri je postoji li file
        //i ima li potrebna prava čitanja/pisanja
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



        // Sad ide dio koda koji detektira je li netko drugi napisao poruku.
        // Ovo je "long polling" u skripti.

        // Klijent će u zahtjevu poslati svoje trenutno vrijeme.
        $lastmodif    = isset( $_GET['timestamp'] ) ? $_GET['timestamp'] : 0;

        // Otkrij kad je zadnji put bio promijenjena datoteka u kojoj je spremljena zadnja poruka.
        $currentmodif = filemtime( $filename );

        // Petlja koja se vrti dok ne dobije promjenu vezanu uz ovaj dokument
        while( 1 )
        {
            // Petlja koja se vrti sve dok se datoteka ne promijeni
            while( $currentmodif <= $lastmodif )
            {
                //ovaj usleep treba ostati, dio long pollinga
                usleep( 100000 ); // odspavaj 1000ms da CPU malo odmori :)
                clearstatcache();
                $currentmodif = filemtime( $filename ); // ponovno dohvati vrijeme zadnje promjene datoteke
            }

            $file_info =  explode(",", file_get_contents( $filename ));
            if( $file_info[1] !== $id )
                $lastmodif = $currentmodif;
            else
                break;
        }


        // Kad dođemo do ovdje, znamo da je datoteka bila promijenjena.
        // Spremi njen sadržaj u $response[ 'msg' ] i vrijeme zadnje promjene u $response[ 'timestamp' ]
        $file_info =  explode(",", file_get_contents( $filename ));
        $response = array();
        $response[ 'user' ]      = $file_info[0];
        $response[ 'id_msg' ]    = $file_info[3];
        $response[ 'msg' ]       = $file_info[2];
        $response[ 'time_msg' ]  = gmdate( 'Y-m-d H:i:s', $currentmodif );
        $response[ 'timestamp' ] = $currentmodif;

        // Napravi JSON string od ovog i ispiši ga (tj. pošalji klijentu).
        $this->sendJSONandExit( $response );
    }

    function sendMessage()
    {
        /*
        Ova skripta nam služi i za spremanje novih poruka koje klijent šalje
        i za slanje poruka klijentu koje je netko drugi napisao.
        */

        // File u kojem se nalazi samo zadnja poruka.
        $filename  = '/'.__SITE_PATH . '/../../../chat.log';

        $error = "";

        //provjeri postoji li file
        //i ima li potrebna prava čitanja/pisanja
        if( !file_exists( $filename ) )
            fopen( $filename, 'w+' );
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


        // Ako klijent šalje novu poruku, pospremi ju u datoteku
        $msg = isset( $_GET['msg'] ) ? $_GET['msg'] : '';

        if( $msg != '' )
        {
            //Prvo spremi poruku u bazu podataka,zatim spremi poruku u datoteku (ovo će prebrisati njen sadržaj)
            $cs = new ChatService();
            //echo $_SESSION['document_id'];
            $login_info = explode(",", $_SESSION['login']);
            $id_message = $cs->addMessage($login_info[0], $_SESSION['document_id'], $msg);
            file_put_contents( $filename,$login_info[0] . ',' . $_SESSION['document_id'] . ',' . $msg . ',' . $id_message );

            // Iako klijent zapravo ne treba odgovor kada šalje novu poruku,
            // možemo mu svejeno nešto odgovoriti da olakšamo debugiranje na strani klijenta.
            $response = [];
            $response[ 'msg' ] = $msg;
            $this->sendJSONandExit( $response );
        }
        else
        {
            $response = [];
            $response[ 'error' ] = "Poruka nema definirano polje msg.";

            $this->sendJSONandExit( $response );
        }
    }

    //u log spremamo informacije u obliku:
    //username,id_document,id_line,action,additional
    //action je jedno od: "new", "delete", "editcontent", "lock", "unlock"
    //additional može biti ili id prethodne linije ako je action "new"
    //ili content linije ako je action "editcontentline"
    //ako je action nešto drugo, nema additional
    function saveToDocumentLog( $username, $id_document, $id_line, $action, $additional="" )
    {
        $filename  = '/'.__SITE_PATH . '/../../../document_change.log';
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

        file_put_contents(
            $filename,
            $username . ',' . $id_document . ',' . $id_line
            . ',' . $action . ',' . $additional
        );
    }

    //funkcija pronalazi dokument po id-u
    //u dokumentu pronalazi liniju s id-em $id_previous_line i pamti njen indeks
    //oslobađamo ju za daljnje uređivanje (mijenjamo is_locked i editing)
    //zatim generiramo novi id i kreiramo novi objekt line, i damo prava uređivanja tom korisniku koji ju je kreirao
    //ubacimo liniju u array iza prethodne
    //update-amo dokument
    //pošaljemo klijentu generirani id

    // TRENUTNO SE PRETHODNA LINIJA AUTOMATSKI OSLOBAĐA, BEZ SPREMANJA !!!
    public function addLineToDocument()
    {
        $id_previous_line = $_POST['id_previous_line'];
        $id_document = $_POST['id_document'];
        $username = $_POST['username'];

        $ds = new DocumentService();

        $document = $ds->getDocumentById( $id_document );

        $error = "";
        $response = [];


        if( $document === null )
        {
            $error = "Dokument s id-em " . $id_document . " ne postoji u bazi.";
            $response['error'] = $error;
            $this->sendJSONandExit( $response );
        }

        $previous_line = $ds->findLineInDocumentById( $document, $id_previous_line );

        if( $previous_line === null )
        {
            $error = 'Linija s id-em ' . $id_previous_line . ' ne postoji u dokumentu s id-em.';
            $response['error'] = $error;
            $this->sendJSONandExit( $response );
        }



        //provjeri ima li korisnik pravo dodati novu liniju ovdje
        //da bi imao to pravo, mora imati pravo uređivanje za prethodnu linju
        else if( $previous_line->editing !== $username )
        {
            $error = "User " . $username . " nema pravo dodavati novu liniju na tom mjestu.";
            $response['error'] = $error;
            $this->sendJSONandExit( $response );
        }

        //napravi novi content za dokument
        $current_content = json_decode($document->content);
        $new_content = array();

        //dodaj sve linije do $previous_line u novi content
        for( $i = 0; $i < count( $current_content ); $i++ ) //dokument po defaultu ima jednu praznu liniju (content ne može biti null)
        {
            if ( $current_content[$i]->id_line === $id_previous_line  )
            {
                $index = $i;
                break;
            }
            $new_content[] = $current_content[$i] ;
        }

        //omogući uređivanje prethodne linije i stavi ju u $new_content
        $previous_line->is_locked = 0;
        $previous_line->editing = "";
        $new_content[$index] = $previous_line;

        //generiraj 6-znamenkasti id
        $id = substr(md5(microtime()),rand(0,26),6);

        //dodaj novu liniju
        $new_line = (object) array(
            'id_line' => $id,
            'content' => "",
            'is_locked' => 0,
            'editing' => ""
        );
        $new_content[$index + 1] = $new_line;

        //dodaj preostale linije u novi content dokumenta
        if( $index < count( $current_content ) )
            for( $i = $index + 1; $i < count( $current_content ); $i++ ) //dokument po defaultu ima jednu praznu liniju (content ne može biti null)
                $new_content[] = $current_content[$i] ;

        //zapamti promjene u dokumentu i update-aj dokument
        $document->content = $new_content;
        $document->last_edit_user = $username;
        $ds->updateDocument( $document );

        //spremi promjenu u log
        $this->saveToDocumentLog( $username, $id_document, $id, 'new', $id_previous_line );

        //pošalji klijentu id stvorene linije
        $response['id'] = $id;
        $this->sendJSONandExit( $response );
    }

    public function permissionForEditingLine()
    {
        $id_line = $_POST['id_line'];
        $id_document = $_POST['id_document'];
        $username = $_POST['username'];

        $ds = new DocumentService();
        $document = $ds->getDocumentById( $id_document );

        $error = "";
        $response = [];

        if( $document === null )
        {
            $error = "Dokument s id-em " . $id_document . " ne postoji u bazi.";
            $response['error'] = $error;
            $this->sendJSONandExit( $response );
        }

        $line = $ds->findLineInDocumentById( $document, $id_line );

        if( $line === null )
        {
            $error = 'Linija s id-em ' . $id_line . ' ne postoji u dokumentu s id-em.';
            $response['error'] = $error;
            $this->sendJSONandExit( $response );
        }

        //ako je linija slobodna za uređivanje
        //zaključaj ju i update-aj dokument
        if( $line->is_locked === 0 )
        {
            $line->is_locked = 1;
            $line->editing = $username;
            $document_content = json_decode( $document->content );

            for ( $i = 0; $i < count($document_content); $i++ )
                if( $document_content[$i]->id_line === $id_line )
                {
                    $document_content[$i] = $line;
                    break;
                }

            $document->content = $document_content;
            $document->last_edit_user = $username;
            $ds->updateDocument( $document );

            //spremi promjenu u log
            $this->saveToDocumentLog( $username, $id_document, $id_line, 'lock' );

        }

        //vrati username korisnika koji smije uređivati liniju
        $response = [];
        $response['editing'] = $line->editing;
        $this->sendJSONandExit( $response );
    }

    //funkcija provjerava ima li korisnik pravo mijenjanja linije
    //ako ima, sprema promjenu i oslobađa liniju
    //inače, ne radi ništa
    //klijentu vraća 1 ako spremi, -1 ako ne spremi promjenu
    public function saveLineChangeToDocument()
    {
        $id_line = $_POST['id_line'];
        $id_document = $_POST['id_document'];
        $username = $_POST['username'];
        $line_new_content = $_POST['line_content'];

        /*
        //ako je + ostao u contentu, ne spremamo ga u bazu
        if( $line_new_content[0] === '+' )
        {
            if( $line_new_content[1] === ' ' )
                $line_new_content = substr( $line_new_content, 2 );
            else
                $line_new_content = substr( $line_new_content, 1 );
        }
        */  //ovu provjeru ipak radimo u javascriptu

        $ds = new DocumentService();
        $document = $ds->getDocumentById( $id_document );

        $error = "";
        $response = [];

        if( $document === null )
        {
            $error = "Dokument s id-em " . $id_document . " ne postoji u bazi.";
            $response['error'] = $error;
            $this->sendJSONandExit( $response );
        }

        $line = $ds->findLineInDocumentById( $document, $id_line );

        if( $line === null )
        {
            $error = 'Linija s id-em ' . $id_line . ' ne postoji u dokumentu s id-em.';
            $response['error'] = $error;
            $this->sendJSONandExit( $response );
        }

        if( $line->editing !== $username )
        {
            $response['success'] = -1;
            $this->sendJSONandExit( $response );
        }

        //ako smo dosli do tu, korisnik ima pravo uređivanja linije
        //mijenjamo content linije i oslobađamo ju
        //zatim update-amo dokument u bazi
        $line->content = $line_new_content;
        $line->is_locked = 0;
        $line->editing = "";

        $document_content = json_decode( $document->content );

        for( $i = 0; $i < count( $document_content ); $i++ ) //dokument po defaultu ima jednu praznu liniju (content ne može biti null)
            if ( $document_content[$i]->id_line === $id_line  )
            {
                $document_content[$i] = $line;
                break;
            }

        $document->content = $document_content;
        $document->last_edit_user = $username;
        $ds->updateDocument( $document );

        //spremi promjenu u log
        $this->saveToDocumentLog( $username, $id_document, $id_line, 'editcontent', urlencode( $line_new_content ) );

        $response['success'] = 1;
        $this->sendJSONandExit( $response );
    }

    public function cancelChange()
    {
        $id_line = $_POST['id_line'];
        $id_document = $_POST['id_document'];
        $username = $_POST['username'];

        $ds = new DocumentService();
        $document = $ds->getDocumentById( $id_document );

        $error = "";
        $response = [];

        if( $document === null )
        {
            $error = 'Dokument s id-em ' . $id_document . ' ne postoji u bazi.';
            $response['error'] = $error;
            $this->sendJSONandExit( $response );
        }

        $line = $ds->findLineInDocumentById( $document, $id_line );

        if( $line === null )
        {
            $error = 'Linija s id-em ' . $id_line . ' ne postoji u dokumentu s id-em.';
            $response['error'] = $error;
            $this->sendJSONandExit( $response );
        }

        //ako korisnik nije imao pravo mijenjati liniju,
        if( $line->editing !== $username )
        {
            $error = "Korisnik " . $username . ' nije imao pravo uređivati liniju ' . $id_line . '.';
            $response['error'] = $error;
            $this->sendJSONandExit( $response );
        }

        $document_content = json_decode( $document->content );

        $line->editing = "";
        $line->is_locked = 0;

        for( $i = 0; $i < count( $document_content ); $i++ )
            if( $document_content[$i]->id_line === $id_line )
                $document_content[$i] = $line;

        $document->content = $document_content;
        $ds->updateDocument( $document );

        //spremi promjene u log
        $this->saveToDocumentLog( $username, $id_document, $id_line, 'unlock' );

        //odgovori klijentu
        $response['success'] = 1;
        $this->sendJSONandExit( $response );
    }

    public function deleteLineFromDocument()
    {
        $id_line = $_POST['id_line'];
        $id_document = $_POST['id_document'];
        $username = $_POST['username'];

        $ds = new DocumentService();

        $document = $ds->getDocumentById( $id_document );

        $error = "";
        $response = [];

        if( $document === null )
        {
            $error = "Dokument s id-em " . $id_document . " ne postoji u bazi.";
            $response['error'] = $error;
            $this->sendJSONandExit( $response );
        }

        //Dokument po defaultu mora imati jednu liniju
        //Ako je linija koju korisnik pokušava obrisati jedina linija u dokumentu
        //Ta se linija ipak nece obrisati
        $current_content = json_decode($document->content);
        if( count( $current_content ) === 1 )
        {
            $error = 'Zabranjeno je brisanje jedine linije u dokumentu.';
            $response['error'] = $error;
            $this->sendJSONandExit( $response );
        }

        $line = $ds->findLineInDocumentById( $document, $id_line );

        if( $line === null )
        {
            $error = 'Linija s id-em ' . $id_line . ' ne postoji u dokumentu s id-em.';
            $response['error'] = $error;
            $this->sendJSONandExit( $response );
        }

        //provjeri ima li korisnik pravo obrisati ovu liniju
        else if( $line->editing !== $username )
        {
            $error = 'Korisnik '.  $username . 'nema pravo uređivanja za liniju ' . $id_line . '.';
            $response['error'] = $error;
            $this->sendJSONandExit( $response );
        }

        //napravi novi content za dokument
        $new_content = array();

        //dodaj sve linije osim $line u novi content
        for( $i = 0; $i < count( $current_content ); $i++ ) //dokument po defaultu ima jednu praznu liniju (content ne može biti null)
        {
            if ( $current_content[$i]->id_line !== $id_line  )
                $new_content[] = $current_content[$i] ;
        }

        //zapamti promjene u dokumentu i update-aj dokument
        $document->content = $new_content;
        $document->last_edit_user = $username;
        $ds->updateDocument( $document );

        //spremi promjenu u log
        $this->saveToDocumentLog( $username, $id_document, $id_line, 'delete' );


        //pošalji klijentu informaciju da je brisanje bilo uspješno
        $response['success'] = 1;
        $this->sendJSONandExit( $response );
    }

    public function ajaxSendChangesToUsers()
    {
        $ds = new DocumentService();

        if( isset( $_SESSION['login'] ) )
        {
			set_time_limit(300);
			$us = new UserService();

            $username = $_GET['username'];
            $id_document = $_GET['id_document'];
            session_write_close();

            // File u kojem se nalazi samo zadnje dodani korisnik nekom dokumentu
            $filename  = '/'.__SITE_PATH . '/../../../document_change.log';
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

            //provjerimo postoji li dokument s poslanim id-em
            $document = $ds->getDocumentById( $id_document );
            if( $document === null )
                $error = $error .'Dokument s id-em ' . $id_document . 'ne postoji. ';

            //postoji li user s poslanim username-om
            $user = $us->getUserByUsername( $username );
            if( $user === null )
                $error = $error. 'User s username-om ' . $username . 'ne postoji. ';

            if( $error !== "" )
            {
                $response = [];
                $response[ 'error' ] = $error;

                $this->sendJSONandExit( $response );
            }

            //detektiramo je li neki drugi korisnik napravio promjenu u dokumentu
            //long polling

            // Klijent će u zahtjevu poslati svoje trenutno vrijeme.
            $lastmodif = isset( $_GET['timestamp'] ) ? $_GET['timestamp'] : 0;

            // Otkrij kad je zadnji put neki korisnik dodan u neki dokument
            $currentmodif = filemtime( $filename );

            $relevantchange = 0;
            // Petlja se vrti sve dok promjenu nije napravio neki drugi korisnik
            while( !$relevantchange )
            {
                $relevantchange = 1;
                // Petlja koja se vrti sve dok se datoteka ne promijeni
                while( $currentmodif <= $lastmodif )
                {
                    //ovaj usleep nam treba ostati
                    usleep( 100000 ); // odspavaj da CPU malo odmori :)
                    clearstatcache();
                    $currentmodif = filemtime( $filename ); // ponovno dohvati vrijeme zadnje promjene datoteke
                }

                // Kad dođemo do ovdje, znamo da je datoteka bila promijenjena.
                list( $r_username, $r_id_document, $r_id_line, $r_action, $additional ) = explode(",", file_get_contents( $filename ) );

                // Provjerimo je li promjenu napravio neki drugi korisnik
                // I je li promjena vezana za dokument koji korisnika zanima
                if( ( $r_username === $username ) || ( $r_id_document !== $id_document ) ) {
                    $relevantchange = 0;
                    $lastmodif = $currentmodif;
                }
            }

            // Kad dođemo do ovdje znamo da smo našli relevantnu promjenu.
            // Spremi njen sadržaj u $response[ 'id' ] i $response['title']
            // i vrijeme zadnje promjene u $response[ 'timestamp' ]
            $response = array();

            $response[ 'id_line' ] = $r_id_line;
            $response[ 'action' ] = $r_action;
            $response[ 'username' ] = $r_username;
            $response[ 'timestamp' ] = $currentmodif;

            if( $r_action === 'new' )
                $response[ 'id_previous_line' ] = $additional;
            else if( $r_action === 'editcontent' )
            {
                /*
                usleep( 6000000 );
				$line = $ds->findLineInDocumentById( $document, $r_id_line );
                $response [ 'content' ] = $line->content;
                */
                $response[ 'content' ] = urldecode( $additional );
            }
            // Napravi JSON string od ovog i ispiši ga (tj. pošalji klijentu).
            $this->sendJSONandExit( $response );
        }
    }

    public function unlockUsersLines()
    {
        $login_info = explode( ',', $_SESSION['login'] );
        $username = $login_info[0];

        $us = new UserService();
        $ds = new DocumentService();

        $user = $us->getUserByUsername( $username );
        $document_list = $ds->getDocumentsByIds( $user->documents );

        foreach( $document_list as $document )
        {
            $promjena=0;
            $document_content = json_decode( $document->content );

            for ( $i = 0; $i < count( $document_content ); $i++ )
            {
                if( ( $document_content[$i]->is_locked === 1 ) &&  ( $document_content[$i]->editing === $username ) )
                {
                    $promjena=1;
                    $document_content[$i]->is_locked = 0;
                    $document_content[$i]->editing = "";
                    $id_line = $document_content[$i]->id_line;

                    //spremi promjenu u log
                    $this->saveToDocumentLog( $username, $document->id, $id_line, 'unlock' );
                }
            }
            if($promjena)
            {
                $document->content = $document_content;
                $ds->updateDocument( $document );
            }
        }
        if( isset( $_GET['logout'] ) )
		    header( 'Location: ' . __SITE_URL . '/index.php?rt=users/logout' );
    }

	public function addCollaborator()
	{
		$ds = new DocumentService();

        if( isset( $_SESSION['login'] ) )
        {
            $us = new UserService();

            $username = $_GET['username'];
            $id_document = $_GET['id_document'];
            session_write_close();

			// File u kojem se nalazi samo zadnja poruka.
	        $filename  = '/'.__SITE_PATH . '/../../../add_collaborator_info.log';

	        $error = "";

	        //provjeri postoji li file
	        //i ima li potrebna prava čitanja/pisanja
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
            $user = $us->getUserByUsername( $username );
            if($user === null)
            {
                $response = [];
                $response['success'] = 0;
                $response['username'] = $username;
                $this->sendJSONandExit( $response );
            }
            else
            {
                for ($i = 0; $i < count( $user->documents ); $i++ )
                {
                    if( $id_document === $user->documents[$i] )
                    {
                        $response = [];
                        $response['success'] = -1;
                        $response['username'] = $username;
                        $this->sendJSONandExit( $response );
                    }
                }
            }
            
			$us->addDocumentToUser($id_document, $user);

			file_put_contents( $filename,$username . ',' . $id_document);
			$response = [];
            $response['success'] = 1;
            $response['username'] = $username;
			$this->sendJSONandExit( $response );
        }
    }
};

?>

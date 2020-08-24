Ovo je implementacija igre križić-kružić pomoću MVC okvira razvijenog na predavanjima (varijanta "PHPRO").
Sadržaj foldera app je potpuno isti kao kod svih drugih web-aplikacija nastalih pomoću ovog okvira.
(Eventualno možemo staviti u app/boot "instalaciju" baze; ovdje nema ni toga.)

Postoje:
- 2 kontrolera (indexController i gameController), a svaki ima po dvije akcije.
- 2 viewa -- jedan prikazuje formu za upis imena, a drugi formu za igranje XO (3x3 tablicu s buttonima).

Model ne koristi bazu podataka, nego se sve sprema u $_SESSION.
- Pristup $_SESSION-u je izveden preko singleton klase Session.
- Kontroleri i view uopće ne znaju što koristi model za spremanje stanja igre. Mogli bismo ostaviti potpuno iste
  kontrolere i view, a model promijeniti tako da koristi bazu podataka i sve bi funkcioniralo.



Napomena:
- Inače nije dobra ideja spremati puno podataka u $_SESSION.
- U $_SESSION se obično samo spremi nekakav identifikator $_SESSION['id'] koji serveru jednoznačno određuje korisnika/session.
- Svi ostali podaci se tada spremaju u bazu podataka.
- Tako bi i ovdje inicijalizacija igre mogla postaviti samo $_SESSION['id'], a u bazi bi postojala tablica XOGame sa sljedećim stupcima:
  session_id, playerX_name, playerO_name, currentPlayer, board_string

  board (stanje 3x3 tablice) možemo spremiti kao jedan string u bazu podataka pomoću funkcije serialize().
  Ta funkcija bilo koju PHP varijablu konvertira u string:
  
  $board = array( array( 'x', 'o', '?' ), array( '?', 'o', '?' ), array( '?', '?', 'x' ) ); // (npr.)
  $board_string = serialize( $board );
  // sad spremi $board_string u bazu podataka u VARCHAR dovoljne duljine.

  Suprotno radi funkcija unserialize():

  // dohvati $board_string iz baze podataka
  $board = unserialize( $board_string );
  // sad je opet $board = array( array( 'x', 'o', '?' ), array( '?', 'o', '?' ), array( '?', '?', 'x' ) );

- Kod svake akcije bi model prvo dohvatio $_SESSION['id'] i onda na temelju njega iz baze stanje igre.

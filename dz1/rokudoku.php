<?php
class Rokudoku
{
  protected $imeIgraca, $brojPokusaja, $gameOver, $tablica, $pocetna;
  protected $errorMsg;
  const n=6;
  const tableMedium = array
    (
      array("","",4,"","",""),
      array("","","",2,3,""),
      array(3,"","","",6,""),
      array("",6,"","","",2),
      array("",2,1,"","",""),
      array("","","",5,"","")
    );
  const tableHard = array
    (
      array("","","","","",1),
      array("",4,1,"","",5),
      array("","",5,"","",6),
      array(1,"","",2,"",""),
      array(5,"","",6,1,""),
      array(6,"","","","","")
    );
  const tableEasy = array
    (
      array("","",5,2,6,""),
      array("",2,1,3,"",""),
      array("","",3,1,2,""),
      array("",1,2,4,"",""),
      array("","",6,5,4,""),
      array("",5,4,6,"","")
    );

  function __construct()
  {
    $this->imeIgraca = false;
    $this->brojPokusaja = 0;
    $this->gameOver = false;
    $this->errorMsg = false;
    $this->tablica = Rokudoku::tableMedium;
    $this->pocetna = Rokudoku::tableMedium;
  }

  function ispisiLogin()
  {
    ?>
    <!DOCTYPE html>
    <html lang="hr" dir="ltr">
      <head>
        <meta charset="utf-8">
        <title>Rokudoku - Dobrodošli!</title>
        <link rel="stylesheet" href="rokudoku.css">
      </head>
      <body>
        <h1><b>Rokudoku!</b></h1>
        <form method="post" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>">
          <p>Unesite Vaše ime:
          <input type="text" name="imeIgraca" autofocus>
          </p>
          <p>Odaberite težinu:
          <input type="radio" name="game" value="easy"> Lagano
          <input type="radio" name="game" value="medium" checked> Srednje
          <input type="radio" name="game" value="hard"> Teško
          </p>
          <button type="submit">Započni igru!</button>
        </form>
        <?php if( $this->errorMsg !== false ) echo '<p>Greška: ' . htmlentities( $this->errorMsg ) . '</p>'; ?>
      </body>
    </html>
    <?php
  }

  function bojaCelije($redak,$stupac)
  {
    $boja="blue";
    if (($redak===0)||($redak===1))
    {
      if ($stupac<3)
      {
        if (($this->tablica[$redak][$stupac]===$this->tablica[0][0])&&($redak!==0)&&($redak!==0)) $boja="red";
        elseif (($this->tablica[$redak][$stupac]===$this->tablica[0][1])&&($redak!==0)&&($redak!==1)) $boja="red";
        elseif (($this->tablica[$redak][$stupac]===$this->tablica[0][2])&&($redak!==0)&&($redak!==2)) $boja="red";
        elseif (($this->tablica[$redak][$stupac]===$this->tablica[1][0])&&($redak!==1)&&($redak!==0)) $boja="red";
        elseif (($this->tablica[$redak][$stupac]===$this->tablica[1][1])&&($redak!==1)&&($redak!==1)) $boja="red";
        elseif (($this->tablica[$redak][$stupac]===$this->tablica[1][2])&&($redak!==1)&&($redak!==2)) $boja="red";
      }
      else
      {
        if (($this->tablica[$redak][$stupac]===$this->tablica[0][3])&&($redak!==0)&&($redak!==3)) $boja="red";
        elseif (($this->tablica[$redak][$stupac]===$this->tablica[0][4])&&($redak!==0)&&($redak!==4)) $boja="red";
        elseif (($this->tablica[$redak][$stupac]===$this->tablica[0][5])&&($redak!==0)&&($redak!==5)) $boja="red";
        elseif (($this->tablica[$redak][$stupac]===$this->tablica[1][3])&&($redak!==1)&&($redak!==3)) $boja="red";
        elseif (($this->tablica[$redak][$stupac]===$this->tablica[1][4])&&($redak!==1)&&($redak!==4)) $boja="red";
        elseif (($this->tablica[$redak][$stupac]===$this->tablica[1][5])&&($redak!==1)&&($redak!==5)) $boja="red";
      }
    }
    if (($redak===2)||($redak===3))
    {
      if ($stupac<3)
      {
        if (($this->tablica[$redak][$stupac]===$this->tablica[2][0])&&($redak!==2)&&($redak!==0)) $boja="red";
        elseif (($this->tablica[$redak][$stupac]===$this->tablica[2][1])&&($redak!==2)&&($redak!==1)) $boja="red";
        elseif (($this->tablica[$redak][$stupac]===$this->tablica[2][2])&&($redak!==2)&&($redak!==2)) $boja="red";
        elseif (($this->tablica[$redak][$stupac]===$this->tablica[3][0])&&($redak!==3)&&($redak!==0)) $boja="red";
        elseif (($this->tablica[$redak][$stupac]===$this->tablica[3][1])&&($redak!==3)&&($redak!==1)) $boja="red";
        elseif (($this->tablica[$redak][$stupac]===$this->tablica[3][2])&&($redak!==3)&&($redak!==2)) $boja="red";
      }
      else
      {
        if (($this->tablica[$redak][$stupac]===$this->tablica[2][3])&&($redak!==2)&&($redak!==3)) $boja="red";
        elseif (($this->tablica[$redak][$stupac]===$this->tablica[2][4])&&($redak!==2)&&($redak!==4)) $boja="red";
        elseif (($this->tablica[$redak][$stupac]===$this->tablica[2][5])&&($redak!==2)&&($redak!==5)) $boja="red";
        elseif (($this->tablica[$redak][$stupac]===$this->tablica[3][3])&&($redak!==3)&&($redak!==3)) $boja="red";
        elseif (($this->tablica[$redak][$stupac]===$this->tablica[3][4])&&($redak!==3)&&($redak!==4)) $boja="red";
        elseif (($this->tablica[$redak][$stupac]===$this->tablica[3][5])&&($redak!==3)&&($redak!==5)) $boja="red";
      }
    }
    if (($redak===4)||($redak===5))
    {
      if ($stupac<3)
      {
        if (($this->tablica[$redak][$stupac]===$this->tablica[4][0])&&($redak!==4)&&($redak!==0)) $boja="red";
        elseif (($this->tablica[$redak][$stupac]===$this->tablica[4][1])&&($redak!==4)&&($redak!==1)) $boja="red";
        elseif (($this->tablica[$redak][$stupac]===$this->tablica[4][2])&&($redak!==4)&&($redak!==2)) $boja="red";
        elseif (($this->tablica[$redak][$stupac]===$this->tablica[5][0])&&($redak!==5)&&($redak!==0)) $boja="red";
        elseif (($this->tablica[$redak][$stupac]===$this->tablica[5][1])&&($redak!==5)&&($redak!==1)) $boja="red";
        elseif (($this->tablica[$redak][$stupac]===$this->tablica[5][2])&&($redak!==5)&&($redak!==2)) $boja="red";
      }
      else
      {
        if (($this->tablica[$redak][$stupac]===$this->tablica[4][3])&&($redak!==4)&&($redak!==3)) $boja="red";
        elseif (($this->tablica[$redak][$stupac]===$this->tablica[4][4])&&($redak!==4)&&($redak!==4)) $boja="red";
        elseif (($this->tablica[$redak][$stupac]===$this->tablica[4][5])&&($redak!==4)&&($redak!==5)) $boja="red";
        elseif (($this->tablica[$redak][$stupac]===$this->tablica[5][3])&&($redak!==5)&&($redak!==3)) $boja="red";
        elseif (($this->tablica[$redak][$stupac]===$this->tablica[5][4])&&($redak!==5)&&($redak!==4)) $boja="red";
        elseif (($this->tablica[$redak][$stupac]===$this->tablica[5][5])&&($redak!==5)&&($redak!==5)) $boja="red";
      }
    }
    if ($boja==="blue")
      for( $r = 0; $r < Rokudoku::n; ++$r )
      {
        for( $c = 0; $c < Rokudoku::n; ++$c )
        {
          if (($redak===$r)&&($stupac!==$c)&&($this->tablica[$redak][$stupac]===$this->tablica[$r][$c])) $boja="red";
          elseif (($redak!==$r)&&($stupac===$c)&&($this->tablica[$redak][$stupac]===$this->tablica[$r][$c])) $boja="red";
        }
      }
    return $boja;
  }

  function ispisiFormuZaKorak()
	{
		?>
    <!DOCTYPE html>
    <html lang="hr" dir="ltr">
      <head>
        <meta charset="utf-8">
        <title>Rokudoku - Probaj pogoditi!</title>
        <link rel="stylesheet" href="rokudoku.css">
      </head>
      <body>
        <h1><b>Rokudoku!</b></h1>
        <p>
          Vaše ime: <?php echo htmlentities( $this->imeIgraca ); ?>
          <br>
          <br>
          Vaš broj pokušaja: <?php echo $this->brojPokusaja; ?>
        </p>
  			<br>
        <table>
    		  <?php
    			  for( $r = 0; $r < Rokudoku::n; ++$r )
            {
              echo "<tr>";
              for( $c = 0; $c < Rokudoku::n; ++$c )
              {
                if ($this->tablica[$r][$c]===$this->pocetna[$r][$c])
                  echo "<td><b>" . $this->tablica[$r][$c] . "</b></td>";
                else
                {
                  $boja=$this->bojaCelije($r,$c);?>
                  <td style="color:<?php echo $boja; ?>"><?php echo $this->tablica[$r][$c] . "</td>";
                }
              }
              echo "</tr>";
            }
          ?>
        </table>
        <br>
        <form method="post" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>">
          <input type="radio" name="odabir" value="insert" checked> Unesi broj
          <input type="text" name="uneseniBroj" autofocus> u redak
          <select name="unesi_redak">
            <option value="0">1</option>
            <option value="1">2</option>
            <option value="2">3</option>
            <option value="3">4</option>
            <option value="4">5</option>
            <option value="5">6</option>
          </select> i stupac
          <select name="unesi_stupac">
            <option value="0">1</option>
            <option value="1">2</option>
            <option value="2">3</option>
            <option value="3">4</option>
            <option value="4">5</option>
            <option value="5">6</option>
          </select>
          <br>
          <input type="radio" name="odabir" value="delete"> Obriši broj iz retka
          <select name="obrisi_redak">
            <option value="0">1</option>
            <option value="1">2</option>
            <option value="2">3</option>
            <option value="3">4</option>
            <option value="4">5</option>
            <option value="5">6</option>
          </select> i stupca
          <select name="obrisi_stupac">
            <option value="0">1</option>
            <option value="1">2</option>
            <option value="2">3</option>
            <option value="3">4</option>
            <option value="4">5</option>
            <option value="5">6</option>
          </select>
          <br>
          <input type="radio" name="odabir" value="reset"> Želim sve ispočetka!
          <br>
          <button type="submit">Izvrši akciju!</button>
        </form>
        <?php if( $this->errorMsg !== false ) echo '<p>Greška: ' . htmlentities( $this->errorMsg ) . '</p>'; ?>
      </body>
    </html>
    <?php
    ++$this->brojPokusaja;
	}

  function ispisiCestitku()
  {
    ?>
    <!DOCTYPE html>
    <html lang="hr" dir="ltr">
      <head>
        <meta charset="utf-8">
        <title>Rokudoku - Čestitam!</title>
        <link rel="stylesheet" href="rokudoku.css">
      </head>
      <body>
        <h1><b>Rokudoku!</b></h1>
        <p>
          Čestitke, <?php echo htmlentities( $this->imeIgraca ); ?>!
  			</p>
        <p>
          Uspješno ste riješili Rokudoku u samo <?php echo $this->brojPokusaja; ?> pokušaja!
  			</p>
        <p>
          <a href="https://rp2.studenti.math.hr/~mamatij/dz1/rokudoku.php">Vrati me na početak!</a>
        </p>
      </body>
    </html>
    <?php
  }

  function get_imeIgraca()
	{
		if( $this->imeIgraca !== false ) return $this->imeIgraca;
		if( isset( $_POST['imeIgraca'] ) )
		{
			if( !preg_match( '/^[a-zA-Z]{1,20}$/', $_POST['imeIgraca'] ) )
			{
				$this->errorMsg = 'Ime igrača treba imati između 1 i 20 slova.';
				return false;
			}
			else
			{
				$this->imeIgraca = $_POST['imeIgraca'];
        if( isset( $_POST['game'] ) )
        {
          if ($_POST['game']==="easy") $this->pocetna = Rokudoku::tableEasy;
          elseif ($_POST['game']==="medium") $this->pocetna = Rokudoku::tableMedium;
          elseif ($_POST['game']==="hard") $this->pocetna = Rokudoku::tableHard;
          $this->tablica = $this->pocetna;
        }
        else
        {
  				$this->errorMsg = 'Odaberite težinu.';
  				return false;
  			}
				return $this->imeIgraca;
			}
		}
		return false;
	}

  function obradiPokusaj()
	{
		if( isset( $_POST['odabir'] ) )
		{
      if ($_POST['odabir']==="insert")
        {
          $options = array( 'options' => array( 'min_range' => 1, 'max_range' => 6 ) );
    			if( filter_var( $_POST['uneseniBroj'], FILTER_VALIDATE_INT, $options ) === false )
    			{
    				$this->errorMsg = 'Trebate unijeti cijeli broj između 1 i 6, uključivo.';
    				return false;
    			}
    			else
          {
    				$pokusaj = (int) $_POST['uneseniBroj'];
            $redak = $_POST['unesi_redak'];
            $stupac = $_POST['unesi_stupac'];
            if (($this->tablica[$redak][$stupac]==="")
                ||($this->tablica[$redak][$stupac]!==$this->pocetna[$redak][$stupac]))
            {
              $this->tablica[$redak][$stupac]=$pokusaj;
              $this->provjeriStanjeIgre();
              if ($this->gameOver) return true;
            }
            elseif ($this->tablica[$redak][$stupac]===$this->pocetna[$redak][$stupac])
            {
              $rr=$redak+1;
              $ss=$stupac+1;
              $this->errorMsg = 'U ' . $rr .'. retku i ' . $ss .'. stupcu ne možete unijeti broj ' . $pokusaj . ' jer je to mjesto predodređeno sa brojem ' . $this->pocetna[$redak][$stupac] . '!';
      				return false;
            }
          }
        }
      if ($_POST['odabir']==="delete")
      {
        $redak = $_POST['obrisi_redak'];
        $stupac = $_POST['obrisi_stupac'];
        if ($this->tablica[$redak][$stupac]==="")
        {
          $rr=$redak+1;
          $ss=$stupac+1;
          $this->errorMsg = 'U ' . $rr .'. retku i ' . $ss .'. stupcu ne možete obrisati broj jer na tom mjestu nema broja!';
          return false;
        }
        elseif ($this->tablica[$redak][$stupac]===$this->pocetna[$redak][$stupac])
        {
          $rr=$redak+1;
          $ss=$stupac+1;
          $this->errorMsg = 'U ' . $rr .'. retku i ' . $ss .'. stupcu ne možete obrisati broj ' . $this->tablica[$redak][$stupac] . ' jer je to mjesto predodređeno!';
          return false;
        }
        else $this->tablica[$redak][$stupac]="";
      }
      if ($_POST['odabir']==="reset")
      {
        $this->tablica = $this->pocetna;
        $this->brojPokusaja = 0;
      }
    }
    return false;
	}

  function provjeriStanjeIgre()
  {
    $brojac=0;
    for( $r = 0; $r < Rokudoku::n; ++$r )
    {
      for( $c = 0; $c < Rokudoku::n; ++$c )
        if (($this->tablica[$r][$c]!=="")&&($this->bojaCelije($r,$c)!=="red")) $brojac+=1;
    }
    if ($brojac===36) $this->gameOver=true;
  }

  function isGameOver() { return $this->gameOver; }

  function run()
  {
    $this->errorMsg = false;
    if( $this->get_imeIgraca() === false )
    {
      $this->ispisiLogin();
      return;
    }
    $rez = $this->obradiPokusaj();
    if( $rez === true ) $this->ispisiCestitku();
    else $this->ispisiFormuZaKorak();
  }
};

//"main"
session_start();

if( !isset( $_SESSION['igra'] ) )
{
	$igra = new Rokudoku();
	$_SESSION['igra'] = $igra;
}
else $igra = $_SESSION['igra'];

$igra->run();

if( $igra->isGameOver() )
{
	session_unset();
	session_destroy();
}
else $_SESSION['igra'] = $igra;

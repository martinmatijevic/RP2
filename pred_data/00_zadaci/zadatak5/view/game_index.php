<?php require_once __SITE_PATH . '/view/_header.php'; ?>

<form method="post" action="<?php echo __SITE_URL . '/index.php?rt=game/processBoardForm';?>">
<table>
	<?php

		// Ovdje radi jednostavnosti for-petljama iscrtavamo ploču.
		// Imamo 1 formu sa 9 submit buttona. Svi imaju name="btn", a value su im "00", "01", "02", "10", ..., "22".
		// Dakle ako kliknemo na srednjem (1.) redu na zadnji (2.) gumb, bit će $_POST["btn"]="12".
		// Gumbi na koje je već netko ranije kliknuo su "disabled", tj. na njih se ne može opet kliknuti.
		for( $r = 0; $r < 3; ++$r )
		{
			echo '<tr>';
			for( $c = 0; $c < 3; ++$c )
			{
				echo '<td>';

				// Ispisujemo npr. <button type="submit" name="btn" value="21"
				echo '<button type="submit" name="btn" value="' . $r . $c . '"';

				// Ako je to mjesto već zauzeto ili je igra gotova, ispisujemo " disabled" tako da se ne može više kliknuti na gumb.
				if( $board[$r][$c] !== '?' || $isGameOver )
					echo " disabled";

				// Ispisujemo >x</button> ili >o</button> ili >?</button>, ovisno već što je na tom mjestu na ploči.
				echo '>' . $board[$r][$c] . '</button>';

				echo '</td>';
			}
			echo '</tr>';
		}
	?>
</table>

<?php 
	if( $errorMessage !== false )
		echo '<p>' . $errorMessage . '</p>';
?>

<button type="submit" name="reset" value="">Resetiraj igru!</button>
</form>

<?php require_once __SITE_PATH . '/view/_footer.php'; ?>

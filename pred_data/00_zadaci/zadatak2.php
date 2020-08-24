<?php 
	function nacrtajFormuZaUnosBrojeva( $greska )
	{
		?>
		<!DOCTYPE html>
		 <html>
		 <head>
		 	<meta charset="utf8">
		 	<title>Zadatak 2 - unos</title>
		 </head>
		 <body>
		 	<?php echo '<p>' . $greska . '</p>'; ?>
		 	<form method="post" action="zadatak2.php">
		 		Unesi prvi pribrojnik:
		 		<input type="text" name="prvi" />
		 		<br />

		 		Unesi drugi pribrojnik:
		 		<input type="text" name="drugi" />

		 		<button type="submit">Pošalji</button>
		 	</form>
		 </body>
		 </html> 
		<?php 
	}

	function ispisiZbroj( $prvi, $drugi, $zbroj )
	{
		?>
		<!DOCTYPE html>
		<html>
		<head>
			<meta charset="utf8">
			<title>Zadatak 2 - Ispis</title>
		</head>
		<body>
			<?php 
				echo $prvi . "+" . $drugi . "=" . $zbroj;
			?>
		</body>
		</html> 
		<?php
	}

	if( !isset($_POST['prvi'] ) || !isset($_POST['drugi'] ) )
	{
		nacrtajFormuZaUnosBrojeva('');
		exit;
	}

	if( !preg_match( '/^[0-9]+$/', $_POST['prvi'] ) ||
		!preg_match( '/^[0-9]+$/', $_POST['drugi'] ) )
	{
		nacrtajFormuZaUnosBrojeva( 'Niste unijeli brojeve!');
		exit;
	}

	$prvi = (int)$_POST['prvi'];
	$drugi = (int)$_POST['drugi'];

	$zbroj = $prvi + $drugi;

	ispisiZbroj( $prvi, $drugi, $zbroj );
	exit();
?>



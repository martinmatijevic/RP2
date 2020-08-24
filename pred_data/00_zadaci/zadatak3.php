<?php 
	session_start();

	if( !isset( $_SESSION['zbroj'] ) )
		$_SESSION['zbroj'] = 0;

	if( isset( $_POST['broj'] ) )
	{	
		if( preg_match( '/^[0-9]+$/', $_POST['broj'] ) )
			$_SESSION['zbroj'] += (int) $_POST['broj'];
	}

	if( isset( $_POST['resetiraj'] ) )
	{
		$_SESSION['zbroj'] = 0;
	}
?> 

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Zadatak 3</title>
</head>
<body>
	<p>
		Dosadašnji zbroj je: 
		<?php echo $_SESSION['zbroj'];?>.
	</p>

	<form method="post" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>">
		Unesi novi broj:
		<input type="text" name="broj" />
		<button type="submit">Pošalji!</button>
	</form>

	<form method="post" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>">
		<input type="hidden" name="resetiraj" />
		<button type="submit">Resetiraj zbroj!</button>
	</form>

</body>
</html>

<?php 
	if( !isset($_POST['prvi'] ) || !isset($_POST['drugi'] ) )
	{
		echo 'Treba unijeti brojeve!';
		exit;
	}

	if( !preg_match( '/^[0-9]+$/', $_POST['prvi'] ) ||
		!preg_match( '/^[0-9]+$/', $_POST['drugi'] ) )
	{
		echo 'Niste unijeli brojeve!';
		exit;		
	}

	$prvi = (int)$_POST['prvi'];
	$drugi = (int)$_POST['drugi'];

	$zbroj = $prvi + $drugi;
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf8">
	<title>Zadatak 1 - Obradi</title>
</head>
<body>
	<?php 
		echo $_POST['prvi'] . "+" . $_POST['drugi'] . "=" . $zbroj;
	?>
</body>
</html>

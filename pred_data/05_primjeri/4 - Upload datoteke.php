<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body>
<?php 
	if( $_SERVER['REQUEST_METHOD' ] == 'GET' ) { ?>
		<form method="post" 
		      action="<?php echo htmlentities( $_SERVER['PHP_SELF'] ) ?>"
	 	      enctype="multipart/form-data">
			<input type="file" name="document" /><br />
			<input type="submit" value="Pošalji datoteku" />
		</form>
	<?php 
	} 
	else {
		// Donja naredba ispisuje podatke o poslanoj datoteci, uključujući i privremeno ime
		// ("tmp_name") prije preseljenja sa move_uploaded_file.
		// echo '<pre>'; print_r( $_FILES ); echo '</pre>';

		// Donja naredba ispisuje ime user-a na serveru kojem treba dati prava pisanja u ciljni direktorij.
		// echo 'PHP username = ' . getenv( 'APACHE_RUN_USER' ) . '<br />';
		
		if( isset( $_FILES['document'] ) && ( $_FILES['document']['error'] == UPLOAD_ERR_OK ) ) 
		{
	 		$newPath = '/tmp/' . basename( $_FILES['document']['name'] );
			if( $_FILES['document']['size'] > 2000 )
				echo 'Nije dozvoljeno slati datoteke veće od 2kB.';
			else if( move_uploaded_file( $_FILES['document']['tmp_name'], $newPath ) )
				echo "Datoteka je spremljena u $newPath";
			else
				echo "Ne mogu spremiti dateku u $newPath";
	 	} 
	 	else
			echo "Nije poslan dobar file.";
	}
?>

</body>
</html>




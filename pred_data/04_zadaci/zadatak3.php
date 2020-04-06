<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<title>Zadatak 3</title>
</head>
<body>
	<?php
		function my_sort( &$niz ) // PAZI: & je nužan!
		{
			$n = count( $niz );

			for( $i = 0; $i < $n; ++$i )
				for( $j = $i+1; $j < $n; ++$j )
					if( strcmp( $niz[$i], $niz[$j] ) > 0 )
					{
						$temp = $niz[$i];
						$niz[$i] = $niz[$j];
						$niz[$j] = $temp;
					}
		}

		$n = 10; $str_len = 5;

		// .. Slucajno generiraj n stringova
		for( $i = 0; $i < $n; ++$i )
		{
			// .. Generiraj i-ti string
			$str = '';
			for( $j = 0; $j < $str_len; ++$j )
				$str .= chr( rand( ord('a'), ord('z') )); 

			$polje[$i] = $str;
		}
	?>

	<p>
		Niz prije sortiranja:
		<?php
			for( $i = 0; $i < $n; ++$i )
				echo $polje[$i] . ' ';
		?>
	</p>

	<?php
		// Sortiranje
		my_sort( $polje );
	?>

	<p>
		Niz nakon sortiranja:
		<?php
			for( $i = 0; $i < $n; ++$i )
				echo $polje[$i] . ' ';
		?>
	</p>

</body>
</html>

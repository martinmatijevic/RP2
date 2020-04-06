<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body>
	<pre><?php
		// echo '$_SERVER:'; print_r( $_SERVER ); echo "\n";
		echo '$_GET:';    print_r( $_GET );    echo "\n";
		echo '$_POST:';   print_r( $_POST );   echo "\n";
		echo '$_COOKIE:'; print_r( $_COOKIE ); echo "\n";
		echo '$_ENV:';    print_r( $_ENV );    echo "\n"; ?>
	</pre>

<?php	
	setcookie( 'ime', 'Mirko' );
?>

</body>
</html>


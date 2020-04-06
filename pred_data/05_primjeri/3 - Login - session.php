<?php
// Počinjemo ili nastavljamo sesiju.
session_start();


// Funkcija za provjeru smije li korisnik $user sa šifrom $pass pristupiti stranici.
function validate( $user, $pass ) 
{
	// Popis svih korisnika koji smiju i njihovih šifri. (Ovo se inače dohvaća iz baze podataka.)
	$users = array(
		'pero' => 'perinasifra' ,
		'ana'  => 'aninasifra' );

	if( isset( $users[$user] ) && ( $users[$user] === $pass ) )
		return true;
	else
	 return false;
}


// Ako je u formi poslan neki username i password, provjerimo je li ispravan dodamo element u $_SESSION.
// Ključ elementa je 'login', a vrijednost username,string.
// String dobijemo kao hashiranu vrijednosti usernamea na kraj kojeg smo zalijepili neku tajnu riječ.
$secret_word = 'racunarski praktikum 2!!!';
if( isset( $_POST['username'] ) 
	&& isset( $_POST['password'] ) 
	&& validate( $_POST['username'], $_POST['password'] ) )
{
	$_SESSION['login'] = $_POST['username'] . ',' . md5( $_POST['username'] . $secret_word );
}


// Sad provjeravamo je li definirana $_SESSION['login'].
// Ako je, znači da je korisnik sad (ili ranije u sesiji) prošao validaciju.
// Dohvaćamo vrijednost $_SESSION['login'], te iz nje username.
unset( $username );
if( isset( $_SESSION['login'] ) ) 
{
	list( $c_username, $cookie_hash ) = explode( ',' , $_SESSION['login'] );

	if( md5( $c_username . $secret_word ) === $cookie_hash )
		$username = $c_username;
	else
		echo "Poslan je pokvareni kolačić :)" ;
}


// Sad provjeravamo je li korisnik kliknuo na logout. Ako je, uništavamo sesiju.
if( isset( $username ) && isset( $_POST['logout'] ) ) 
{
	session_unset();
	session_destroy();
	unset( $username );
}
?>

<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body>

<?php
	if( isset( $username ) ) {
		// Ako je korisnik ulogiran, ispiši mu poruku i gumb za logout.
		echo "Dobro došao, $username.<br />"; ?>
		<form method="POST" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>">
			<input type="hidden" name="logout">
			<input type="submit" value="Log Out">
		</form>
		<?php
	} 
	else {
		// Ako nije ulogiran, ispiši mu formu za logiranje. ?>
		<form method="POST" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>">
			Username: <input type="text" name="username"> <br />
			Password: <input type="password" name="password"> <br />
			<input type="submit" value="Log In">
		</form>
		<?php
	}
?>

</body>
</html>



<?php require_once __SITE_PATH . '/view/_header.php'; ?>
<div class="container">
    <h1 class="display-3"> <?php echo $title; ?> </h1>
</div>
<div class="container">
	<form method="post" action="<?php echo __SITE_URL; ?>/index.php?rt=users/loginResults">
		<input type="text" name="username" autofocus placeholder="Username"/>
	    <br>
		<input type="password" name="password" placeholder="Password"/>
		<br>
		<button class="btn btn-warning" type="submit" name="action" value="login">Ulogiraj se!</button>
		<br>
		Novi ste korisnik?
		<button class="btn btn-warning" type="submit" name="action" value="registration">Registriraj se!</button>
	</form>
</div>


<?php require_once __SITE_PATH . '/view/_footer.php'; ?>

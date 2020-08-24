<?php require_once __SITE_PATH . '/view/_header.php'; ?>

<form method="post" action="<?php echo __SITE_URL . '/index.php?rt=index/processNamesForm' ?>">
	Unesite ime igrača x:
	<input type="text" name="nameX" />
	<br />
	Unesite ime igrača o:
	<input type="text" name="nameO" />
	<br /><br />
	<button type="submit">Započni igru!</button>
</form>

<p><?php echo $errorMessage; ?></p>

<?php require_once __SITE_PATH . '/view/_footer.php'; ?>

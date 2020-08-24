<?php require_once __SITE_PATH . '/view/_header.php'; ?>
<div class="container">
    <h1 class="display-3"> <?php echo $title; ?> </h1>
</div>
<div class="container">
    <form method="post" action="<?php echo __SITE_URL; ?>/index.php?rt=users/registrationResults">
        <input type="text" name="firstname" autofocus placeholder="First name"/>
        <br>
        <input type="text" name="lastname" placeholder="Last name"/>
        <br>
    	<input type="text" name="username" placeholder="Username"/>
        <br>
    	<input type="password" name="password" placeholder="Password" />
        <br>
        <input type="text" name="email" placeholder="E-mail" />
        <br>
        <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
        <button class="btn btn-warning" type="submit" name="action" value="registration">Registriraj se!</button><br>
        <button class="btn btn-warning" type="submit" name="action" value="login">Vrati se na formu za login</button>
    </form>
</div>


<?php require_once __SITE_PATH . '/view/_footer.php'; ?>

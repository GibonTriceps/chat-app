<div class="logincontainer">
	<form method="post" action="">
		<?php
		if (!empty($badAttempt)) {
			echo "<p>{$badAttempt}</p>";
		}
		?>
		<input type="text" name="user_login" placeholder="Login">
		<input type="password" name="user_password" placeholder="Hasło">
		<input type="submit" name="submitLogin" value="Zaloguj się!">
	</form>
</div>
<?php
require_once 'init.php';
ob_start();
if(isset($_POST['submitLogin'])){
	$badAttempt = $users->login();
}
require_once 'include/header.php';
require_once 'include/container.php';
if ($users->isLogged()) {
	require_once 'include/chat.php';
}
else {
	require_once 'include/login.php';
}
require_once 'include/footer.php';
ob_end_flush();
?>
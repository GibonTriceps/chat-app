<?php
require_once 'init.php';
if($users->isLogged()) {
	if(isset($_POST['action']) && $_POST['action'] === 'check_rooms') {
		$rooms = $chatrooms->showRooms();
		echo json_encode($rooms);
	}
	if(isset($_POST['action']) && $_POST['action'] === 'check_users') {
		$roomusers = $chatrooms->showUsers();
		echo json_encode($roomusers);
	}
	if(isset($_POST['action']) && $_POST['action'] === 'update_messages') {
		$messages = $chatrooms->showMessages();
		echo json_encode($messages);
	}
	if(isset($_POST['action']) && $_POST['action'] === 'old_messages') {
		$messages = $chatrooms->showMessages('<');
		echo json_encode($messages);
	}
	if(isset($_POST['action']) && $_POST['action'] === 'send_message') {
		$chatrooms->sendMessages();
	}
	if(isset($_POST['action']) && $_POST['action'] === 'add_group') {
		$chatrooms->newGroup();
	}
	if(isset($_POST['action']) && $_POST['action'] === 'add_user') {
		$chatrooms->newGroupUser();
	}
	if(isset($_POST['action']) && $_POST['action'] === 'send_image') {
		$chatrooms->sendImage();
	}				
}
?>
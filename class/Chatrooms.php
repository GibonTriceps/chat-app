<?php

class Chatrooms extends Database {
	use AccessTrait;
	private $dbConnect = false;
	public function __construct() {
		$this->dbConnect = $this->dbConnect();
	}
	public function showRooms() {
		$chatroomsSend = array();
		$chatroomsQuery = $this->dbConnect->prepare('
			SELECT 
				accessrooms.*,
				chatrooms.roomName,
				chatrooms.lastMessage
			FROM accessrooms 
			LEFT JOIN chatrooms
				ON chatrooms.id = accessrooms.roomId
			WHERE userId = :userLogin
			ORDER BY lastMessage DESC
		');
		$chatroomsQuery->bindValue(':userLogin', $_SESSION['user_id'], PDO::PARAM_INT);
		$chatroomsQuery->execute();
		$chatrooms = $chatroomsQuery->fetchAll();
		$roomOrder = 1;
		foreach($chatrooms as $chat) {
			$nameSend = $chat['roomName'];
			if(empty($chat['roomName'])){
				$roomnameQuery = $this->dbConnect->prepare('
					SELECT 
						accessrooms.*,
						users.displayName
					FROM accessrooms 
					LEFT JOIN users
						ON users.id = accessrooms.userId
					WHERE roomId = :roomId AND userId != :userLogin
					LIMIT 1
				');
				$roomnameQuery->bindValue(':userLogin', $_SESSION['user_id'], PDO::PARAM_INT);
				$roomnameQuery->bindValue(':roomId', $chat['roomId'], PDO::PARAM_INT);
				$roomnameQuery->execute();
				$roomName = $roomnameQuery->fetch();
				if(!empty($roomName)){
					$nameSend = $roomName['displayName'];
				}
			}
			$rowsSend = [
				'roomId' => $chat['roomId'],
				'roomName' => $nameSend,
				'roomOrder' => 'order: '.$roomOrder.';'
			];
			$chatroomsSend[] = $rowsSend;
			$roomOrder++;
		}
		return [
			'chatRooms' => $chatroomsSend
		];
	}
	public function accessRoom($roomId, $userId) {
		$accessQuery = $this->dbConnect->prepare('
			SELECT * FROM accessrooms WHERE roomId = :roomId AND userId = :userId
		');
		$accessQuery->bindValue(':roomId', $roomId, PDO::PARAM_INT);
		$accessQuery->bindValue(':userId', $userId, PDO::PARAM_INT);
		$accessQuery->execute();
		$access = $accessQuery->fetch();
		if(!$access){
			return false;
		}
		else {
			return true;
		}
	}
	public function showUsers() {
		$roomId = filter_input(INPUT_POST, 'room_id', FILTER_SANITIZE_NUMBER_INT);
		$userId = $_SESSION['user_id'];
		if($this->accessRoom($roomId, $userId)) {
			$usersQuery = $this->dbConnect->prepare('
				SELECT 
					accessrooms.*,
					users.displayName
				FROM accessrooms
				LEFT JOIN users
					ON users.id = accessrooms.userId
				WHERE
					roomId = :roomId
			');
			$usersQuery->bindValue(':roomId', $roomId, PDO::PARAM_INT);
			$usersQuery->execute();
			$roomUsers = $usersQuery->fetchAll();

			return [
				'roomUsers' => $roomUsers
			];
		}
	}
	public function showMessages($compare = '>', $messLimit = 10) {
		$roomId = filter_input(INPUT_POST, 'room_id', FILTER_SANITIZE_NUMBER_INT);
		$currentId = filter_input(INPUT_POST, 'current_id', FILTER_SANITIZE_NUMBER_INT);
		$userId = $_SESSION['user_id'];
		if($this->accessRoom($roomId, $userId)) {
			$messagesQuery =  $this->dbConnect->prepare('
				SELECT 
					messages.*,
					users.displayName
				FROM messages 
				LEFT JOIN users 
					ON users.id = messages.userId
				WHERE roomId = :roomId
					AND messages.id '.$compare.' :currentid
				ORDER BY messages.id DESC
				LIMIT :messLimit
			');
			$messagesQuery->bindValue(':roomId', $roomId, PDO::PARAM_INT);
			$messagesQuery->bindValue(':currentid', $currentId, PDO::PARAM_INT);
			$messagesQuery->bindValue(':messLimit', $messLimit, PDO::PARAM_INT);
			$messagesQuery->execute();

			$currentMessage = $currentId;
			if($compare === '>') {
				$messages = array_reverse($messagesQuery->fetchAll());
				foreach($messages as $lastmess) {
					if($lastmess['id'] > $currentMessage) {
						$currentMessage = $lastmess['id'];
					}
				}
			}
			if($compare === '<') {
				$messages = $messagesQuery->fetchAll();
				foreach($messages as $lastmess) {
					if($lastmess['id'] < $currentMessage) {
						$currentMessage = $lastmess['id'];
					}
				}
			}
			return [
				'messagesTable' => $messages,
				'messagesCurrent' => $currentMessage
			];
		}
	}
	public function sendMessages() {
		$userId = $_SESSION['user_id'];
		$roomId = filter_input(INPUT_POST, 'room_id', FILTER_SANITIZE_NUMBER_INT);
		$rawContent = trim(filter_input(INPUT_POST, 'message_content'));
		$messageContent = filter_var($rawContent, FILTER_SANITIZE_SPECIAL_CHARS);
		if($this->accessRoom($roomId, $userId) && !empty($messageContent)) {
			$this->messageQuery($userId, $roomId, $messageContent);
		}
	}
	public function messageQuery($userId, $roomId, $messageContent, $isImage = 0) {
		$currentDate = date('Y-m-d H:i:s');

		$messQuery = $this->dbConnect->prepare('
				INSERT INTO messages (
						userId, 
						roomId, 
						message,
						isImage
					)
					VALUES (
						:userId, 
						:roomId,
						:message,
						:isImage
					)
			');
			$messQuery->bindValue(':userId', $userId, PDO::PARAM_INT);
			$messQuery->bindValue(':roomId', $roomId, PDO::PARAM_INT);
			$messQuery->bindValue(':message', $messageContent, PDO::PARAM_STR);
			$messQuery->bindValue(':isImage', $isImage, PDO::PARAM_BOOL);
			$messQuery->execute();

			$groupMessQuery = $this->dbConnect->prepare('
				UPDATE chatrooms SET lastMessage = :currentDate WHERE id = :roomId
			');
			$groupMessQuery->bindValue(':roomId', $roomId, PDO::PARAM_INT);
			$groupMessQuery->bindValue(':currentDate', $currentDate, PDO::PARAM_STR);
			$groupMessQuery->execute();
	}
	public function newGroup() {
		$rawroomName = trim(filter_input(INPUT_POST, 'room_name'));
		$roomName =  filter_var($rawroomName, FILTER_SANITIZE_SPECIAL_CHARS);
		$userId = $_SESSION['user_id'];
		$currentDate = date('Y-m-d H:i:s');

		if(!empty($roomName)) {
			$newgroupQuery = $this->dbConnect->prepare('
				INSERT INTO chatrooms (
					creatorId, 
					roomName,
					lastMessage
				)
				VALUES (
					:userId, 
					:roomName,
					:currentDate
				)
			');
			$newgroupQuery->bindValue(':userId', $userId, PDO::PARAM_INT);
			$newgroupQuery->bindValue(':roomName', $roomName, PDO::PARAM_STR);
			$newgroupQuery->bindValue(':currentDate', $currentDate, PDO::PARAM_STR);
			$newgroupQuery->execute();

			$this->grantAccess($userId, $this->dbConnect->lastInsertId());
		}
	}
	public function grantAccess($userId, $roomId) {
		$accessCheckQuery = $this->dbConnect->prepare('
			SELECT * FROM accessrooms WHERE userId = :userId AND roomId = :roomId
		');
		$accessCheckQuery->bindValue('roomId',$roomId,PDO::PARAM_INT);
		$accessCheckQuery->bindValue('userId',$userId,PDO::PARAM_INT);
		$accessCheckQuery->execute();
		$accessExist = $accessCheckQuery->fetch();

		if(empty($accessExist)) {
			$roomAccesQyery = $this->dbConnect->prepare('
				INSERT INTO accessrooms (
					roomId, 
					userId
				)
				VALUES (
					:roomId, 
					:userId
				)
			');
			$roomAccesQyery->bindValue('roomId', $roomId,PDO::PARAM_INT);
			$roomAccesQyery->bindValue(':userId', $userId, PDO::PARAM_STR);
			$roomAccesQyery->execute();
		}
	}
	public function newGroupUser() {
		$rawUsername = trim(filter_input(INPUT_POST, 'user_name'));
		$username = filter_var($rawUsername, FILTER_SANITIZE_SPECIAL_CHARS);
		$roomId = filter_input(INPUT_POST, 'room_id', FILTER_SANITIZE_NUMBER_INT);
		$loggedUser = $_SESSION['user_id'];

		$userQuery = $this->dbConnect->prepare('
			SELECT * FROM users WHERE displayName = :username
		');
		$userQuery->bindValue(':username', $username, PDO::PARAM_STR);
		$userQuery->execute();
		$userFetch = $userQuery->fetch();

		if(!empty($userFetch) && $this->accessRoom($roomId, $loggedUser)) {
			$userId = $userFetch['id'];
			$this->grantAccess($userId, $roomId);
		}
	}
	public function sendImage() {
		$userId = $_SESSION['user_id'];
		$roomId = filter_input(INPUT_POST, 'room_id', FILTER_SANITIZE_NUMBER_INT);
		if(empty($_FILES['file'])) {
		}
		else if(!$this->accessRoom($roomId, $userId)) {
		}
		else {
			$target_dir = 'img/';
			$imageFileType = strtolower(pathinfo(basename($_FILES['file']['name']),PATHINFO_EXTENSION));
			$target_file = '';
			$check = getimagesize($_FILES['file']['tmp_name']);
			do {
				$target_file = $target_dir . uniqid('contentfile') . '.' .  $imageFileType;
			} while (file_exists($target_file));
			
			if($check === false) {
			}
			else {
				move_uploaded_file($_FILES['file']['tmp_name'], $target_file);
				$this->messageQuery($userId, $roomId, $target_file, 1);
			}
		}
	}
}
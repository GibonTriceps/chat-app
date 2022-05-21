$(document).ready(function(){
	setInterval(function(){
		checkRooms();
		loadMessages();
		checkUsers();
	}, 1000);
	$(document).on('click', '.roomLink', function() {
		room_id = $(this).attr('room-id');
		room_title = $(this).text();
		clearChat();
		loadRoom(room_id);
		loadTitle(room_title, room_id);
		$('#list-users').text('');
		checkUsers();
	});
	$(document).on('click', '#chat-title', function() {
		loadOldMessages();
	});
	$(document).on('submit', '#message-form', function(evt) {
		evt.preventDefault();
		sendMessages();
		uploadImage(fileVariable);	
		clearChat();
		loadMessages();
	});
	$(document).on('submit', '#new-chat-form', function() {
		addRoom();
		$('#new-chat-name').val('');
		checkRooms();
	});
	$(document).on('submit', '#user-chat-form', function() {
		addRoomUser();
		$('#user-chat-name').empty();
	});
	document.querySelector("#message-content").addEventListener('paste', e => {
		console.log(e.clipboardData.files);
		if(e.clipboardData.files.length > 0) {
			$('#fileToUpload')[0].files = e.clipboardData.files;
			fileVariable = $('#fileToUpload')[0].files[0];
			setPreviewImage(fileVariable);
		}
	});
	document.querySelector('#fileToUpload').addEventListener('change', function() {
		setPreviewImage($('#fileToUpload')[0].files[0]);
	});
});
let fileVariable = '';

function scrollToBottom() {
	const messages = document.getElementById('message-container');
	console.log(messages.scrollHeight);
	messages.scrollTo(0, messages.scrollHeight);
  }
function checkUsers() {
	var room_id = $('#chat-container').attr('room-id');
	$.ajax({
		url:"chat_action.php",
		method:"POST",
		data:{room_id:room_id, action:'check_users'},
		dataType: "json",
		success:function(response) {
			if(response !== null){
				if(response.roomUsers !== '') {
					response.roomUsers.forEach(function(eachUser) {
						if($('#userid' + eachUser['userId']).length) {
							if($('#userid' + eachUser['userId']).text() !== eachUser['displayName']) {
								$('#userid' + eeachUser['userId']).html(eachUser['displayName']);
							}
						}
						else {
							$('#list-users').append('<div class="roomUser" id="userid' + eachUser['userId'] + '">' + eachUser['displayName'] + '</div>');
						}
					});
				}
			}
		}
	});
}
function checkRooms() {
	$.ajax({
		url:"chat_action.php",
		method:"POST",
		data:{action:'check_rooms'},
		dataType: "json",
		success:function(response) {
			if(response.chatRooms !== '') {
				response.chatRooms.forEach(function(eachRoom) {
					if($('#roomid' + eachRoom['roomId']).length) {
						if($('#roomid' + eachRoom['roomId']).text() !== eachRoom['roomName']) {
							$('#roomid' + eachRoom['roomId']).html(eachRoom['roomName']);
						}
						if($('#roomid' + eachRoom['roomId']).attr('style') !== eachRoom['roomOrder']) {
							$('#roomid' + eachRoom['roomId']).attr('style', eachRoom['roomOrder']);
						}
					}
					else {
						$('#list-chats').append('<div class="roomLink" id="roomid' + eachRoom['roomId'] + '" room-id="'+eachRoom['roomId']+'" style="' + eachRoom['roomOrder'] + '">' + eachRoom['roomName'] + '</div>');
					}
					loadTitle(eachRoom['roomName'], eachRoom['roomId']);
				});
			}
		}
	});
}
function loadTitle(room_title, selected_room) {
	if($('#chat-container').attr('room-id') === selected_room) {
		if($('#chat-title').text() !== room_title) {
			$('#chat-title').html(room_title);
		}
	}
}
function loadRoom(room_id) {
	$('#chat-container').attr('room-id', room_id);
	$('#chat-container').attr('message-current', 0);
	$('#chat-container').attr('message-old', 0);
	$('#message-container').empty();
	loadMessages();
}
function changeHeight(id_img) {
	scrollToBottom();
}
function loadMessages() {
	var room_id = $('#chat-container').attr('room-id');
	var current_id = $('#chat-container').attr('message-current');
	$.ajax({
		url:"chat_action.php",
		method:"POST",
		data:{room_id:room_id, current_id:current_id, action:'update_messages'},
		dataType: "json",
		success:function(response){
			if(response !== null){
				if(response.messagesTable !== '' && response.messagesCurrent > current_id){
					response.messagesTable.forEach(function(eachMessage) {
						if(!$('#messagenum'+eachMessage['id']).length) {
							if(eachMessage['isImage']) {
								$('#message-container').append('<div id="messagenum'+eachMessage['id']+'"class="message-class"><b>' + eachMessage['displayName'] + ':</b>' + '<img id="image'+eachMessage['id']+'" src="'+ eachMessage['message'] +'" onload="changeHeight('+eachMessage['id']+')"></div>');
							}
							else {
								$('#message-container').append('<div id="messagenum'+eachMessage['id']+'"class="message-class"><b>' + eachMessage['displayName'] + ':</b>' + eachMessage['message'] + '</div>');
							}
						}
						if($('#messagenum'+eachMessage['id']).length) {
							scrollToBottom();
						}
					});
					$('#chat-container').attr('message-current', response.messagesCurrent);
					if($('#chat-container').attr('message-old') == 0) {
						$('#chat-container').attr('message-old', response.messagesCurrent);
					}
				}
			}
		}
	});
}
function addRoom() {
	var room_name = $('#new-chat-name').val();
	$.ajax({
		url:"chat_action.php",
		method:"POST",
		data:{room_name:room_name, action:'add_group'},
		dataType: "json",
	});
}
function addRoomUser() {
	var user_name = $('#user-chat-name').val();
	var room_id = $('#chat-container').attr('room-id');
	$.ajax({
		url:"chat_action.php",
		method:"POST",
		data:{user_name:user_name, room_id:room_id, action:'add_user'},
		dataType: "json",
	});
}
function sendMessages() {
	var room_id = $('#chat-container').attr('room-id');
	var message_content = $("#message-content").val()
	$.ajax({
		url:"chat_action.php",
		method:"POST",
		data:{room_id:room_id, message_content:message_content, action:'send_message'},
		dataType: "json",
	});
}
function uploadImage(fileTwo) {
	var room_id = $('#chat-container').attr('room-id');
	var file = $('#fileToUpload')[0].files[0];
	var formData = new FormData();
	if(file === undefined) {
		file = fileTwo;
	}
	formData.append('file', file);
	formData.append('action', 'send_image');
	formData.append('room_id', room_id);
	$.ajax({
		url : 'chat_action.php',
		type : 'POST',
		data : formData,
		processData: false,  
		contentType: false,  
	});
}
function setPreviewImage(file) {
	const fileReader = new FileReader();

	fileReader.readAsDataURL(file);
	fileReader.onload = function () {
		$('#message-prev').attr('src', fileReader.result);
	}
 }
 function clearChat() {
	$('#message-prev').attr('src', '');
	$('#message-content').val('');
	$('#fileToUpload').val('');
	fileVariable = '';
 }
 function loadOldMessages() {
	var room_id = $('#chat-container').attr('room-id');
	var current_id = $('#chat-container').attr('message-old');
	$.ajax({
		url:"chat_action.php",
		method:"POST",
		data:{room_id:room_id, current_id:current_id, action:'old_messages'},
		dataType: "json",
		success:function(response){
			if(response !== null){
				if(response.messagesTable !== '' && response.messagesCurrent < current_id){
					response.messagesTable.forEach(function(eachMessage) {
						if(!$('#messagenum'+eachMessage['id']).length) {
							if(eachMessage['isImage']) {
								$('#message-container').prepend('<div id="messagenum'+eachMessage['id']+'"class="message-class"><b>' + eachMessage['displayName'] + ':</b>' + '<img src="'+ eachMessage['message'] +'"></div>');
							}
							else {
								$('#message-container').prepend('<div id="messagenum'+eachMessage['id']+'"class="message-class"><b>' + eachMessage['displayName'] + ':</b>' + eachMessage['message'] + '</div>');
							}
						}
					});
					$('#chat-container').attr('message-old', response.messagesCurrent);
				}
			}
		}
	});
 }
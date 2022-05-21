<div class="left-menu" id="left-list">
	<div>
		<form action="javascript:void(0);" id="new-chat-form">
			<section>
				<input type="text" id="new-chat-name" name="new-chat-name" placeholder="Nazwa grupy">
			</section>
			<section>
				<input type="submit" name="new-chat-submit" id="new-chat-submit" value="">
			</section>
		</form>
	</div>
	<div id="list-chats" class="rooms-list"></div>
</div>
<div id="chat-container" class="chat-container" room-id="none" message-current="0" message-old="0">
	<div id="chat-title" class="chat-title">
	</div>
	<div id="message-container" class="message-container">
	</div>
	<div id="chat-message" class="chat-message">
		<form action="" id="message-form">
			<textarea name="message-content" id="message-content" id="message-content" placeholder="Aa"></textarea>
			<input type="hidden" name="chat-id" value="">
			<input type="file" name="fileToUpload" id="fileToUpload">
			<input type="submit" name="message-button" id="message-button" value="">
		</form>
	</div>
</div>
<div class="right-menu" id="right-list">
	<div>
		<form action="javascript:void(0);" id="user-chat-form">
			<section>
				<input type="text" id="user-chat-name" name="user-chat-name" placeholder="Nazwa użytkownika">
			</section>
			<section>
				<input type="submit" name="user-chat-submit" id="user-chat-submit" value="">
			</section>
		</form>
	</div>
	<div id="list-users" class="rooms-list"></div>
</div>
<script src="js/chatRooms.js"></script>
<img alt="preview" id="message-prev">
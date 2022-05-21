<?php
trait AccessTrait {
	public function isLogged() {
		if(isset($_SESSION['user_id'])) {
			return true;
		}
		else {
			return false;
		}
	}
}
<?php

class Users extends Database {
	use AccessTrait;
	private $dbConnect = false;
	public function __construct() {
		$this->dbConnect = $this->dbConnect();
	}
	public function login() {
		$badAttempt = '';
		if(!$this->isLogged()){
			if(!empty($_POST['user_login']) && !empty($_POST['user_password'])) {
				$userLogin = filter_input(INPUT_POST, 'user_login', FILTER_SANITIZE_SPECIAL_CHARS);
				$userPassword = filter_input(INPUT_POST, 'user_password');
				
				$userQuery = $this->dbConnect->prepare('
					SELECT * FROM users WHERE username = :userLogin
				');
				$userQuery->bindValue(':userLogin', $userLogin, PDO::PARAM_STR);
				$userQuery->execute();
				$user = $userQuery->fetch();
				
				if(!empty($user) && password_verify($userPassword, $user['passwd'])) {
					$_SESSION['user_id'] = $user['id'];
					$_SESSION['user_login'] = $user['username'];
				}
				else {
					$badAttempt = 'Niepoprawny login lub hasło';
				}
			}
			else {
				$badAttempt = 'Proszę wprowadzić login oraz hasło';
			}
		}
		return $badAttempt;
	}
}
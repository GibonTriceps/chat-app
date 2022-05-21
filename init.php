<?php
session_start();
$config = require_once 'config.php';
define('HOST', $config['host']);
define('USER', $config['user']);
define('PASSWORD', $config['password']);
define('DATABASE', $config['database']);
require_once 'class/Database.php';
require_once 'class/AccessTrait.php';
require_once 'class/Users.php';
require_once 'class/Chatrooms.php';
$database = new Database;
$chatrooms = new Chatrooms;
$users = new Users;
<?php

require_once(__DIR__."/../model/User.php");
require_once(__DIR__."/../model/UserMapper.php");
require_once(__DIR__."/BaseRest.php");

/**
* Class UserRest
*
* It contains operations for adding and check users credentials.
* Methods gives responses following Restful standards. Methods of this class
* are intended to be mapped as callbacks using the URIDispatcher class.
*
*/
class UserRest extends BaseRest {
	private $userMapper;

	public function __construct() {
		parent::__construct();

		$this->userMapper = new UserMapper();
	}

	public function postUser() {
		
		try {
			if ( !isset($_FILES['img'])){
				$errors = array();
				$errors["image"] = "Image neccesary";
				http_response_code(400);
				echo(json_encode($errors));
				return;
			}
				
			$user = new User(NULL,$_POST['name'], $_POST['surname'], $_POST['login'], $_POST['password'], $_FILES['img']);
			$user->checkIsValidForRegister();

			$this->userMapper->save($user);

			header($_SERVER['SERVER_PROTOCOL'].' 201 Created');
			header("Location: ".$_SERVER['REQUEST_URI']."/".$_POST['login']);
		}catch(ValidationException $e) {
			http_response_code(400);
			header('Content-Type: application/json');
			echo(json_encode($e->getErrors()));
		}
	}

	public function login() {
		$currentLogged = parent::authenticateUser();
		
		if (!isset($currentLogged)) {
			header($_SERVER['SERVER_PROTOCOL'].' 403 Forbidden');
			echo("You are not authorized to login as anyone but you");
		} else {
			header($_SERVER['SERVER_PROTOCOL'].' 200 Ok');
			echo("Hello ");
		}
	}

	public function getUser() {
		$currentLogged = parent::authenticateUser();
		
		$user = $this->userMapper->getUser($currentLogged->getLogin());
		
		header($_SERVER['SERVER_PROTOCOL'].' 200 Ok');
		header('Content-Type: application/json');
        echo(json_encode(array(
			'name' => $user->getName(),
			'surname' => $user->getSurname(), 
			'login' => $user->getLogin(), 
			'image' => $user->getImage()
		)));
	}
}

// URI-MAPPING for this Rest endpoint
$userRest = new UserRest();
URIDispatcher::getInstance()
->map("GET",	"/user", array($userRest,"login"))
->map("POST", "/user", array($userRest,"postUser"))
->map("GET", "/userinfo", array($userRest,"getUser"));

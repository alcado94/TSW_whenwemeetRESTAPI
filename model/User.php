<?php
// file: model/User.php

require_once(__DIR__."/../core/ValidationException.php");

/**
* Class User
*
* Represents a User in the blog
*
* @author lipido <lipido@gmail.com>
*/
class User {

	private $id;
	/**
	* The user name of the user
	* @var string
	*/
	private $name;

	/**
	* The user name of the user
	* @var string
	*/
	private $surname;

	/**
	* The user name of the user
	* @var string
	*/
	private $login;

	/**
	* The password of the user
	* @var string
	*/
	private $passwd;
	
	/**
	* The image of the user
	* @var string
	*/
	private $image;

	/**
	* The constructor
	*
	* @param string $name The name of the user
	* @param string $surname The surname of the user
	* @param string $login The login of the user
	* @param string $passwd The password of the user
	*/
	public function __construct($id=NULL,$name=NULL, $surname=NULL, $login=NULL, $passwd=NULL, $image=NULL) {
		$this->id = $id;
		$this->name = $name;
		$this->surname = $surname;
		$this->login = $login;
		$this->passwd = $passwd;
		$this->image = $image;
	}

	public function getId() {
		return $this->id;
	}

	/**
	* Gets the name of this user
	*
	* @return string The Name of this user
	*/
	public function getName() {
		return $this->name;
	}

	/**
	* Sets the name of this user
	*
	* @param string $name The name of this user
	* @return void
	*/
	public function setName($name) {
		$this->name = $name;
	}

	/**
	* Gets the surname of this user
	*
	* @return string The SurName of this user
	*/
	public function getSurname() {
		return $this->surname;
	}

	/**
	* Sets the surname of this user
	*
	* @param string $surname The Surname of this user
	* @return void
	*/
	public function setSurname($surname) {
		$this->surname = $surname;
	}

	/**
	* Gets the login of this user
	*
	* @return string The login of this user
	*/
	public function getLogin() {
		return $this->login;
	}

	/**
	* Sets the login of this user
	*
	* @param string $login The login of this user
	* @return void
	*/
	public function setLogin($login) {
		$this->login = $login;
	}

	/**
	* Gets the password of this user
	*
	* @return string The password of this user
	*/
	public function getPasswd() {
		return $this->passwd;
	}
	/**
	* Sets the password of this user
	*
	* @param string $passwd The password of this user
	* @return void
	*/
	public function setPassword($passwd) {
		$this->passwd = $passwd;
	}
	
	/**
	* Gets the image of this user
	*
	* @return string The image of this user
	*/
	public function getImage() {
		return $this->image;
	}
	/**
	* Sets the image of this user
	*
	* @param string $image The image of this user
	* @return void
	*/
	public function setImage($image) {
		$this->image = $image;
	}

	/**
	* Checks if the current user instance is valid
	* for being registered in the database
	*
	* @throws ValidationException if the instance is
	* not valid
	*
	* @return void
	*/
	public function checkIsValidForRegister() {
		$errors = array();
		if (strlen($this->name) < 1) {
			$errors["name"] = "name must be at least 1 characters length";
		}
		if (strlen($this->surname) < 1) {
			$errors["surname"] = "surname must be at least 1 characters length";
		}
		if (strlen($this->login) < 1) {
			$errors["login"] = "login must be at least 1 characters length";
		}
		if (strlen($this->passwd) < 1) {
			$errors["passwd"] = "Password must be at least 1 characters length";
		}
		if ($this->image == NULL ) {
			$errors["image"] = "Image neccesary";
		}
		if (sizeof($errors)>0){
			throw new ValidationException($errors, "user is not valid");
		}
	}
}

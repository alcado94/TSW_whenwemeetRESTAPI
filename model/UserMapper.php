<?php
// file: model/UserMapper.php

require_once(__DIR__."/../core/PDOConnection.php");

/**
* Class UserMapper
*
* Database interface for User entities
*
* @author lipido <lipido@gmail.com>
*/
class UserMapper {

	/**
	* Reference to the PDO connection
	* @var PDO
	*/
	private $db;

	public function __construct() {
		$this->db = PDOConnection::getInstance();
	}

	/**
	* Saves a User into the database
	*
	* @param User $user The user to be saved
	* @throws PDOException if a database error occurs
	* @return void
	*/
	public function save($user) {
		$stmt = $this->db->prepare("INSERT INTO usuarios (nombre,apellidos,login,contrasena) values (?,?,?,?)");
		$stmt->execute(array($user->getName(), $user->getSurname(), $user->getLogin(), $user->getPasswd()));
		
		$id = $this->db->lastInsertId();
		$ruta = "../Files/".$id.".jpg";
				
		$stmt = $this->db->prepare("UPDATE usuarios SET img=? WHERE idusuarios=?");
		$stmt->execute(array($ruta,$id));
		
		move_uploaded_file($user->getImage()['tmp_name'],$ruta);
	}

	/**
	* Checks if a given username is already in the database
	*
	* @param string $username the username to check
	* @return boolean true if the username exists, false otherwise
	*/
	public function usernameExists($username) {
		$stmt = $this->db->prepare("SELECT count(login) FROM usuarios where login=?");
		$stmt->execute(array($username));

		if ($stmt->fetchColumn() > 0) {
			return true;
		}
	}

	public function getUser($username) {
		$stmt = $this->db->prepare("SELECT * FROM usuarios where login=?");
		$stmt->execute(array($username));
		$user = $stmt->fetch(PDO::FETCH_ASSOC);

		if ($user != NULL) {
			return new User($user["idusuarios"], $user["nombre"], $user["apellidos"], $user["login"], $user["contrasena"], $user["img"]);
		}
		else{
			return NULL;
		}
			
	}

	public function findUserImgsbyPoll(){
		$stmt = $this->db->prepare("SELECT DISTINCT encuestas.idencuestas, encuestas.titulo, encuestas.fecha_creacion, usuarios.idusuarios, usuarios.nombre, usuarios.apellidos, img 
			FROM usuarios, encuestas, 
				(SELECT DISTINCT huecos.encuestas_idencuestas FROM huecos, huecos_has_usuarios 
					WHERE huecos_has_usuarios.usuarios_idusuarios=? AND huecos_has_usuarios.idhuecos=huecos.idhueco) AS part
			WHERE (usuarios.idusuarios=encuestas.usuarios_idcreador AND encuestas.usuarios_idcreador=?) 
				OR part.encuestas_idencuestas=encuestas.idencuestas AND usuarios.idusuarios=encuestas.usuarios_idcreador");
		
		$stmt->execute(array($_SESSION["currentuser"],$_SESSION["currentuser"]));
		$poll_db = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$imgs = array();

		foreach ($poll_db as $poll) {
			$stmt2 = $this->db->prepare("SELECT DISTINCT huecos_has_usuarios.usuarios_idusuarios FROM huecos, huecos_has_usuarios
				WHERE huecos.encuestas_idencuestas=? AND huecos.idhueco=huecos_has_usuarios.idhuecos");
				
			$stmt2->execute(array($poll["idencuestas"]));
			$user_db = $stmt2->fetchAll(PDO::FETCH_ASSOC);

			$aux = array();
			foreach($user_db as $user){
				array_push($aux,$user["usuarios_idusuarios"]);
			}
			$imgs[$poll["idencuestas"]]=$aux;
		}
		return $imgs;
		
	}

	/**
	* Checks if a given pair of username/password exists in the database
	*
	* @param string $username the username
	* @param string $passwd the password
	* @return boolean true the username/passwrod exists, false otherwise.
	*/
	public function isValidUser($username, $passwd) {
		$stmt = $this->db->prepare("SELECT count(login) FROM usuarios where login=? and contrasena=?");
		$stmt->execute(array($username, $passwd));

		if ($stmt->fetchColumn() > 0) {
			return true;
		}
	}
}

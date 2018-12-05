<?php
// file: model/UserMapper.php

require_once(__DIR__."/../core/PDOConnection.php");
require_once(__DIR__."/Encuesta.php");
require_once(__DIR__."/Huecos_has_usuarios.php");
/**
* Class UserMapper
*
* Database interface for User entities
*
* @author lipido <lipido@gmail.com>
*/
class HuecohasUsuariosMapper {

	/**
	* Reference to the PDO connection
	* @var PDO
	*/
	private $db;

	public function __construct() {
		$this->db = PDOConnection::getInstance();
    }
	

	public function modify(Hueco_has_usuarios $hueco) {
		$stmt = $this->db->prepare("UPDATE huecos_has_usuarios SET estado = ? WHERE idhuecos = ? AND usuarios_idusuarios = ?");
		$stmt->execute(array( $hueco->getEstado(), $hueco->getId(), $hueco->getUsuarios_idusuarios()->getId()));
		return $this->db->lastInsertId();
	}

	public function existHueco(Hueco_has_usuarios $hueco) {
		$stmt = $this->db->prepare("SELECT COUNT(idhuecos) FROM huecos_has_usuarios WHERE idhuecos=? AND usuarios_idusuarios=?");
		$stmt->execute(array($hueco->getId(), $hueco->getUsuarios_idusuarios()->getId()));

		if ($stmt->fetchColumn() > 0) {
			return true;
		}
	}
	public function existHuecoId($id,$user) {
		$stmt = $this->db->prepare("SELECT COUNT(idhuecos) FROM huecos_has_usuarios,huecos 
			WHERE huecos.idhueco=huecos_has_usuarios.idhuecos AND huecos.encuestas_idencuestas=? AND huecos_has_usuarios.usuarios_idusuarios=?");
		$stmt->execute(array($id,$user));

		if ($stmt->fetchColumn() > 0) {
			return true;
		}
	}
	
	public function createHuecosUser($id,$user){
		$stmt = $this->db->prepare("SELECT idhueco FROM huecos WHERE encuestas_idencuestas=?");
		$stmt->execute(array($id));
		
		$hueco_db = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		foreach($hueco_db as $hueco){
			$stmt2 = $this->db->prepare("INSERT INTO huecos_has_usuarios(idhuecos,usuarios_idusuarios,estado) VALUES(?,?,?)");
			$stmt2->execute(array($hueco["idhueco"],$user,0));
			
		}
	}

	public function createHuecosUserSingle($idhueco, $id){
		
		// $stmt2 = $this->db->prepare("INSERT INTO huecos_has_usuarios(idhuecos,usuarios_idusuarios,estado) VALUES(?,?,?)");
		// $stmt2->execute(array($idhueco,$_SESSION["currentuser"],0));
		
		$stmt = $this->db->prepare("SELECT DISTINCT usuarios_idusuarios FROM huecos, huecos_has_usuarios WHERE huecos_has_usuarios.idhuecos=huecos.idhueco AND huecos.encuestas_idencuestas=?");
		$stmt->execute(array($id));
		
		$user_db = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		foreach($user_db as $user){
			$stmt2 = $this->db->prepare("INSERT INTO huecos_has_usuarios(idhuecos,usuarios_idusuarios,estado) VALUES(?,?,?)");
			$stmt2->execute(array($idhueco,$user["usuarios_idusuarios"],0));
		}
		
	
	}
	
	
	public function defaultAllHueco($user, $hueco) {
		$stmt = $this->db->prepare("UPDATE huecos_has_usuarios SET estado = 0 WHERE idhuecos = ? AND usuarios_idusuarios = ?");
		$stmt->execute(array($hueco->getId(), $user->getId()));

		if ($stmt->fetchColumn() > 0) {
			return true;
		}
	}

	public function createHuecos($id,$user){
		$stmt = $this->db->prepare("SELECT huecos.encuestas_idencuestas FROM huecos_has_usuarios,huecos WHERE huecos.encuestas_idencuestas=? AND huecos_has_usuarios.usuarios_idusuarios=?");
		$stmt->execute(array($id,$user));
		$poll_db = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		if(empty($poll_db)){
		//if ($stmt->fetchColumn() > 0) {
			return true;
		}
		return false;
	}
}

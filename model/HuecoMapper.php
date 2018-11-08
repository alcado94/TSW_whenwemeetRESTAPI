<?php
// file: model/UserMapper.php

require_once(__DIR__."/../core/PDOConnection.php");
require_once(__DIR__."/Encuesta.php");
require_once(__DIR__."/Hueco.php");
/**
* Class UserMapper
*
* Database interface for User entities
*
* @author lipido <lipido@gmail.com>
*/
class HuecoMapper {

	/**
	* Reference to the PDO connection
	* @var PDO
	*/
	private $db;

	public function __construct() {
		$this->db = PDOConnection::getInstance();
    }
	

	public function save(Hueco $hueco) {
		$stmt = $this->db->prepare("INSERT INTO huecos(encuestas_idencuestas, fecha_inicio, fecha_fin) values (?,?,?)");
		$stmt->execute(array($hueco->getEncuestas_idencuestas(), $hueco->getFechaInicio()->format('Y-m-d H:i:s'), $hueco->getFechaFin()->format('Y-m-d H:i:s')));
		return $this->db->lastInsertId();
	}

	public function delete($id) {
		$stmt = $this->db->prepare("DELETE FROM huecos WHERE idhueco=?");
		$stmt->execute(array($id));
	
	}

	public function getAllOneEncuesta($id) {
		
		$huecos = array();

		$stmt = $this->db->prepare("SELECT idhueco FROM huecos where encuestas_idencuestas=?");
		$stmt->execute(array($id));
		$huecos_db = $stmt->fetchAll(PDO::FETCH_ASSOC);
		foreach ($huecos_db as $hueco) {
			array_push($huecos, new Hueco( $hueco["idhueco"], NULL, NULL,NULL));
		}
		

		return $huecos;
	}

	public function get(Hueco $hueco) {
		$stmt = $this->db->prepare("SELECT count(idhueco) FROM huecos where idhueco=?");
		$stmt->execute(array($hueco->getId()));

		if ($stmt->fetchColumn() > 0) {
			return true;
		}
	}

	public function ownerHueco($idhueco, $iduser) {
		$stmt = $this->db->prepare("SELECT count(idhueco) FROM huecos,encuestas where idencuestas=encuestas_idencuestas and usuarios_idcreador=? and idhueco=?");
		$stmt->execute(array($iduser,$idhueco));

		if ($stmt->fetchColumn() > 0) {
			return true;
		}
	}
	
}

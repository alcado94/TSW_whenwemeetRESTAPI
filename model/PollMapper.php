<?php
// file: model/UserMapper.php

require_once(__DIR__."/../core/PDOConnection.php");
require_once(__DIR__."/Encuesta.php");
/**
* Class UserMapper
*
* Database interface for User entities
*
* @author lipido <lipido@gmail.com>
*/
class PollMapper {

	/**
	* Reference to the PDO connection
	* @var PDO
	*/
	private $db;

	public function __construct() {
		$this->db = PDOConnection::getInstance();
	}

    

	public function findall($user) {
				
		$stmt = $this->db->prepare("SELECT DISTINCT encuestas.idencuestas, encuestas.titulo, encuestas.fecha_creacion, usuarios.idusuarios, usuarios.nombre, usuarios.apellidos, img 
			FROM usuarios, encuestas, 
				(SELECT DISTINCT huecos.encuestas_idencuestas FROM huecos, huecos_has_usuarios 
					WHERE huecos_has_usuarios.usuarios_idusuarios=? AND huecos_has_usuarios.idhuecos=huecos.idhueco) AS part
			WHERE (usuarios.idusuarios=encuestas.usuarios_idcreador AND encuestas.usuarios_idcreador=?) 
				OR part.encuestas_idencuestas=encuestas.idencuestas AND usuarios.idusuarios=encuestas.usuarios_idcreador");
		
		$stmt->execute(array($user->getId(),$user->getId()));
		$poll_db = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$polls = array();

		foreach ($poll_db as $poll) {
			$stmt2 = $this->db->prepare("SELECT COUNT(DISTINCT huecos_has_usuarios.usuarios_idusuarios) FROM huecos, huecos_has_usuarios
				WHERE huecos.encuestas_idencuestas=? AND huecos.idhueco=huecos_has_usuarios.idhuecos");
				
			$stmt2->execute(array($poll["idencuestas"]));
			$num = $stmt2->fetchColumn();
			
			array_push($polls, new Encuesta( $poll["idencuestas"], new User($poll["idusuarios"],$poll["nombre"],$poll["apellidos"], NULL, NULL, $poll['img']), $poll["titulo"], $poll["fecha_creacion"], $num));
		}

		return $polls;
	}

	public function get($id, $date){
		if($date==null){
			$stmt = $this->db->prepare("SELECT idencuestas, titulo,fecha_creacion,idencuestas,usuarios_idcreador, fecha_inicio, fecha_fin, nombre, estado,idhuecos, idusuarios, img FROM encuestas,huecos, huecos_has_usuarios, usuarios 
				WHERE huecos.encuestas_idencuestas = ? AND huecos.idhueco = huecos_has_usuarios.idhuecos 
				AND usuarios.idusuarios = huecos_has_usuarios.usuarios_idusuarios AND encuestas.idencuestas= ? ORDER by fecha_inicio");
			$stmt->execute(array($id,$id));
			$poll_db = $stmt->fetchAll(PDO::FETCH_ASSOC);
		}
		else{
			$stmt = $this->db->prepare("SELECT idencuestas, titulo,fecha_creacion,idencuestas,usuarios_idcreador, fecha_inicio, fecha_fin, nombre, estado, idhuecos, idusuarios, img FROM encuestas,huecos, huecos_has_usuarios, usuarios 
			WHERE huecos.encuestas_idencuestas = ? AND huecos.idhueco = huecos_has_usuarios.idhuecos 
			AND usuarios.idusuarios = huecos_has_usuarios.usuarios_idusuarios AND encuestas.idencuestas= ? AND fecha_creacion = ? ORDER by fecha_inicio");
			$stmt->execute(array($id,$id,$date));
			$poll_db = $stmt->fetchAll(PDO::FETCH_ASSOC);

		}
		//Retorna un array de datos no compatibles con ninguna entidad
		return $poll_db;
	}

	public function getEncuesta($id, $date){
		if($date==null){
			$stmt = $this->db->prepare("SELECT titulo,fecha_creacion,idencuestas,usuarios_idcreador, fecha_inicio, fecha_fin FROM encuestas,huecos 
			WHERE huecos.encuestas_idencuestas = encuestas.idencuestas AND encuestas.idencuestas= ? ORDER by fecha_inicio");
			$stmt->execute(array($id));
			$poll_db = $stmt->fetchAll(PDO::FETCH_ASSOC);
		}
		else{
			$stmt = $this->db->prepare("SELECT titulo,fecha_creacion,idencuestas,usuarios_idcreador, fecha_inicio, fecha_fin FROM encuestas,huecos 
			WHERE huecos.encuestas_idencuestas = encuestas.idencuestas AND encuestas.idencuestas= ? AND fecha_creacion=? ORDER by fecha_inicio");
			$stmt->execute(array($id,$date));
			$poll_db = $stmt->fetchAll(PDO::FETCH_ASSOC);
		}
		//Retorna un array de datos no compatibles con ninguna entidad
		return $poll_db;
	}

	public function getEncuestaEdit($id, $date){
		if($date==null){
			$stmt = $this->db->prepare("SELECT titulo,fecha_creacion,idencuestas, fecha_inicio, fecha_fin, idhueco FROM encuestas,huecos 
			WHERE huecos.encuestas_idencuestas = encuestas.idencuestas AND encuestas.idencuestas= ? ORDER by fecha_inicio");
			$stmt->execute(array($id));
			$poll_db = $stmt->fetchAll(PDO::FETCH_ASSOC);
		}
		else{
			$stmt = $this->db->prepare("SELECT titulo,fecha_creacion,idencuestas, fecha_inicio, fecha_fin, idhueco FROM encuestas,huecos 
			WHERE huecos.encuestas_idencuestas = encuestas.idencuestas AND encuestas.idencuestas= ? AND fecha_creacion=? ORDER by fecha_inicio");
			$stmt->execute(array($id,$date));
			$poll_db = $stmt->fetchAll(PDO::FETCH_ASSOC);
		}

		
		//Retorna un array de datos no compatibles con ninguna entidad
		return $poll_db;
	}
	
	public function getEncuestaInfo($id, $date){
		if($date==null){
			$stmt = $this->db->prepare("SELECT titulo,fecha_creacion,idencuestas,usuarios_idcreador FROM encuestas,huecos 
			WHERE encuestas.idencuestas= ?");
			$stmt->execute(array($id));
			$poll_db = $stmt->fetchAll(PDO::FETCH_ASSOC);
		}
		else{
			$stmt = $this->db->prepare("SELECT titulo,fecha_creacion,idencuestas,usuarios_idcreador FROM encuestas,huecos 
			WHERE encuestas.idencuestas= ? AND fecha_creacion=?");
			$stmt->execute(array($id,$date));
			$poll_db = $stmt->fetchAll(PDO::FETCH_ASSOC);

		}
		
		return new Encuesta($poll_db[0]['idencuestas'],$poll_db[0]['usuarios_idcreador'],$poll_db[0]['titulo'],$poll_db[0]['fecha_creacion']);
	}

	public function getAuthor($id){
		$stmt = $this->db->prepare("SELECT nombre FROM encuestas, usuarios WHERE  encuestas.usuarios_idcreador = usuarios.idusuarios and encuestas.idencuestas = ?");
		$stmt->execute(array($id));
		$poll_db = $stmt->fetchAll(PDO::FETCH_ASSOC);

		return new User(NULL,$poll_db[0]['nombre']);
	}

	public function save(Encuesta $encuesta) {
		$stmt = $this->db->prepare("INSERT INTO encuestas(usuarios_idcreador, titulo, fecha_creacion) values (?,?,?)");
		$stmt->execute(array($encuesta->getUsuarios_idcreador(), $encuesta->getTitulo(), $encuesta->getFechaCreacion()));
		return $this->db->lastInsertId();
	}

	public function edit(Encuesta $encuesta) {
		$stmt = $this->db->prepare("UPDATE encuestas SET titulo=? WHERE encuestas.idencuestas=? ");
		$stmt->execute(array($encuesta->getTitulo(),$encuesta->getId()));
		return $this->db->lastInsertId();
	}

		
	public function userIsAuthor($id,$user){
		$stmt = $this->db->prepare("SELECT COUNT(*) FROM encuestas WHERE idencuestas=? AND usuarios_idcreador=?");
		$stmt->execute(array($id,$user));
		
		if ($stmt->fetchColumn() > 0) {
			return true;
		}
		return false;
	}

}

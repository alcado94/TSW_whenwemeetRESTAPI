<?php

require_once(__DIR__."/../model/User.php");
require_once(__DIR__."/../model/UserMapper.php");

require_once(__DIR__."/../model/Hueco.php");
require_once(__DIR__."/../model/Encuesta.php");
require_once(__DIR__."/../model/PollMapper.php");
require_once(__DIR__."/../model/HuecoMapper.php");
require_once(__DIR__."/../model/Huecos_has_usuariosMapper.php");


require_once(__DIR__."/BaseRest.php");


class PollRest extends BaseRest {
	private $pollMapper;

	public function __construct() {
		parent::__construct();

		$this->pollMapper = new PollMapper();
		$this->huecoMapper = new HuecoMapper();
		$this->huecohasusuariosMapper = new huecohasusuariosMapper();
		$this->userMapper = new UserMapper();
		
	}

	public function getPolls() {
        $currentLogged = parent::authenticateUser();

		$polls = $this->pollMapper->findAll($currentLogged);
		
		$imgs = $this->userMapper->findUserImgsbyPoll($currentLogged->getId());


		

		$polls_array = array();
		foreach($polls as $poll) {
			
			$imgsId = array();
			foreach($imgs[$poll->getId()] as $img){
				array_push($imgsId, $img);
			}
			$imgsId = array_slice($imgsId, 0, 6); 

			array_push($polls_array, array(
                "id" => $poll->getId(),
                "creador" => array(
                    "id_creador" => $poll->getUsuarios_idcreador()->getId(),
                    "name_creador" => $poll->getUsuarios_idcreador()->getName(),
                    "surname_creador" => $poll->getUsuarios_idcreador()->getSurname(),
                    "image_creador" => $poll->getUsuarios_idcreador()->getImage()
                ),
                "title" => $poll->getTitulo(),
                "fecha_creacion" => $poll->getFechaCreacion(),
				"num_usuarios" => $poll->getNumUsrs(),
				"imgsId" => $imgsId
            ));
            
		}
        
		header($_SERVER['SERVER_PROTOCOL'].' 200 Ok');
		header('Content-Type: application/json');
        echo(json_encode($polls_array));
        
	}

	public function getPoll($id) {
        $currentLogged = parent::authenticateUser();
		
		if(!isset($id)){
			header($_SERVER['SERVER_PROTOCOL'].' 400 Bad request');
			return;
		}

		if(!$this->huecohasusuariosMapper->existHuecoId($id,$currentLogged->getId()) && !$this->pollMapper->userIsAuthor($id,$currentLogged->getId())){
			header($_SERVER['SERVER_PROTOCOL'].' 403 Forbidden');
			return;
		}

		$date=null;

		$result = $this->pollMapper->get($id,$date);
		if(empty($result)){
			$result = $this->pollMapper->getEncuesta($id,$date);
		}
		if(empty($result)){
			$result = $this->pollMapper->getEncuestaInfo($id,$date);
		}
		if(!empty($result)){
			$_SESSION["permission"]=true;
			
			$author = $this->pollMapper->getAuthor($id);
			
			$poll = $this->pollMapper->recomposeArrayShow($result,$author[0]['nombre'],$currentLogged->getId());		
		}

		
		if(!$this->pollMapper->userIsAuthor($id,$currentLogged->getId())) {
			$poll["url"] = '';	
		}
        
		header($_SERVER['SERVER_PROTOCOL'].' 200 Ok');
		header('Content-Type: application/json');
        echo(json_encode($poll));
        
	}

	public function getPollParticipate($id) {
        $currentLogged = parent::authenticateUser();
		
		if(!isset($id)){
			header($_SERVER['SERVER_PROTOCOL'].' 400 Bad request');
			return;
		}

		if($this->huecohasusuariosMapper->existHuecoId($id,$currentLogged->getId())!=true){
			$this->huecohasusuariosMapper->createHuecosUser($id,$currentLogged->getId());
		}

		$date=null;

		$result = $this->pollMapper->get($id,$date);
		if(empty($result)){
			$result = $this->pollMapper->getEncuesta($id,$date);
		}
		if(empty($result)){
			$result = $this->pollMapper->getEncuestaInfo($id,$date);
		}
		if(!empty($result)){
			$_SESSION["permission"]=true;
			
			$author = $this->pollMapper->getAuthor($id);
			
			$poll = $this->pollMapper->recomposeArrayShow($result,$author[0]['nombre'],$currentLogged->getId());		
		}

		
		if(!$this->pollMapper->userIsAuthor($id,$currentLogged->getId())) {
			$poll["url"] = '';	
		}
        
		header($_SERVER['SERVER_PROTOCOL'].' 200 Ok');
		header('Content-Type: application/json');
        echo(json_encode($poll));
        
	}
	
	public function addPoll($data) {
        $currentLogged = parent::authenticateUser();
		
		try{

			if ($data == NULL){ 
				header($_SERVER['SERVER_PROTOCOL'].' 400 Bad request');
				return;
			}
			
			$data = json_encode($data);
			$data = json_decode($data,true);
			$date = date("Y-m-d H:i:s");
			
			$enc = new Encuesta(NULL,$currentLogged->getId(),$data["title"],$date);

			$enc->checkIsValidForCreate();
			
			
			$array_huecos_checked = array();
			//Este for comprueba si todas las fechas son validas
			foreach ($data["day"] as $key => $value) {
				
				$dia = $value[0];

				$array_huecos = array();


				foreach ($value as $key2 => $value2) {
					if($value2 != $value[0]){
						$hueco = new Hueco();						
						
						$hueco->setFechaInicio(new DateTime($dia.' '.$value2['hourInit']));
						$hueco->setFechaFin(new DateTime($dia.' '.$value2['hourEnd']));
						$hueco->checkIsValidForCreate();
						
						foreach ($array_huecos as $hue) {
							if ( ( $hueco->getFechaInicio() > $hue->getFechaInicio() && $hueco->getFechaInicio() < $hue->getFechaFin() )
								|| ( $hueco->getFechaFin() > $hue->getFechaInicio() && $hueco->getFechaFin() < $hue->getFechaFin() ) ) {
								header($_SERVER['SERVER_PROTOCOL'].' 400 Bad request');
								return;
							}
						}
					
						array_push($array_huecos,$hueco);	
						array_push($array_huecos_checked,$hueco);	
					}
				}
			}

			//Si llega aqui esta todo correcto
			$id_enc = $this->pollMapper->save($enc);

			foreach ($array_huecos_checked as $key => $hueco) {
				$hueco->setEncuestas_idencuestas($id_enc);
				$this->huecoMapper->save($hueco);
			}
		
			$this->huecohasusuariosMapper->createHuecosUser($id_enc,$currentLogged->getId());
			
			header($_SERVER['SERVER_PROTOCOL'].' 201 Created');
			header('Location: '.$_SERVER['REQUEST_URI']."/".$id_enc);
			header('Content-Type: application/json');
		
		} catch (ValidationException $e) {
			header($_SERVER['SERVER_PROTOCOL'].' 400 Bad request');
			header('Content-Type: application/json');
			echo(json_encode($e->getErrors()));
		}
	}

	public function editPoll($id, $data) {
        $currentLogged = parent::authenticateUser();
		
		if ($data == NULL || $id == NULL){ 
			header($_SERVER['SERVER_PROTOCOL'].' 400 Bad request');
			return;
		}

		if(!$this->pollMapper->userIsAuthor($id,$currentLogged->getId())){
			header($_SERVER['SERVER_PROTOCOL'].' 403 Forbidden');
			return;
		}

		$data = json_encode($data);
		$data = json_decode($data,true);

		if ( !isset($data["title"]) ){ 
			header($_SERVER['SERVER_PROTOCOL'].' 400 Bad request');
			return;
		}

		$result = $this->pollMapper->getEncuestaEdit($id,NULL);

		$date = date("Y-m-d H:i:s");

		$enc = new Encuesta($id,$currentLogged->getId(),$data["title"],$date);
		
		$enc->checkIsValidForCreate();

		$id_enc = $this->pollMapper->edit($enc);

		
		//Esto comprueba que el JSON esta bien
		$array_huecos = array();

		$today = date("Y-m-d H:i:s");
		try{
			foreach ($data["daysNew"] as $dia => $arrayhoras) {
				foreach ($arrayhoras as $cita => $horas) {

						$hueco = new Hueco();						
						
						$hueco->setFechaInicio(new DateTime($dia.' '.$horas[0]));
						$hueco->setFechaFin(new DateTime($dia.' '.$horas[1]));
						$hueco->checkIsValidForCreate();
						
						foreach ($array_huecos as $hue) {
							if ( ( $hueco->getFechaInicio() > $hue->getFechaInicio() && $hueco->getFechaInicio() < $hue->getFechaFin() )
								|| ( $hueco->getFechaFin() > $hue->getFechaInicio() && $hueco->getFechaFin() < $hue->getFechaFin() ) 
								|| $hueco->getFechaInicio() < $hueco->getFechaFin() 
								|| $hueco->getFechaInicio() > $today 
								|| $hueco->getFechaFin() > $today ) {
								header($_SERVER['SERVER_PROTOCOL'].' 400 Bad request');
								return;
							}
						}
					
						array_push($array_huecos,$hueco);
					
				}
			}

			foreach ($data["days"] as $keyData => $valueData) {
				if ( !$this->huecoMapper->ownerHueco($valueData,$currentLogged->getId()) ) {
					header($_SERVER['SERVER_PROTOCOL'].' 403 Forbidden');
					return;
				}
			}
		} catch (ValidationException $e) {
			header($_SERVER['SERVER_PROTOCOL'].' 400 Bad request');
			header('Content-Type: application/json');
			echo(json_encode($e->getErrors()));
			return;
		}	

		//Aqui se acaba las comprobaciones
		
		foreach ($data["daysNew"] as $dia => $arrayhoras) {
			foreach ($arrayhoras as $cita => $horas) {
		
				$añadir = False;
				$evaluado = False;

				if ( !empty($result) ){		
					//Este for recorre cada dia que ya existe en BD
					foreach ($result['dias'] as $diaExistente => $arrayhorasExistente) {	
						//value2 es el contenido de un dia, por lo que "e" es una cita 
						foreach ($arrayhorasExistente as $citaExistente => $horasExistente) {				
							if(!$evaluado){
								
								if ($diaExistente == $dia & 
									(new DateTime($horasExistente['Init']))->format('H:i') == (new DateTime($horas[0]))->format('H:i') &
									(new DateTime($horasExistente['End']))->format('H:i') == (new DateTime($horas[1]))->format('H:i')) {

									$añadir = False;
									$evaluado = True;

								} else {

									$añadir = True;
									$inicioAñadir = $dia.' '.$horas[0];
									$finAñadir = $dia.' '.$horas[1];

								}
							}
						}
					}	
				} else {
					$añadir = true;
					$inicioAñadir = $dia.' '.$horas[0];
					$finAñadir = $dia.' '.$horas[1];
				}
				if($añadir){
					$hueco = new Hueco(NULL,$id,new DateTime($inicioAñadir),new DateTime($finAñadir));
					$idhueco = $this->huecoMapper->save($hueco);
				}
				if ( $data['daysNew'] & isset($idhueco))
					$this->huecohasusuariosMapper->createHuecosUserSingle($idhueco,$id);
			}
		}
			
		if(!empty($result)){
			foreach ($result['diasId'] as $key => $dia) {
				foreach ($dia as $keyDia => $value) {
				
					$delete = True;
					foreach ($data["days"] as $keyData => $valueData) {
						if ($value == $valueData){
							$delete = False;
						} 
					}
					if($delete & $this->huecoMapper->ownerHueco($value,$currentLogged->getId())){
						$this->huecoMapper->delete($value);
					}
				}
			}		
		}	
        
		header($_SERVER['SERVER_PROTOCOL'].' 200 Ok');
        
	}


	public function participatePoll($id,$data) {
        $currentLogged = parent::authenticateUser();
		
		if ($data == NULL || $id == NULL){ 
			header($_SERVER['SERVER_PROTOCOL'].' 400 Bad request');
			return;
		}

		if ( !$this->huecohasusuariosMapper->existHuecoId($id,$currentLogged->getId()) ) {
			header($_SERVER['SERVER_PROTOCOL'].' 403 Forbidden');
			return;
		}

		$user = new User($currentLogged->getId());

		$data = json_encode($data);
		$data = json_decode($data,true);

		//Comprobacion de que los huecos son del usuario
		if(isset($data["participateDate"])){
			foreach ($data["participateDate"] as $key => $value) {
			
				$hueco_part = new Hueco_has_usuarios($key,$user,NULL);

				if(!$this->huecohasusuariosMapper->existHueco($hueco_part)){
					header($_SERVER['SERVER_PROTOCOL'].' 403 Forbidden');
					return;
				}
			}
		}

		//Recoge todo los huecos de una encuesta
		$huecos = $this->huecoMapper->getAllOneEncuesta($id);

		//Pone a 0 el estado de los huecos de un usuario de una cierta encuesta
		foreach ($huecos as $hueco) {
			$this->huecohasusuariosMapper->defaultAllHueco($user,$hueco);
		}
	
		//Setea el estado de un hueco a 1
		if(isset($data["participateDate"])){
			foreach ($data["participateDate"] as $key => $value) {

				if ($value == 1){
					$hueco_part = new Hueco_has_usuarios($key,$user,1);
					$this->huecohasusuariosMapper->modify($hueco_part);
				}
				
			}
		}
        
		header($_SERVER['SERVER_PROTOCOL'].' 200 Ok');
        
	}

	public function confirmPoll($code){
		$id = substr($code, 10);
		$time = substr($code,0, 10);
		$date = date("Y-m-d H:i:s",$time);

		print_r($id);

		$result = $this->pollMapper->get($id,$date);
		if(empty($result)){
			$result = $this->pollMapper->getEncuesta($id,$date);
		}
		if(empty($result)){
			$result = $this->pollMapper->getEncuestaInfo($id,$date);
		}
		if(!empty($result)){
			return null;
		}
		return $id;

	}
}

// URI-MAPPING for this Rest endpoint
$pollRest = new PollRest();
URIDispatcher::getInstance()
->map("GET",	"/poll", array($pollRest,"getPolls"))
->map("GET",	"/poll/$1", array($pollRest,"getPoll"))
->map("POST",	"/poll", array($pollRest,"addPoll"))
->map("PUT",	"/poll/$1", array($pollRest,"editPoll"))
->map("PUT",	"/poll/$1/participate", array($pollRest,"participatePoll"))
->map("GET",	"/code/$1",array($pollRest,"confirmPoll"))
->map("GET",	"/poll/code/$1",array($pollRest,"getPollParticipate"));

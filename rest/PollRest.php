<?php

require_once(__DIR__."/../model/User.php");
require_once(__DIR__."/../model/UserMapper.php");

require_once(__DIR__."/../model/Hueco.php");
require_once(__DIR__."/../model/Encuesta.php");
require_once(__DIR__."/../model/PollMapper.php");
require_once(__DIR__."/../model/HuecoMapper.php");
require_once(__DIR__."/../model/Huecos_has_usuariosMapper.php");

require_once(__DIR__."/../mail/PHPMailerAutoload.php");


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
			
			$poll = $this->recomposeArrayShow($result,$author->getName(),$currentLogged->getId());		
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
			
			$poll = $this->recomposeArrayShow($result,$author->getName(),$currentLogged->getId());		
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
						
						if($dia == '' || $value2['hourInit'] == '' || $value2['hourEnd'] == ''){
							header($_SERVER['SERVER_PROTOCOL'].' 400 Bad request');
							return;
						}					
						
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

		$result = $this->recomposeArrayShowEditPoll(($this->pollMapper->getEncuestaEdit($id,NULL)));

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
						
						if($dia == '' || $horas[0] == '' || $horas[1] == ''){
							header($_SERVER['SERVER_PROTOCOL'].' 400 Bad request');
							return;
						}					
						
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


        $this->notifyUsers($id);
		header($_SERVER['SERVER_PROTOCOL'].' 200 Ok');
        
	}

	private function notifyUsers($id){
		$currentLogged = parent::authenticateUser();

		$result = $this->userMapper->getMails($id);

		$usuario = $this->userMapper->getUser($currentLogged->getLogin());

		$poll = $this->pollMapper->getEncuestaInfo($id,null);

		if(!empty($result)){
			$mail = new PHPMailer;
			//Tell PHPMailer to use SMTP
			$mail->isSMTP();
			//Enable SMTP debugging
			// 0 = off (for production use)
			// 1 = client messages
			// 2 = client and server messages
			//$mail->SMTPDebug = 2;
			//Ask for HTML-friendly debug output
			$mail->Debugoutput = 'html';
			//Set the hostname of the mail server
			//$mail->Host = 'smtp.gmail.com';
			// use
			$mail->Host = gethostbyname('smtp.gmail.com');
			// if your network does not support SMTP over IPv6
			//Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
			$mail->Port =587;
			//Set the encryption system to use - ssl (deprecated) or tls
			$mail->SMTPSecure = 'tls';
			//Whether to use SMTP authentication
			$mail->SMTPAuth = true;
			//Username to use for SMTP authentication - use full email address for gmail
			$mail->Username = "g12.abp.gimnasio@gmail.com";
			//Password to use for SMTP authentication
			$mail->Password = "gimnasio";
			//Set who the message is to be sent from
			$mail->setFrom("g12.abp.gimnasio@gmail.com", 'When We Meet');
			//Set an alternative reply-to address
			$mail->addReplyTo("g12.abp.gimnasio@gmail.com", 'When We Meet');
			//Set who the message is to be sent to
			//$mail->addAddress($email, $email);
			//Set the subject line
			$mail->Subject = "Nueva participacion en encuesta";
			//convert HTML into a basic plain-text alternative body
			$mail->Body = "El usuario " . $usuario->getName() . " " . $usuario->getSurname() . 
				" ha participado en la encuesta: " . '"' . $poll->getTitulo(). '"';
			//Replace the plain text body with one created manually
			$mail->AltBody = "El usuario " . $usuario->getName() . " " . $usuario->getSurname() . 
			" ha participado en la encuesta: " . '"' . $poll->getTitulo() . '"';
			$mail->SMTPOptions = array(
				'ssl' => array(
					'verify_peer' => false,
					'verify_peer_name' => false,
					'allow_self_signed' => true
					));	

			foreach($result as $user ){				
				$mail->addAddress($user->getEmail(), $user->getEmail());
			}		  

			if (!$mail->Send()) {
				//echo("Mailer Error");
			} else {
				//echo("Message sent!");
			}
		}
		return;
	}
	

	public function confirmPoll($code){
		$id = substr($code, 10);
		$time = substr($code,0, 10);
		$date = date("Y-m-d H:i:s",$time);

		$result = $this->pollMapper->get($id,$date);
		if(empty($result)){
			$result = $this->pollMapper->getEncuesta($id,$date);
		}
		if(empty($result)){
			$result = $this->pollMapper->getEncuestaInfo($id,$date);
		}
		if(empty($result)){
			return null;
		}

		header($_SERVER['SERVER_PROTOCOL'].' 201 Created');
		echo($id);

	}

	private function recomposeArrayShowEditPoll($poll_db){

		if (  empty($poll_db) ){
			return array();
		}

		$result = array();
		$result['title'] = $poll_db[0]['titulo'];
		$result['Id'] = $poll_db[0]['idencuestas'];
		$result['dias'] = array();
		$result['diasId'] = array();

		foreach ($poll_db as $value) {
			$parts = explode(' ', $value['fecha_inicio']);
			$parts2 = explode(' ', $value['fecha_fin']);
			#$result['dias'][$parts[0]] = array("horas"=>array("Init"=>$parts[1],"End"=>$parts2[1]));
			$result['dias'][$parts[0]] = array();
			
		}
		foreach($poll_db as $poll){
			$parts = explode(' ', $poll['fecha_inicio']);
			$parts2 = explode(' ', $poll['fecha_fin']);
			if(isset($result['dias'][$parts[0]])){
				array_push($result['dias'][$parts[0]],array("Init"=>$parts[1],"End"=>$parts2[1]));
			}
		}


		foreach ($poll_db as $value) {
			$parts = explode(' ', $value['fecha_inicio']);
			$parts2 = explode(' ', $value['fecha_fin']);
			#$result['dias'][$parts[0]] = array("horas"=>array("Init"=>$parts[1],"End"=>$parts2[1]));
			$result['diasId'][$parts[0]] = array();
			
		}
		foreach($poll_db as $poll){
			$parts = explode(' ', $poll['fecha_inicio']);
			$parts2 = explode(' ', $poll['fecha_fin']);
			if(isset($result['diasId'][$parts[0]])){
				array_push($result['diasId'][$parts[0]],$poll['idhueco']);
			}
		}
		return $result;
	}

	public function recomposeArrayShow($result, $autor, $iduser){



		if(isset($result[0]['fecha_inicio']) & isset($result[0]['idusuarios'])){

			$checkDays =  array();
			$day;
			$daypos;

			foreach ($result as $key => $value) {
				if(!in_array($value['fecha_inicio'],$checkDays)){
					array_push($checkDays,$value['fecha_inicio']);
					$day = $value['fecha_inicio'];
					$daypos = $key;
					$temp = $value;
				}

				if($day == $value['fecha_inicio'] & $iduser == $value['idusuarios']){
					$result[$daypos] = $value;
					$result[$key] = $temp;
				}
			}
		}

		$toret = array();
		$toret['id'] = $result[0]['idencuestas'];
		$toret['titulo'] = $result[0]['titulo'];
		$toret['autor'] = $autor;
		$toret['idAutor'] = $result[0]['usuarios_idcreador'];
		$toret['participantes'] = array();
		$toret['participantesId'] = array();
		$toret['participantesImg'] = array();
		$toret['dias'] = array();
		$toret['url'] = strtotime($result[0]['fecha_creacion']).$result[0]['idencuestas'];

		$toret['diasId'] = array();
		

		$i = 0;
		if(isset($result[0]['fecha_inicio'])){

			if(isset($result[0]['nombre'])){
				$toret['participantes'][$i] = $result[0]['nombre'];
				$toret['participantesId'][$i] = $result[0]['idusuarios'];
				$toret['participantesImg'][$i] = $result[0]['img'];
			}

			$parts = explode(' ', $result[0]['fecha_inicio']);
			
			$toret['dias'][$parts[0]] = array();

			$i++;
			
			
			foreach ($result as $key => $value) {
				foreach($toret['participantes'] as $k=>$val){
					
					if(!in_array($value['idusuarios'], $toret['participantesId'])){
						$toret['participantes'][$i] = $value['nombre'];
						$toret['participantesId'][$i] = $value['idusuarios'];
						$toret['participantesImg'][$i] = $value['img'];
						
						$i++;
					}
				}	
			}

			
			$i = 0;
			foreach ($result as $key => $value) {
				foreach($toret['dias'] as $k=>$val){
					$parts = explode(' ', $value['fecha_inicio']);
						
					if(!in_array($parts[0], $toret['dias'])){
						$toret['dias'][$parts[0]] = array();	
					}

				}	
			}

			foreach ($result as $key => $value) {
				$parts = explode(' ', $value['fecha_inicio']);
				$partsfin = explode(' ', $value['fecha_fin']);
				foreach($toret['dias'] as $k2=>$val2){				
					$toret['dias'][$k2][$parts[1].'-'.$partsfin[1]] = array();	
				}
			}

			$i = 0;
			foreach ($result as $key => $value) {
				$parts = explode(' ', $value['fecha_inicio']);
				$partsfin = explode(' ', $value['fecha_fin']);
				if(isset($value['estado']))
					array_push($toret['dias'][$parts[0]][$parts[1].'-'.$partsfin[1]],$value['estado']);
				else
					array_push($toret['dias'][$parts[0]][$parts[1].'-'.$partsfin[1]],'');
			}

			foreach ($toret['dias'] as $key => $value) {
				foreach ($toret['dias'][$key] as $key2 => $value2) {
					if(empty($toret['dias'][$key][$key2])){
						unset($toret['dias'][$key][$key2]);
					}
			
				}
			}

			foreach ($result as $key => $value) {
				foreach($toret['participantes'] as $k=>$val){
					if(!in_array($value['idhuecos'], $toret['diasId'])){
						array_push($toret['diasId'],$value['idhuecos']);
						
					}
				}	
			}

			
		}

		return $toret;
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

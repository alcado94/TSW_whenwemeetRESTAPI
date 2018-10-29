<?php

require_once(__DIR__."/../model/User.php");
require_once(__DIR__."/../model/UserMapper.php");

require_once(__DIR__."/../model/Encuesta.php");
require_once(__DIR__."/../model/PollMapper.php");


require_once(__DIR__."/BaseRest.php");

/**
* Class PostRest
*
* It contains operations for creating, retrieving, updating, deleting and
* listing posts, as well as to create comments to posts.
*
* Methods gives responses following Restful standards. Methods of this class
* are intended to be mapped as callbacks using the URIDispatcher class.
*
*/
class PollRest extends BaseRest {
	private $pollMapper;
	private $commentMapper;

	public function __construct() {
		parent::__construct();

		$this->pollMapper = new PollMapper();
		
	}

	public function getPolls() {
        $currentLogged = parent::authenticateUser();

		$polls = $this->pollMapper->findAll($currentLogged);

		$polls_array = array();
		foreach($polls as $poll) {
            
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
                "num_usuarios" => $poll->getNumUsrs()
            ));
            
		}
        
		header($_SERVER['SERVER_PROTOCOL'].' 200 Ok');
		header('Content-Type: application/json');
        echo(json_encode($polls_array));
        
	}

	
	
}

// URI-MAPPING for this Rest endpoint
$pollRest = new PollRest();
URIDispatcher::getInstance()
->map("GET",	"/poll", array($pollRest,"getPolls"));

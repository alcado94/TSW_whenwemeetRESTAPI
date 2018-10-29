<?php


require_once(__DIR__."/../core/ValidationException.php");


class Hueco_has_usuarios {

	/**
	* The id of the Hueco
	* @var string
	*/
	private $id;

	/**
	* The usuarios_idusuarios of the Hueco
	* @var string
	*/
	private $usuarios_idusuarios;

	/**
	* The estado of the Hueco
	* @var int
	*/
	private $estado;

	/**
	* The constructor
	*
	* @param string $id The id of the comment
	* @param string $content The content of the comment
	* @param int $author The author of the comment
	*/
	public function __construct($id=NULL,User $usuarios_idusuarios=NULL, $estado=NULL) {
		$this->id = $id;
		$this->usuarios_idusuarios = $usuarios_idusuarios;
		$this->estado = $estado;
	}

	/**
	* Gets the id of this comment
	*
	* @return string The id of this comment
	*/
	public function getId(){
		return $this->id;
	}

	/**
	* Gets the content of this comment
	*
	* @return string The content of this comment
	*/
	public function getUsuarios_idusuarios() {
		return $this->usuarios_idusuarios;
	}

	/**
	* Sets the content of the Comment
	*
	* @param string $content the content of this comment
	* @return void
	*/
	public function setUsuarios_idusuarios($user) {
		$this->usuarios_idusuarios = $user;
	}


	/**
	* Gets the parent post of this comment
	*
	* @return The parent post of this comment
	*/
	public function getEstado() {
		return $this->estado;
	}

	/**
	* Sets the parent Post
	*
	* @param  $post the parent post
	* @return void
	*/
	public function setestado($estado) {
		$this->Estado = $estado;
	}

	/**
	* Checks if the current instance is valid
	* for being inserted in the database.
	*
	* @throws ValidationException if the instance is
	* not valid
	*
	* @return void
	*/
	public function checkIsValidForCreate() {
		$errors = array();

		if (strlen(trim($this->encuestas_idencuestas)) < 2 ) {
			$errors["encuestas_idencuestas"] = "encuestas_idencuestas is mandatory";
		}
		if ($this->fechaInicio == NULL ) {
			$errors["fechaInicio"] = "fechaInicio is mandatory";
		}
		if ($this->fechaFin == NULL ) {
			$errors["fechaFin"] = "fechaFin is mandatory";
		}

		if (sizeof($errors) > 0){
			throw new ValidationException($errors, "comment is not valid");
		}
	}
}

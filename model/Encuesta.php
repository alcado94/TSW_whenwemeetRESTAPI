<?php


require_once(__DIR__."/../core/ValidationException.php");


class Encuesta {

	/**
	* The id of the Hueco
	* @var string
	*/
	private $id;

	/**
	* The encuestas_idencuestas of the Hueco
	* @var string
	*/
	private $usuarios_idcreador;

	/**
	* The fechaInicio of the Hueco
	* @var string
	*/
	private $titulo;

	/**
	* The fechaFin of this Hueco
	* @var date
	*/
	private $fechaCreacion;
	private $numUsrs;

	/**
	* The constructor
	*
	* @param string $id The id of the comment
	* @param string $content The content of the comment
	* @param string $author The author of the comment
	* @param date $post The parent post
	*/
	public function __construct($id=NULL, $usuarios_idcreador=NULL, $titulo=NULL, $fechaCreacion=NULL, $numUsrs=NULL) {
		$this->id = $id;
		$this->usuarios_idcreador = $usuarios_idcreador;
		$this->titulo = $titulo;
		$this->fechaCreacion = $fechaCreacion;
		$this->numUsrs = $numUsrs;
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
	* @return User The content of this comment
	*/
	public function getUsuarios_idcreador() {
		return $this->usuarios_idcreador;
	}

	/**
	* Sets the content of the Comment
	*
	* @param User $content the content of this comment
	* @return void
	*/
	public function setUsuarios_idcreador($author) {
		$this->usuarios_idcreador = $author;
	}

	/**
	* Gets the author of this comment
	*
	* @return string The author of this comment
	*/
	public function getTitulo() {
		return $this->titulo;
	}

	/**
	* Sets the author of this comment
	*
	* @param string $author the author of this comment
	* @return void
	*/
	public function setTitulo(date $titulo){
		$this->titulo = $titulo;
	}

	
	public function getNumUsrs() {
		return $this->numUsrs;
	}
	
	public function setNumUsrs(int $numUsrs){
		$this->numUsrs = $numUsrs;
	}
	
	/**
	* Gets the parent post of this comment
	*
	* @return Post The parent post of this comment
	*/
	public function getFechaCreacion() {
		return $this->fechaCreacion;
	}

	/**
	* Sets the parent Post
	*
	* @param Post $post the parent post
	* @return void
	*/
	public function setFechaCreacion(date $fechaCreacion) {
		$this->fechaCreacion = $fechaCreacion;
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
			$errors["usuarios_idcreador"] = "usuarios_idcreador is mandatory";
		}
		if ($this->titulo == NULL ) {
			$errors["titulo"] = "titulo is mandatory";
		}
		if ($this->fechaCreacion == NULL ) {
			$errors["fechaCreacion"] = "fechaCreacion is mandatory";
		}

		if (sizeof($errors) > 0){
			throw new ValidationException($errors, "comment is not valid");
		}
	}
}

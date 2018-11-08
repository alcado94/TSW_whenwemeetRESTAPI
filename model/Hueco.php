<?php


require_once(__DIR__."/../core/ValidationException.php");


class Hueco {

	/**
	* The id of the Hueco
	* @var string
	*/
	private $id;

	/**
	* The encuestas_idencuestas of the Hueco
	* @var string
	*/
	private $encuestas_idencuestas;

	/**
	* The fechaInicio of the Hueco
	* @var date
	*/
	private $fechaInicio;

	/**
	* The fechaFin of this Hueco
	* @var date
	*/
	private $fechaFin;

	/**
	* The constructor
	*
	* @param string $id The id of the comment
	* @param string $content The content of the comment
	* @param date $author The author of the comment
	* @param date $post The parent post
	*/
	public function __construct($id=NULL, $encuestas_idencuestas=NULL, $fechaInicio=NULL, $fechaFin=NULL) {
		$this->id = $id;
		$this->encuestas_idencuestas = $encuestas_idencuestas;
		$this->fechaInicio = $fechaInicio;
		$this->fechaFin = $fechaFin;
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
	public function getEncuestas_idencuestas() {
		return $this->encuestas_idencuestas;
	}

	/**
	* Sets the content of the Comment
	*
	* @param string $content the content of this comment
	* @return void
	*/
	public function setEncuestas_idencuestas($encuesta) {
		$this->encuestas_idencuestas = $encuesta;
	}

	/**
	* Gets the author of this comment
	*
	* @return User The author of this comment
	*/
	public function getFechaInicio() {
		return $this->fechaInicio;
	}

	/**
	* Sets the author of this comment
	*
	* @param User $author the author of this comment
	* @return void
	*/
	public function setFechaInicio(DateTime $fechaInicio){
		$this->fechaInicio = $fechaInicio;
	}

	/**
	* Gets the parent post of this comment
	*
	* @return Post The parent post of this comment
	*/
	public function getFechaFin() {
		return $this->fechaFin;
	}

	/**
	* Sets the parent Post
	*
	* @param Post $post the parent post
	* @return void
	*/
	public function setFechaFin(DateTime $fechaFin) {
		$this->fechaFin = $fechaFin;
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

		if ($this->fechaInicio == NULL ) {
			$errors["fechaInicio"] = "fechaInicio is mandatory";
		}
		if ($this->fechaFin == NULL ) {
			$errors["fechaFin"] = "fechaFin is mandatory";
		}

		if ( $this->fechaFin < $this->fechaInicio )  {
			$errors["fechas"] = "fechas deben ser correctas";
		}

		if (sizeof($errors) > 0){
			throw new ValidationException($errors, "Hueco is not valid");
		}
	}
}

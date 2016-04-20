<?php
	namespace WaveLearning\Controllers\Database;

	use Silex\Application;

	class MySQLPDOController {
		private $user;
		private $password;
		private $host;
		private $database;
		private $configarray;
		private $app;
		private $pdoobject;

		public function __construct($u, $p, $h, $d, $ac, Application $app){
			$this->user = $u;
			$this->password = $p;
			$this->host = $h;
			$this->database = $d;
			$this->configarray = $ac;
			$this->app = $app;
			$this->pdoobject = null;
		}

		public function __get($name){
			if($name === "user")
				return $this->user;
			else if($name === "password")
				return $this->password;
			else if($name === "host")
				return $this->host;
			else if($name === "database")
				return $this->database;
			else if($name === "configarray")
				return $this->configarray;
			else if($name === "app")
				return $this->app;
		}

		public function __set($name, $pvalue){
			try{
				if(is_string($pvalue)
					&& $name != "configarray"){
					if($name === "user")
						return ($this->user = $pvalue);
					else if($name === "password")
						return ($this->password = $pvalue);
					else if($name === "host")
						return ($this->host = $pvalue);
					else if($name === "database")
						return ($this->database = $pvalue);
				}
				else if($name === "configarray"
					&& is_array($pvalue))
					return ($this->configarray = $pvalue);
				else if($name === "app"
					&& $pvalue != null
					&& $pvalue instanceof Application)
					return ($this->app = $pvalue); 

				throw new \Exception("MySQLControllerError: The parameters passed are wrong", 1);
			}
			catch(\Exception $ex){
				if($this->app["monolog"]
					&& !$this->app["debug"])
					$this->app["monolog"]->addError($ex->getMessage());
			}
		}

		/*
			@{Self}
			Creates and return a new PDO Object.
		*/
		public function buildPDOObject(){
			try{
				$this->pdoobject = new \PDO("mysql:host=$this->host;dbname=$this->database;charset=utf8", 
					$this->user, 
					$this->password, 
					$this->arrayconfig);
				return true;
			}
			catch(\PDOException $ex){
				if($this->app["monolog"]
					&& !$this->app["debug"])
					$this->app["monolog"]->addError($ex->getMessage());	
				return false;
			}
		}

		/*
			@{Self}
			destroy the unique instance of PDOObject
		*/

		public function destroyPDOObject(){
			if($this->pdoobject != null){
				$this->pdoobject = null;
				return true;
			}
			return false;
		}

		/*
			@{Self}
			get the current instance of a pdo object	
		*/

		public function getPDOObject(){
			if(!is_null($this->pdoobject))
				return $this->pdoobject;
			else return false;
		}

		/*
			@{Self}
			get a sql matrix given a query 
		*/

		public function getSQLMatrix($query){
			try{
				if(!is_string($query))
					throw new \Exception("MySQLPDOControllerError: trace method getSQLMatrix -> the query is not a string.", 1);
				 if(!$this->buildPDOObject())
					throw new \Exception("MySQLPDOControllerError: trace method getSQLMatrix -> the pdoobject is null and not valid.", 1);				

				$statement = $this->pdoobject->query($query);
				
				if(!$statement)
					throw new \Exception("MySQLPDOControllerError: trace method getSQLMatrix -> the statement is null, thus the query is wrond wrong", 1);
				else {
					$filteredmatrix = array();

					foreach($statement as $row)
						$filteredmatrix[] = $row;
					$this->destroyPDOObject();

					return $filteredmatrix;
				}
			}
			catch(\Exception $ex){
				if($this->app["monolog"]
					&& !$this->app["debug"])
					$this->app["monolog"]->addError($ex->getMessage());
				return false;
			}
		}

		/*
			@{Self}
			return an PDOStatement class instance
		*/

		public function exec($query){
			try{
				if(!is_string($query))
					throw new \Exception("MySQLPDOControllerError: trace method exec -> the parameter query need to be a string", 1);
				if(!$this->buildPDOObject())
					throw new \Exception("MySQLPDOControllerError: trace method exec -> the internal pdo object can't be build", 1);
				$st = $this->pdoobject->exec($query);
				$this->destroyPDOObject();

				return $st;
			}
			catch(\Exception $ex){
				if($this->app["monolog"]
					&& !$this->app["debug"])
					$this->app["monolog"]->addError($ex->getMessage());

				return false;
			}
		}

		/*
			@{Self}
			returns one filtered matrix
			filter = array(
				0 => space name
				1 => other name 
				.
				.
				n => n name	
			);
		*/

		public function filterSQLMatrix($matrix, $filter){
			try{
				if(is_array($matrix)
					&& is_array($filter)){
					$reducedmatrix = array();

					foreach($matrix as $array){
						$factorizedarray = array(); 

						for($w = 0; $w < count($filter); $w++)
							$factorizedarray[$filter[$w]] = $array[$filter[$w]];

						$reducedmatrix[] = $factorizedarray;
					} 
					return $reducedmatrix;
				}

				throw new \Exception("MySQLPDOControllerError: trace method filterSQLMatrix -> parameters are wrong", 1);
				
			}
			catch(\Exception $ex){
				if($this->app["monolog"]
					&& !$this->app["debug"])
					$this->app["monolog"]->addError($ex->getMessage());
				return false;	
			}	
		}
	}
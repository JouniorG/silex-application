<?php
	namespace WaveLearning\Entities;

	class User {
		private $id, $email, $password;

		public function __construct($id, $email, $password){
			$this->id = $id;
			$this->email = $email;
			$this->password = $password;
		}

		public function setID($id){
			$this->id = $id;
		}

		public function getID(){
			return $this->id;
		}

		public function setEmail($email){
			$this->email = $email;
		}

		public function getEmail(){
			return $this->email;
		}

		public function setPassword($password){
			$this->password = $password;
		}

		public function getPassword(){
			return $this->password;
		}
	}

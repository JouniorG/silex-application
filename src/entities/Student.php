<?php
	namespace WaveLearning\Entities;
	use WaveLearning\Entities\User;
	use WaveLearning\Entities\LoginInformation;

	class Student extends User{
		private $logininformation;

		public function __construct($id, $email, $password){
			parent::__construct($id, $email, $password);
			$this->logininformation = new LoginInformation(
				@$_SERVER["REMOTE_ADDR"],
				@$_SERVER["HTTP_USER_AGENT"]
			);
		}

		public function getLoginInformation(){
			return $this->logininformation->toString();
		}

		public function toArray(){
			return array(
				"id" => parent::getID(),
				"email" => parent::getEmail(),
				"pass" => parent::getPassword(),
				"login-information" => $this->getLoginInformation()
			);
		}
	}
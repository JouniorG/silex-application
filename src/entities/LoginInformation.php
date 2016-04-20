<?php
	namespace WaveLearning\Entities;

	class LoginInformation{
		private $ipv4, $browser;

		public function __construct($ipv4, $browser){
			$this->ipv4 = $ipv4;
			$this->browser = $browser;
		}

		public function toString(){
			return sprintf("User IPv4 [%s], HTTP Client [%s]", $this->ipv4, $this->browser);
		} 
	}
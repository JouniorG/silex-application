<?php
	namespace WaveLearning\Entities;

	class RegexRules{
		private $regex, $assert, $logininformation;

		public function __construct($regexrules, $assertrules){
			$this->regex = $regexrules;
			$this->assert = $assertrules;
		}

		public function addRegexRule($name, $regex){
			$this->regex[] = array($name => $regex);
		}

		public function getRegexRule($name){
			return $this->regex[$name];
		}

		public function addAssertRule($name, $regex){
			$this->assert[] = array($name => $regex);
		}

		public function getAssertRule($name){
			return $this->assert[$name];
		}

		public function setLoginInformation($li){
			$this->logininformation = $li;
		}

		public function getLoginInformation(){
			return $this->logininformation;
		}
	}
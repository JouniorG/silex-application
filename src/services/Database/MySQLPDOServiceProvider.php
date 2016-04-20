<?php 
	namespace WaveLearning\Services\Database;

	use Silex\Application;
	use Silex\ServiceProviderInterface;
	use WaveLearning\Controllers\Database\MySQLPDOController;

	class MySQLPDOServiceProvider implements ServiceProviderInterface {
		/*
			@{Overrided}
		*/
		public function register(Application $app){
			$app["pdo.mysql"] = $app->share(function() use($app){
				$pdocontroller = new MySQLPDOController(
					$app["dbuser"],
					$app["dbpass"],
					$app["dbhost"],
					$app["db"],
					$app["ac"], 
					$app
				);
				$pdocontroller->buildPDOObject();
				return $pdocontroller;
			});
		}

		/*
			@{Overrided}
		*/
		public function boot(Application $app){}
	}
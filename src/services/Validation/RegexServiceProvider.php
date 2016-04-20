<?php
	namespace WaveLearning\Services\Validation;

	use Silex\Application;
	use Silex\ServiceProviderInterface;
	use WaveLearning\Entities\RegexRules;

	class RegexServiceProvider implements ServiceProviderInterface {
		/*
			@{Override}
		*/
		public function register(Application $app){
			$app["regex.rules"] = $app->share(function() use($app){
				return new RegexRules($app["regexrules"], $app["assertrules"]);
			});
		}

		/*
			@{Override}
		*/
		public function boot(Application $app){}
	}
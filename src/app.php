<?php
	use Silex\Application;
	use Silex\Provider\TwigServiceProvider;
	use Silex\Provider\UrlGeneratorServiceProvider;
	use Silex\Provider\MonologServiceProvider;
	use Silex\Provider\SessionServiceProvider;
	use WaveLearning\Services\Database\MySQLPDOServiceProvider;
	use WaveLearning\Services\Validation\RegexServiceProvider;

	class MyApplicationClass extends Application{
		use Application\TwigTrait;
		use Application\UrlGeneratorTrait;
		use Application\MonologTrait;

		public function __construct(array $values = array()){
			parent::__construct($values);

			//deploy application
			$this["debug"]=true;
			$this["production"]=false;
			$this["local"]=false;
			$this["https"]=false;
			$this["asset.url"]=false;
			$this["prefix-production.host"]="://silexskeleton.herokuapp.com/";
			$this["prefix.host"]="://127.0.0.1/wavelearning/";
			$this["host"] = ($this["production"])?$this["prefix-production.host"]:$this["prefix.host"];
			$this["host"] = ($this["https"])?"https".$this["host"]:"http".$this["host"];

			//register framework service providers
			$this->register(new UrlGeneratorServiceProvider());
			$this->register(new SessionServiceProvider());
			$this->register(new TwigServiceProvider(), array(
				"twig.path" => __DIR__."/../views"
			));

			$this["twig"] = $this->extend("twig", function($twig, $app){
				$twig->addFunction(new \Twig_SimpleFunction("asset", function($asset) use($app){
					return $app["request_stack"]->getMasterRequest()->getBasePath()."/".ltrim($asset, "/");
 				}));
				return $twig;
			}); 

			$this->register(new MySQLPDOServiceProvider(), 
				array(
					"dbuser" => "root",
					"dbpass" => "yourpass",
					"dbhost" => "localhost",
					"db" => "wavelearningdb",
					"ac" => array(
						\PDO::FETCH_ASSOC => true
					)
			));

			$this->register(new RegexServiceProvider(),
				array(
					"regexrules" => array(
						"email" => "/^[a-zA-Z0-9_-]+@[a-zA-Z\.0-9_-]+$/",
						"name" => "/^[a-zA-Z0-9á-úÁ-Ú,\.:_ ]+$/",
						"hash" => "/^[a-f0-9]{32}$/",
						"number" => "/\d+/",
						"date" => "/^\d{2}\/\d{2}\/\d{4}$/",
						"datestandar" => "/^\d{4}-\d{2}-\d{2}$/",
						"website" => "/^(http|https):\/\/[a-zA-Z0-9_-]+\.[a-zA-Z\.\/0-9_-]+$/",
						"keyword" => "/^[a-zA-Z0-9á-úÁ-Ú,\.:_\- ]+$/",
						"uname" => "/^[a-zA-Zá-úÁ-Ú ]+$/"
					),
					"assertrules" => array(
						"email" => "^[a-zA-Z0-9_-]+@[a-zA-Z\.0-9_-]+$",
						"name" => "^[a-zA-Z0-9á-úÁ-Ú,\.:_ ]+$",
						"hash" => "^[a-f0-9]{32}$",
						"number" => "\d+",
						"date" => "^\d{2}\/\d{2}\/\d{4}$",
						"website" => "^(http|https):\/\/[a-zA-Z0-9_-]+\.[a-zA-Z\.\/0-9_-]+$",
						"keyword" => "^[a-zA-Z0-9á-úÁ-Ú,\.:_\- ]+$"
					)
			));
		}
	}

	return new MyApplicationClass();

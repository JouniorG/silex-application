<?php
	namespace WaveLearning\Controllers\Page;

	use Silex\Application;
	use Silex\ControllerProviderInterface;

	class PageControllers implements ControllerProviderInterface {
		/*
			@{Override}
		*/
		public function connect(Application $app){
			$controllers = $app["controllers_factory"];

			$controllers->get("/",function() use($app){
				return $app["twig"]->render(
					"index.html.twig",
					array("isauth" => $app["session"]->get("isAuth"))
				);
			})
				->bind("index");

			$controllers->get("/login", function() use($app){
				if($app["session"]->get("isAuth"))
					return $app->redirect("cpanel");
				return $app["twig"]->render("login.html.twig");
			})	
				->bind("login");

			$controllers->get("/signup", function() use($app){
				if($app["session"]->get("isAuth"))
					return $app->redirect("cpanel");
				return $app["twig"]->render("signup.html.twig");
			})
				->bind("signup");

			$controllers->get("/cpanel", function() use($app){
				if($app["session"]->get("isAuth"))
					return $app["twig"]->render("cpanel.html.twig", 
						array(
							"usertype" => $app["session"]->get("usertype")
					));
				return $app->redirect("login");
			})
				->bind("cpanel");

			$controllers->get("/logout", function() use($app){
					$app["session"]->set("isAuth", false);
					$app["session"]->get("user.object", null);
				return $app->redirect($app->path("index"));
			})
				->bind("logout");

			$controllers->get("/cpanel/courses", function() use($app){
				if($app["session"]->get("isAuth")){
					$result = $app["pdo.mysql"]->getSQLMatrix("select * from class");
					if($result)
						$result = $app["pdo.mysql"]->filterSQLMatrix($result, array("id","name","token","description"));
					
					if($app["session"]->get("usertype") == 1)
						return $app["twig"]->render(	
							"cpanel-courses.html.twig",
							array(
								"usertype" => $app["session"]->get("usertype"),
								"courses" => $result
						));

					return $app["twig"]->render(	
						"cpanel-courses-student.html.twig",
						array(
							"usertype" => $app["session"]->get("usertype"),
							"courses" => $result
					));
				}
				else if($app["session"]->get("usertype") == 0)
					return $app->redirect($app->path("cpanel"));
				return $app->redirect($app->path("login"));
			})
				->bind("cpanel.courses");

			$controllers->get("/cpanel/activities", function() use($app){
				if($app["session"]->get("isAuth")
					&& $app["session"]->get("usertype") == 1){
					$result = $app["pdo.mysql"]->getSQLMatrix("select * from class");

					if($result)
						$result = $app["pdo.mysql"]->filterSQLMatrix($result, array("id","name","token","description"));

					return $app["twig"]->render(	
						"cpanel-activities.html.twig",
						array(
							"usertype" => $app["session"]->get("usertype"),
							"courses" => $result
					));
				}
				else if($app["session"]->get("usertype") == 0)
					return $app->redirect($app->path("cpanel"));
				return $app->redirect($app->path("login"));
			})
				->bind("cpanel.activities");

			$controllers->get("/cpanel/students", function() use($app){
				if($app["session"]->get("isAuth")
					&& $app["session"]->get("usertype") == 1){
					$result = $app["pdo.mysql"]->getSQLMatrix("select * from users where usertype=0");
					if($result)
						$result = $app["pdo.mysql"]->filterSQLMatrix($result, array("id","email","password","about"));

					return $app["twig"]->render(	
						"cpanel-students.html.twig",
						array(
							"usertype" => $app["session"]->get("usertype"),
							"students" => $result
					));
				}
				else if($app["session"]->get("usertype") == 0)
					return $app->redirect($app->path("cpanel"));
				return $app->redirect($app->path("login"));
			})
				->bind("cpanel.students");

			$controllers->get("/cpanel/profile", function() use($app){
				if($app["session"]->get("isAuth"))
					return $app["twig"]->render(
						"cpanel-profile.html.twig",
						array(
							"usertype" => $app["session"]->get("usertype"),
							"userinfo" => $app["session"]->get("user.object")->toArray(),
							"userabout" => $app["session"]->get("userabout")
					));
				return $app->redirect($app->path("login"));
			})
				->bind("cpanel.profile");

			$controllers->get("/courses", function() use($app){
				$result = $app["pdo.mysql"]->getSQLMatrix("select name from class");
				$result = $app["pdo.mysql"]->filterSQLMatrix($result, array("name"));

				return $app["twig"]->render(
					"courses.html.twig",
					array(
						"isauth" => $app["session"]->get("isAuth"),
						"courses" => $result
				));
			})	
				->bind("courses");

			return $controllers;
		}
	}
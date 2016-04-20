<?php
	namespace WaveLearning\Rest\Controllers;

	use Silex\Application;
	use Silex\ControllerProviderInterface;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\HttpFoundation\JsonResponse;
	use Symfony\Component\HttpFoundation\RedirectResponse;
	use WaveLearning\Entities\Teacher;
	use WaveLearning\Entities\Student;

	class PanelEventControllers implements ControllerProviderInterface {
		/*
			@{Override}
		*/
		public function connect(Application $app){
			$controllers = $app["controllers_factory"];
			
			$controllers->post("/login", function(Request $req) use($app){
				$email = $req->get("email");
				$password = md5($req->get("password"));

				$queryresult = $app["pdo.mysql"]->getSQLMatrix("select * from users where email='$email' and password='$password'");

				if($queryresult){
					$queryresult = $app["pdo.mysql"]->filterSQLMatrix($queryresult, array("id", "usertype", "email", "password", "about"));
					$queryresult = $queryresult[0];
					
					$app["session"]->set(
						"user.object",
						($queryresult["usertype"]==1) ? new Teacher($queryresult["id"], $queryresult["email"], $queryresult["password"]) : new Student($queryresult["id"], $queryresult["email"], $queryresult["password"])
					);
					$app["session"]->set("isAuth", true);
					$app["session"]->set("usertype", $queryresult["usertype"]);
					$app["session"]->set("userabout", $queryresult["about"]);
					
					return new JsonResponse(array("acepted" => "login"), 200);
				}

				return new JsonResponse(array("error" => "login"), 400);	
			})
				->before(function(Request $req) use($app){
					$email = $req->get("email");

					if(!preg_match($app["regex.rules"]->getRegexRule("email"), $email))
						return new JsonResponse(array("error"=>"login"), 403);
				})
				->bind("rest.login");

			
			$controllers->get("/logout", function() use($app){
					$app["session"]->set("isAuth", false);
					$app["session"]->get("user.object", null);
				return $app->redirect($app->path("login"));
			})
				->bind("logout");

			$controllers->post("/signup", function(Request $req) use($app){
				$email = $req->get("email");
				$result = $app["pdo.mysql"]->getSQLMatrix("select id from users where email='$email'");

				if(!$result){
					$pass = $req->get("password-ver");
					$app["pdo.mysql"]->exec("insert into users(usertype,email,password) values(0,'$email', '$pass')");

					return new JsonResponse(array("acepted"=>"signup"), 201);
				}

				return new JsonResponse(array("error"=>"signup"), 400); 
			})
				->before(function(Request $req) use($app){
					$email = $req->get("email");
					$pass = $req->get("password");
					$passver = $req->get("password-ver");

					if($pass !== $passver
						|| !preg_match($app["regex.rules"]->getRegexRule("email"), $email))
						return new JsonResponse(array("error"=>"signup"), 403);
				})
				->bind("rest.signup");


			$controllers->post("/add-student", function(Request $req) use($app){
				$email = $req->get("email");
				$result = $app["pdo.mysql"]->getSQLMatrix("select id from users where email='$email'");

				if(!$result){
					$pass = $req->get("password-ver");
					$about = $req->get("desc");
					$app["pdo.mysql"]->exec("insert into users(usertype,email,password, about) values(0,'$email', '$pass', '$about')");

					return new JsonResponse(array("acepted"=> "Action [add student], msg [the student was added]"), 201);
				}

				return new JsonResponse(array("error"=>"Action [add student], msg [the email exists]"), 400); 
			})
				->before(function(Request $req) use($app){
					$email = $req->get("email");
					$pass = $req->get("password");
					$passver = $req->get("password-ver");

					if($pass !== $passver
						|| !preg_match($app["regex.rules"]->getRegexRule("email"), $email))
						return new JsonResponse(array("error"=>"[action], msg [invalid request]"), 403);
				})
				->bind("rest.add-student");


			$controllers->get("/students", function() use($app){
				$result = $app["pdo.mysql"]->getSQLMatrix("select id, email, password from users where usertype=0");

				if($result){
					$result = $app["pdo.mysql"]->filter($result, array("id", "email", "password"));
					return $app->json($result);
				}

				return $app->abort(404);
			});

			$controllers->get("/courses", function() use($app){
				$result = $app["pdo.mysql"]->getSQLMatrix("select id, name, description from class");

				if($result){
					$result = $app["pdo.mysql"]->filterSQLMatrix($result, array("id", "name", "description"));
					return $app->json($result);
				}

				return $app->abort(404);
			});

			$controllers->post("/add-course", function(Request $req) use($app){
				$name = $req->get("name");
				$desc = $req->get("desc");

				$result = $app["pdo.mysql"]->exec("insert into class(name, description) values('$name','$desc')");
				if($result)
					return new JsonResponse(array("acepted" => "Action [add course], msg [course created]"), 201);

				return new JsonResponse(array("error" => "Action [add course], msg [course not created]"), 401);
			})
				->before(function(Request $req) use($app){
					if(!preg_match($app["regex.rules"]->getRegexRule("name"), $req->get("name")))
						return new JsonResponse(array("error" => "Action [add course], msg [invalid fields]"), 403);
				});

			return $controllers;
		}
	}